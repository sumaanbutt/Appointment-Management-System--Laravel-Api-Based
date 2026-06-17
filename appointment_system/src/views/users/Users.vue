<template>
  <div class="ams-page">

    <!-- HEADER -->
    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="mb-0">Users</h2>
        <p class="text-muted small mb-0">Manage system users</p>
      </div>
      <router-link to="/admin/users/create" class="btn btn-ams">+ New User</router-link>
    </div>

    <!-- TABLE CARD -->
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-3">User Name</th>
              <th>Business Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Role</th>
              <th>Status</th>
              <th class="pe-3" style="width:160px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users" :key="user.code">
              <td class="ps-3">{{ user.name }}</td>
              <td>{{user.business?.name || '—' }}</td>
              <td>{{ user.email }}</td>
              <td>{{user.phone}}</td>
              <td><span class="badge bg-white text-dark border px-2 py-1.5 fw-medium small text-lowercase">{{ user.user_type || '—' }}</span></td>
              <!--              <td><code>{{ user.code }}</code></td>-->
              <td><span :class="['badge', user.status=== 'ACTIVE' ? 'bg-success' : 'bg-secondary']">{{ user.status === 'ACTIVE' ? 'Active' : 'Inactive' }}</span></td>
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
                      <button
                          class="dropdown-item"
                          @click="openEdit(user)">

                        <i class="bi bi-pencil me-2"></i>
                        Edit

                      </button>
                    </li>

                    <li>
                      <button
                          class="dropdown-item text-danger"
                          @click="openDelete(user)">

                        <i class="bi bi-person-x me-2"></i>
                        Deactivate

                      </button>
                    </li>

                  </ul>

                </div>

              </td>
            </tr>
            <tr v-if="users.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No users found</td>
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
            <h5 class="modal-title">Edit User</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateUser">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Full Name *</label>
                <input v-model="editForm.name" class="form-control" placeholder="Full Name" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Email *</label>
                <input v-model="editForm.email" type="email" class="form-control" placeholder="Email" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Phone *</label>
                <input v-model="editForm.phone" type="text" class="form-control" placeholder="Phone" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">
                  New Password
                </label>

                <input
                    v-model="editForm.password"
                    type="password"
                    class="form-control"
                    placeholder="Leave blank to keep current password"
                />

                <small class="text-muted">
                  Leave empty if you don't want to change the password.
                </small>
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
            <h5 class="modal-title">Deactivate User</h5>
            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-0">Deactivate <strong>{{ selected?.name }}</strong>?</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-secondary btn-sm" @click="showDeleteModal = false">Cancel</button>
            <button class="btn btn-danger btn-sm" @click="deactivateUser" :disabled="saving">{{ saving ? '...' : 'Deactivate' }}</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap-icons/font/bootstrap-icons.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import {apiHandler} from "@/services/api/apiHandler.ts";

const users = ref([])
const currentPage = ref(1)
const lastPage = ref(1)
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')

const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selected = ref(null)

const editForm = reactive({
  name:'',
  email:'',
  phone:'',
  password:'',
  status:'ACTIVE'
})

async function fetchUsers() {
  loading.value = true
  error.value = ''
  try {
    const res = await apiHandler('user', 'getAllUsers', {
      params: {
        page: currentPage.value
      }
    })

    // console.log('USERS RESPONSE:', res.data)
    // console.log('FIRST USER:', res.data.data.data[0])
    // console.log('IS_ACTIVE:', res.data.data.data[0].is_active)
    // console.log('STATUS:', res.data.data.data[0].status)

    users.value = res.data.data.data || []
    currentPage.value = res.data.data.current_page
    lastPage.value = res.data.data.last_page
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load users'
  } finally {
    loading.value = false
  }
}

async function changePage(page) {
  if (page < 1 || page > lastPage.value) return

  currentPage.value = page
  await fetchUsers()
}

function openEdit(user) {
  selected.value = user
  editForm.name = user.name
  editForm.email = user.email
  editForm.phone = user.phone
  editForm.password = ''
  editForm.status = user.status
  formError.value = ''
  showEditModal.value = true
}

function openDelete(user) {
  selected.value = user
  showDeleteModal.value = true
}

async function updateUser() {
  saving.value = true
  formError.value = ''
  try {
    const payload = {
      name: editForm.name,
      email: editForm.email,
      phone: editForm.phone,
      status: editForm.status
    }

    if (editForm.password?.trim()) {
      payload.password = editForm.password
    }

    await apiHandler(
        'user',
        'updateUser',
        {
          pathParams: {
            code: selected.value.code
          },
          body: {
            ...payload
          }
        })

    showEditModal.value = false
    await fetchUsers()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Update failed'
  } finally {
    saving.value = false
  }
}
// deactivate user
async function deactivateUser() {
  saving.value = true
  try {
    await apiHandler(
        'user',
        'deactivateUser',
        {
          pathParams: {
            code: selected.value.code
          },
          body: {
            status: 'INACTIVE'
          }
        })

    showDeleteModal.value = false
    await fetchUsers()
  } catch (err) {
    error.value = err.response?.data?.message || 'Deactivation failed'
  } finally {
    saving.value = false
  }
}

// async function deleteUser() {
//   saving.value = true
//   try {
//     await api.delete(`/users/delete-user${selected.value.user_code}`)
//     showDeleteModal.value = false
//     await fetchUsers()
//   } catch (err) {
//     error.value = err.response?.data?.message || 'Failed to deactivate user'
//   } finally {
//     saving.value = false
//   }
// }

onMounted(fetchUsers)
</script>


<style scoped>

.ams-page{
  display:flex;
  flex-direction:column;
  gap:20px;
}

.card{
  border-radius:12px;
}

.ams-table th,
.ams-table td{
  vertical-align:middle;
  font-size:14px;
}

code{
  background:#f1f5f9;
  padding:3px 8px;
  border-radius:6px;
  color:#334155;
}

.btn-ams{
  background:#6366f1;
  color:#fff;
  border:none;
}

.btn-ams:hover{
  background:#4f46e5;
  color:#fff;
}

.form-control,
.form-select{
  border-radius:8px;
  font-size:14px;
}

.form-control:focus,
.form-select:focus{
  border-color:#6366f1;
  box-shadow:0 0 0 0.15rem rgba(99,102,241,.15);
}

</style>