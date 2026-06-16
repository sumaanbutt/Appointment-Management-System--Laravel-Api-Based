<template>
  <div class="ams-page">
    <div class="d-flex align-items-center justify-content-between">
      <div><h2 class="mb-0">Services</h2><p class="text-muted small mb-0">Manage your business services</p></div>
      <router-link to="/owner/services/create" class="btn btn-ams">+ New Service</router-link>
    </div>
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
            <tr><th class="ps-3">Name</th><th>Code</th><th>Description</th><th>Price</th><th>Status</th><th class="pe-3" style="width:140px">Actions</th></tr>
          </thead>
          <tbody>
            <tr v-for="svc in services" :key="svc.code">
              <td class="ps-3">{{ svc.service_name }}</td>
              <td><code>{{ svc.code }}</code></td>
              <td>{{ svc.description }}</td>
              <td>{{ svc.cost != null ? svc.cost : '—'}}</td>
              <td><span :class="['badge',svc.status==='active'?'bg-success':'bg-secondary']">{{ svc.status }}</span></td>
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
                          @click="openEdit(svc)">

                        <i class="bi bi-pencil me-2"></i>
                        Edit

                      </button>
                    </li>

                    <li>
                      <button
                          class="dropdown-item text-danger"
                          @click="openDelete(svc)">

                        <i class="bi bi-trash me-2"></i>
                        Delete

                      </button>
                    </li>

                  </ul>

                </div>

              </td>
            </tr>
            <tr v-if="services.length === 0"><td colspan="6" class="text-center text-muted py-4">No services found</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Service</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateService">
            <div class="modal-body">

              <div class="mb-3">
                <label class="form-label fw-semibold">
                  Service Name *
                </label>

                <input
                    v-model="editForm.service_name"
                    class="form-control"
                    required
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">
                  Duration
                </label>

                <input
                    v-model.number="editForm.time_duration"
                    type="number"
                    min="1"
                    class="form-control"
                />
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">
                  Price
                </label>

                <input
                    v-model.number="editForm.charges"
                    type="number"
                    step="0.01"
                    min="0"
                    class="form-control"
                />
              </div>

              <div class="mb-3">

                <label class="form-label fw-semibold">
                  Status
                </label>

                <select
                    v-model="editForm.status"
                    class="form-select"
                >

                  <option value="active">
                    Active
                  </option>

                  <option value="inactive">
                    Inactive
                  </option>

                </select>

              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
              <button type="submit" class="btn btn-ams" :disabled="saving">{{ saving ? 'Saving...' : 'Save' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- DELETE MODAL -->
    <div v-if="showDeleteModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Service</h5>
            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-0">Delete <strong>{{ selected?.name }}</strong>?</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-secondary btn-sm" @click="showDeleteModal = false">Cancel</button>
            <button class="btn btn-danger btn-sm" @click="deleteService" :disabled="saving">{{ saving ? 'Deleting...' : 'Delete' }}</button>
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
const services = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selected = ref(null)

const createForm = reactive({
  service_name:'',
  description:'',
  time_duration:'',
  charges:'',
  status:'active',
  duration_uom:'',
})

const editForm = reactive({
  service_name:'',
  description:'',
  time_duration:'',
  charges:'',
  status:'active',
  duration_uom:'',
})

async function fetchServices() {
  loading.value = true
  error.value = ''
  try {
    const biz = authStore.user?.business_code
    const res = await api.get('/services', { params: biz ? { business_code: biz } : {} })
    services.value = res.data.data.data || []
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load services'
  } finally {
    loading.value = false
  }
}

function openEdit(svc){

  selected.value = svc

  editForm.service_name = svc.service_name
  editForm.description = svc.description
  editForm.time_duration = svc.time_duration
  editForm.charges = svc.charges
  editForm.status = svc.status
  editForm.duration_uom = svc.duration_uom

  showEditModal.value = true
}

function openDelete(svc) { selected.value = svc; showDeleteModal.value = true }

async function createService() {
  saving.value = true
  formError.value = ''
  try {
    const biz = authStore.user?.business_code
    const payload = { ...createForm, business_code: biz }
    if (!payload.price) delete payload.price
    if (!payload.description) delete payload.description
    await api.post('/services', payload)
    showCreateModal.value = false
    Object.assign(createForm, { name: '', duration_minutes: '', price: '', description: '' })
    await fetchServices()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Create failed'
  } finally {
    saving.value = false
  }
}

async function updateService() {
  saving.value = true
  formError.value = ''
  try {
    await api.put(
        `/services/${selected.value.code}`,
        {
          ...editForm,
          business_code:
          authStore.user?.business_code
        }
    )
    showEditModal.value = false
    await fetchServices()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Update failed'
  } finally {
    saving.value = false
  }
}

async function deleteService() {
  saving.value = true
  try {
    await api.delete(`/services/${selected.value.code}`)
    showDeleteModal.value = false
    await fetchServices()
  } catch (err) {
    error.value = err.response?.data?.message || 'Delete failed'
  } finally {
    saving.value = false
  }
}

onMounted(fetchServices)
</script>

<style scoped>
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select, .field textarea { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; font-family: inherit; }
.save-btn { background: #0f172a; color: white; border: none; padding: 10px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }

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
