<template>
  <div class="ams-page">
    <div class="d-flex align-items-center justify-content-between">
      <div><h2 class="mb-0">Staff</h2><p class="text-muted small mb-0">Manage your business staff</p></div>
      <router-link to="/owner/staff/create" class="btn btn-ams">+ Add Staff</router-link>
    </div>
    <div class="d-flex gap-2">
      <select v-model="typeFilter" @change="fetchStaff" class="form-select" style="max-width:220px">
        <option value="">All Types</option>
        <option value="OPERATION_STAFF">Operational Staff</option>
        <option value="SERVICE_STAFF">Service Staff</option>
      </select>
    </div>
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
          <tr>
            <th class="ps-3">Name</th>
            <th>Email</th>
            <th>Role</th>
            <!--              <th>Code</th>-->
            <th>Status</th>
            <th class="pe-3" style="width:220px">Actions</th></tr>
          </thead>
          <tbody>
          <tr v-for="user in staff" :key="user.code">
            <td class="ps-3">{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td><span class="badge bg-white text-dark border px-2 py-1.5 fw-medium small text-lowercase">{{ user?.user_type || '-' }}</span></td>

            <!--              <td><code>{{ user.user_code }}</code></td>-->
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
                        class="dropdown-item text-warning"
                        @click="openDeactivate(user)">

                      <i class="bi bi-person-x me-2"></i>
                      Deactivate

                    </button>
                  </li>

                </ul>

              </div>

            </td>
          </tr>
          <tr v-if="staff.length === 0"><td colspan="6" class="text-center text-muted py-4">No staff found</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Staff</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateStaff">
            <div class="modal-body">
              <div class="mb-3"><label class="form-label fw-semibold">Full Name *</label><input v-model="editForm.name" class="form-control" required /></div>
              <div class="mb-3"><label class="form-label fw-semibold">Phone</label><input v-model="editForm.phone" class="form-control" /></div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Employee Type</label>
                <select v-model="editForm.employee_type" class="form-select">
                  <option value="PERMANENT">Permanent</option>
                  <option value="VISITING">Visiting</option>
                  <option value="REMOTE">Remote</option>
                </select>
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
              <button type="submit" class="btn btn-ams" :disabled="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- DEACTIVATE MODAL -->
    <div v-if="showDeactivateModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Deactivate Staff</h5>
            <button type="button" class="btn-close" @click="showDeactivateModal = false"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-0">Deactivate <strong>{{ selected?.name }}</strong>?</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-secondary btn-sm" @click="showDeactivateModal = false">Cancel</button>
            <button class="btn btn-warning btn-sm" @click="deactivateStaff" :disabled="saving">{{ saving ? '...' : 'Deactivate' }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

const authStore = useAuthStore()
const staff = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')
const typeFilter = ref('')

const showEditModal = ref(false)
const showDeactivateModal = ref(false)
const selected = ref(null)
const editForm = reactive({ name: '', phone: '', employee_type: "", status: 'ACTIVE'})

async function fetchStaff() {
  loading.value = true
  error.value = ''
  try {
    const biz = authStore.user?.business_code
    const params = {}
    if (biz) params.business_code = biz
    if (typeFilter.value) params.user_type = typeFilter.value
    const res = await api.get('/users', { params })

    const users = res.data.data.data || []

    staff.value = users.filter(u => {
      const isStaff = ['OPERATION_STAFF', 'SERVICE_STAFF'].includes(u.user_type)

      if (!typeFilter.value) {
        return isStaff
      }

      return isStaff && u.user_type === typeFilter.value
    })
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load staff'
  } finally {
    loading.value = false
  }
}

function openEdit(user) {
  selected.value = user
  editForm.name = user.name
  editForm.phone = user.phone || ''
  editForm.status = user.status
  formError.value = ''
  showEditModal.value = true
}

function openDeactivate(user) { selected.value = user; showDeactivateModal.value = true }

async function updateStaff() {
  saving.value = true
  formError.value = ''
  try {
    await api.put(`/users/${selected.value.code}`, editForm)
    showEditModal.value = false
    await fetchStaff()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Update failed'
  } finally {
    saving.value = false
  }
}

async function deactivateStaff() {
  saving.value = true
  try {
    await api.put(
        `/users/${selected.value.code}`,
        { status:'INACTIVE' }
    )
    showDeactivateModal.value = false
    await fetchStaff()
  } catch(err){
    error.value =
        err.response?.data?.message
        || 'Deactivate failed'
  } finally {
    saving.value = false
  }
}

onMounted(fetchStaff)
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