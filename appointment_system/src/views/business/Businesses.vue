<template>
  <div class="ams-page">

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="mb-0">Businesses</h2>
        <p class="text-muted small mb-0">Manage all businesses</p>
      </div>
      <router-link to="/admin/businesses/create" class="btn btn-ams">+ New Business</router-link>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-3">Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Organization Name</th>
              <th>Time Zone</th>
              <th>Status</th>
              <th class="pe-3" style="width:230px">Actions</th>
            </tr>
          </thead>
          <tbody>
          <tr v-for="business in businesses" :key="business.code">

            <td class="ps-3">{{ business.name }}</td>
            <td>{{ business?.email }}</td>
            <td>{{ business?.phone }}</td>
            <td>{{ business.organization.name || business.organization_code || '—' }}</td>
            <td>{{ business?.timezone || '—' }}</td>
            <td><span :class="['badge',
              business.status === 'ACTIVE' ? 'bg-success' :
              business.status === 'INACTIVE' ? 'bg-secondary':
              'bg-light text-dark'
              ]">{{ business.status.replaceAll('_', ' ').toLowerCase()  }}</span></td>

            <td class="pe-3">

              <div class="dropdown">

                <button
                    class="btn btn-sm btn-outline-secondary"
                    type="button"
                    data-bs-toggle="dropdown">

                  <i class="bi bi-three-dots-vertical"></i>

                </button>

                <ul class="dropdown-menu dropdown-menu-end">

                  <li>
                    <router-link
                        :to="`/businesses/${business.code}`"
                        class="dropdown-item">

                      <i class="bi bi-eye me-2"></i>
                      View

                    </router-link>
                  </li>

                  <li>
                    <button
                        class="dropdown-item"
                        @click="openEdit(business)">

                      <i class="bi bi-pencil me-2"></i>
                      Edit

                    </button>
                  </li>

                  <li>
                    <button
                        class="dropdown-item text-danger"
                        @click="openDelete(business)">

                      <i class="bi bi-trash me-2"></i>
                      Deactivate

                    </button>
                  </li>

                </ul>

              </div>

            </td>

          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <nav class="mt-3">
      <ul class="pagination justify-content-end">

        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)">
            Previous
          </button>
        </li>

        <li
            v-for="page in lastPage"
            :key="page"
            class="page-item"
            :class="{ active: currentPage === page }">

          <button
              class="page-link"
              @click="changePage(page)">

            {{ page }}

          </button>

        </li>

        <li class="page-item" :class="{ disabled: currentPage === lastPage }">
          <button class="page-link" @click="changePage(currentPage + 1)">
            Next
          </button>
        </li>

      </ul>
    </nav>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Business</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateBusiness">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Business Name *</label>
                <input v-model="editForm.name" class="form-control" placeholder="Business Name" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select v-model="editForm.status" class="form-select">
                  <option value="ACTIVE">Active</option>
                  <option value="INACTIVE">Inactive</option>
                </select>
              </div>
              <p v-if="formError" class="text-danger small mb-0">{{ formError }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
              <button type="submit" class="btn btn-ams" :disabled="saving">{{ saving ? 'Saving...' : 'Save Changes' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- DEACTIVATE CONFIRM MODAL -->
    <div v-if="showDeleteModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Deactivate Business</h5>
            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-0">Deactivate <strong>{{ selected?.name }}</strong>?</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-secondary btn-sm" @click="showDeleteModal = false">Cancel</button>
            <button class="btn btn-danger btn-sm" @click="deactivateBusiness" :disabled="saving">{{ saving ? '...' : 'Deactivate' }}</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

const businesses = ref([])
const currentPage = ref(1)
const lastPage = ref(1)
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')

const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selected = ref(null)

const editForm = reactive({ name: '', status: 'active' })

async function fetchBusinesses() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get('/businesses',{
      params: {
        page: currentPage.value
      }
    })
    businesses.value = res.data.data.data || []
    currentPage.value = res.data.data.current_page
    lastPage.value = res.data.data.last_page
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load businesses'
  } finally {
    loading.value = false
  }
}

async function changePage(page) {
  if (page < 1 || page > lastPage.value) return

  currentPage.value = page
  await fetchBusinesses()
}

function openEdit(business) {
  selected.value = business
  editForm.name = business.name
  editForm.status = business.status
  formError.value = ''
  showEditModal.value = true
}

function openDelete(business) {
  selected.value = business
  showDeleteModal.value = true
}

async function updateBusiness() {
  saving.value = true
  formError.value = ''

  try {
    await api.put(
        `/businesses/${selected.value.code}`,
        editForm
    )

    showEditModal.value = false
    await fetchBusinesses()

  } catch (err) {
    formError.value =
        err.response?.data?.message || 'Update failed'
  } finally {
    saving.value = false
  }
}

// async function updateBusiness() {
//   saving.value = true
//   formError.value = ''
//   try {
//     await api.put(`/businesses/update-business${selected.value.business_code}`, editForm)
//     showEditModal.value = false
//     await fetchBusinesses()
//   } catch (err) {
//     formError.value = err.response?.data?.message || 'Update failed'
//   } finally {
//     saving.value = false
//   }
// }

// Deactivate Business

async function deactivateBusiness() {
  saving.value = true

  try {
    await api.patch(
        `/businesses/${selected.value.code}`,
        { status: 'INACTIVE' }
    )

    showDeleteModal.value = false
    await fetchBusinesses()

  } catch (err) {
    error.value =
        err.response?.data?.message || 'Deactivation failed'
  } finally {
    saving.value = false
  }
}

// async function deleteBusiness() {
//   saving.value = true
//   try {
//     await api.delete(`/businesses/delete-business${selected.value.business_code}`)
//     showDeleteModal.value = false
//     await fetchBusinesses()
//   } catch (err) {
//     error.value = err.response?.data?.message || 'Delete failed'
//   } finally {
//     saving.value = false
//   }
// }

onMounted(fetchBusinesses)
</script>


