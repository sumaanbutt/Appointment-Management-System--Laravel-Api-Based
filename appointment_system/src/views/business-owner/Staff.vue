<!--<template>-->
<!--  <div class="container-fluid py-4">-->

<!--    <div class="d-flex align-items-center justify-content-between mb-4">-->
<!--      <div>-->
<!--        <h2 class="text-dark fw-bold mb-1">Staff Directory</h2>-->
<!--        <p class="text-muted small mb-0">Review active management profiles for operational and service delivery teams.</p>-->
<!--      </div>-->
<!--    </div>-->

<!--    <div class="card border-0 shadow-sm rounded-3">-->
<!--      <div class="card-body p-0">-->
<!--        <div class="table-responsive">-->
<!--          <table class="table table-hover align-middle mb-0">-->
<!--            <thead class="table-light text-secondary text-uppercase fs-7 small fw-bold">-->
<!--            <tr>-->
<!--              <th class="ps-4 py-3">Full Identity</th>-->
<!--              <th>Contact Communications</th>-->
<!--              <th>Privilege Group</th>-->
<!--              <th>Employment Type</th>-->
<!--              <th>Operational Status</th>-->
<!--              <th class="pe-4 text-end" style="width: 120px;">Actions</th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--            <tbody>-->
<!--            <tr v-if="filteredStaffList.length === 0">-->
<!--              <td colspan="6" class="text-center py-5 text-muted">-->
<!--                No operational or service staff members found.-->
<!--              </td>-->
<!--            </tr>-->
<!--            <tr v-for="user in filteredStaffList" :key="user.code">-->

<!--              <td class="ps-4">-->
<!--                <div class="fw-bold text-dark">{{ user.name }}</div>-->
<!--                <div class="text-secondary small font-monospace fs-7">#{{ user.code }}</div>-->
<!--              </td>-->

<!--              <td>-->
<!--                <div class="text-dark fs-6">{{ user.email || '—' }}</div>-->
<!--                <div class="text-muted small">{{ user.phone || '—' }}</div>-->
<!--              </td>-->

<!--              <td>-->
<!--                  <span class="badge bg-dark-subtle text-dark border px-2 py-1 small">-->
<!--                    {{ user.user_type }}-->
<!--                  </span>-->
<!--              </td>-->

<!--              <td>-->
<!--                  <span v-if="user.employee_type" class="badge bg-primary text-white border-0 px-2.5 py-1.5 font-monospace">-->
<!--                    {{ user.employee_type }}-->
<!--                  </span>-->
<!--                <span v-else class="text-muted opacity-50 fst-italic small ps-2">— N/A</span>-->
<!--              </td>-->

<!--              <td>-->
<!--                  <span :class="['badge px-2.5 py-1.5 rounded-pill font-monospace fw-bold',-->
<!--                    user.status === 'ACTIVE' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger']">-->
<!--                    {{ user.status || 'ACTIVE' }}-->
<!--                  </span>-->
<!--              </td>-->

<!--              <td class="pe-4 text-end">-->
<!--                <button class="btn btn-sm btn-outline-danger border-0" @click="handleDeleteUser(user.code)">-->
<!--                  <i class="bi bi-trash3"></i>-->
<!--                </button>-->
<!--              </td>-->

<!--            </tr>-->
<!--            </tbody>-->
<!--          </table>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

<!--  </div>-->
<!--</template>-->

<!--<script setup>-->
<!--import { ref, onMounted, computed } from 'vue'-->
<!--import { useAuthStore } from '@/stores/auth.store'-->
<!--import api from '@/services/api'-->

<!--const authStore = useAuthStore()-->
<!--const rawUsersList = ref([])-->

<!--// Frontend safety filter to strictly isolate operational and service roles-->
<!--const filteredStaffList = computed(() => {-->
<!--  return rawUsersList.value.filter(user =>-->
<!--      ['OPERATION_STAFF', 'SERVICE_STAFF'].includes(user.user_type)-->
<!--  )-->
<!--})-->

<!--onMounted(async () => {-->
<!--  await fetchSystemUsersDirectory()-->
<!--})-->

<!--async function fetchSystemUsersDirectory() {-->
<!--  const corporateBusinessCode = authStore.user?.business_code || ''-->
<!--  if (!corporateBusinessCode) return-->

<!--  try {-->
<!--    const response = await api.get('/users', { params: { business_code: corporateBusinessCode } })-->
<!--    rawUsersList.value = response.data.data?.data || response.data.data || []-->
<!--  } catch (err) {-->
<!--    console.error('Failed to query database user directory.', err)-->
<!--  }-->
<!--}-->

<!--async function handleDeleteUser(userCode) {-->
<!--  if (!confirm('Are you certain you want to purge this staff member?')) return-->
<!--  try {-->
<!--    await api.delete(`/users/${userCode}`)-->
<!--    await fetchSystemUsersDirectory()-->
<!--  } catch (err) {-->
<!--    alert('Error occurred clearing staff record.')-->
<!--  }-->
<!--}-->
<!--</script>-->

<!--<style scoped>-->
<!--.fs-7 { font-size: 0.765rem; }-->
<!--</style>-->


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
            <td>{{ user.user_type }}</td>
            <!--              <td><code>{{ user.user_code }}</code></td>-->
            <td><span :class="['ams-badge', user.status === 'ACTIVE' ? 'ACTIVE' : 'INACTIVE']">{{ user.status === 'ACTIVE' ? 'Active' : 'Inactive' }}</span></td>
            <td class="pe-3">
              <button class="btn btn-sm btn-outline-primary me-1" @click="openEdit(user)">Edit</button>
              <button class="btn btn-sm btn-outline-warning" @click="openDeactivate(user)">Deactivate</button>
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
                  <option value="permanent">Permanent</option>
                  <option value="visiting">Visiting</option>
                  <option value="remote">Remote</option>
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
    staff.value = (res.data.data.data || []).filter(u => ['OPERATION_STAFF','SERVICE_STAFF'].includes(u.user_type))
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