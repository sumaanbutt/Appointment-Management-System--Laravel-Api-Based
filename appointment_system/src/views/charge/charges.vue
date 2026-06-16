<template>
  <div class="ams-page">

    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="mb-0">Charges</h2>
        <p class="text-muted small mb-0">Manage all charges</p>
      </div>
      <button class="btn btn-ams" @click="showCreateModal = true">+ New Charge</button>
    </div>

    <div v-if="isAdmin" class="d-flex gap-2">
      <select v-model="bizFilter" @change="fetchCharges" class="form-select" style="max-width:240px">
        <option value="">All Businesses</option>
        <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>
      </select>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
          <tr>
            <th class="ps-3">Business Name</th>
            <th>Name</th>
            <th>Value</th>
            <th>Type</th>
            <th>Status</th>
            <th>Auto Applied Status</th>
            <th class="pe-3" style="width:100px">Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="charge in charges" :key="charge.code">
            <td class="ps-3">{{ charge.business?.name || '—' }}</td>
            <td>{{ charge.name }}</td>
            <td>{{ charge.charge_value }}</td>
            <td><span class="badge bg-white text-dark border px-2 py-1.5 fw-medium small text-lowercase">{{ charge.charge_uom || '-' }}</span></td>
            <!--            <td><span :class="['ams-badge', charge.status]">{{ charge.status }}</span></td>-->
            <td><span :class="['badge', charge.status=== 'ACTIVE' ? 'bg-success' : 'bg-secondary']">{{ charge.status === 'ACTIVE' ? 'Active' : 'Inactive' }}</span></td>
            <td><span :class="['ams-badge', charge.auto_apply]">{{ charge.auto_apply }}</span></td>
            <td class="pe-3">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <button class="dropdown-item" @click="openEdit(charge)">
                      <i class="bi bi-pencil me-2"></i>
                      Edit
                    </button>
                  </li>
                  <li>
                    <button class="dropdown-item text-danger" @click="openDeactivate(charge)">
                      <i class="bi bi-trash me-2"></i>
                      Deactivate
                    </button>
                  </li>
                  <li>
                    <button class="dropdown-item text-danger" @click="openDelete(charge)">
                      <i class="bi bi-trash me-2"></i>
                      Delete
                    </button>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
          <tr v-if="charges.length === 0">
            <td colspan="6" class="text-center text-muted py-4">No charges found</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!--    Deactivate charge-->
    <div v-if="showDeactivateModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Deactivate Charge</h5>
            <button type="button" class="btn-close" @click="showDeactivateModal = false"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-0">Deactivate <strong>{{ selected?.name }}</strong>?</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-secondary btn-sm" @click="showDeactivateModal = false">Cancel</button>
            <button class="btn btn-danger btn-sm" @click="deactivateCharge" :disabled="saving">{{ saving ? '...' : 'Deactivate' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
    <div v-if="showDeleteModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Charge</h5>
            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-0">Delete <strong>{{ selected?.name }}</strong>?</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-secondary btn-sm" @click="showDeleteModal = false">Cancel</button>
            <button class="btn btn-danger btn-sm" @click="deleteCharge" :disabled="saving">{{ saving ? '...' : 'Delete' }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- CREATE MODAL -->
    <div v-if="showCreateModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New Charge</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="createCharge">
            <div class="modal-body">
              <div v-if="isAdmin" class="mb-3">
                <label class="form-label fw-semibold">Business *</label>
                <select v-model="createForm.business_code" :class="['form-select', createErrors.business_code ? 'is-invalid' : '']">
                  <option value="">Select business</option>
                  <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>
                </select>
                <div v-if="createErrors.business_code" class="invalid-feedback">{{ createErrors.business_code }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Name *</label>
                <input v-model="createForm.name" :class="['form-control', createErrors.name ? 'is-invalid' : '']" placeholder="Charge name" />
                <div v-if="createErrors.name" class="invalid-feedback">{{ createErrors.name }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Charge UOM *</label>
                <select v-model="createForm.charge_uom" :class="['form-select', createErrors.charge_uom ? 'is-invalid' : '']">
                  <option value="">Select UOM</option>
                  <option value="FIXED">Fixed</option>
                  <option value="PERCENTAGE">Percentage</option>
                </select>
                <div v-if="createErrors.charge_uom" class="invalid-feedback">{{ createErrors.charge_uom }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Charge Value *</label>
                <input v-model.number="createForm.charge_value" type="number" step="0.01" min="0" :class="['form-control', createErrors.charge_value ? 'is-invalid' : '']" placeholder="Value" />
                <div v-if="createErrors.charge_value" class="invalid-feedback">{{ createErrors.charge_value }}</div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea v-model="createForm.description" class="form-control" rows="2" placeholder="Optional description"></textarea>
              </div>
              <p v-if="createError" class="text-danger small mb-0">{{ createError }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
              <button type="submit" class="btn btn-ams" :disabled="saving">{{ saving ? 'Creating...' : 'Create' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!--  Edit Modal-->
  <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Charge</h5>
          <button type="button" class="btn-close" @click="showEditModal = false"></button>
        </div>
        <form @submit.prevent="updateCharge">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Charge Name *</label>
              <input v-model="editForm.name" class="form-control" placeholder="Charge Name" required />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Description</label>
              <input v-model="editForm.description" class="form-control" placeholder="Description" />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Charge Value *</label>
              <input v-model="editForm.charge_value" class="form-control" placeholder="Charge Value" required />
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Status</label>
              <select v-model="editForm.status" class="form-select">
                <option value="ACTIVE">Active</option>
                <option value="INACTIVE">Inactive</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label fw-semibold">Auto Apply</label>
              <select v-model="editForm.auto_apply" class="form-select">
                <option :value=true>Auto Apply</option>
                <option :value=false>Manual / Off</option>
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
</template>

<script setup>
import {ref, computed, onMounted, reactive} from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

const authStore = useAuthStore()
const isAdmin = computed(
    () => authStore.user?.user_type === 'SUPER_ADMIN'
)
const charges = ref([])
const businesses = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')
const bizFilter = ref('')

const showEditModal = ref(false)
const showDeleteModal = ref(false)
const showDeactivateModal = ref(false)
const showCreateModal = ref(false)
const selected = ref(null)
const createError = ref('')
const createErrors = ref({})
const createForm = ref({ business_code: '', name: '', charge_uom: '', charge_value: '', description: '' })
const editForm = reactive({ name: '', status: 'ACTIVE', charge_value: '', description: '',auto_apply: true})

async function fetchCharges() {
  loading.value = true
  error.value = ''

  try {

    let params = {}

    if (isAdmin.value) {

      if (bizFilter.value) {
        params.business_code = bizFilter.value
      }

    } else {

      params.business_code =
          authStore.user?.business_code
    }

    const [chargeRes, businessRes] = await Promise.all([
      api.get('/charges', { params }),
      api.get('/businesses'),
    ])

    const businesses = businessRes.data.data?.data || []

    const businessNameByCode = new Map(
        businesses.map(bus => [bus.code, bus.name])
    )

    charges.value = (chargeRes.data.data?.data || []).map(charge => ({
      ...charge,
      business_name:
          businessNameByCode.get(charge.business_code) ||
          charge.business?.name ||
          ''
    }))

  } catch (err) {

    error.value =
        err.response?.data?.message ||
        'Failed to load charges'

  } finally {

    loading.value = false
  }
}

function openEdit(charge) {

  console.log('CHARGE FROM API:', charge)
  console.log('AUTO APPLY:', charge.auto_apply)
  console.log('TYPE:', typeof charge.auto_apply)

  selected.value = charge
  editForm.name = charge.name
  editForm.charge_value = charge.charge_value
  editForm.description = charge.description
  editForm.status = charge.status
  editForm.auto_apply = !!charge.auto_apply

  formError.value = ''
  showEditModal.value = true
}

function openDelete(charge) {
  selected.value = charge
  showDeleteModal.value = true
}

function openDeactivate(charge) {
  selected.value = charge
  showDeactivateModal.value = true
}

async function deleteCharge() {
  saving.value = true
  try {
    await api.delete(`/charges/${selected.value.code}`)
    showDeleteModal.value = false
    await fetchCharges()
  } catch (err) {
    error.value = err.response?.data?.message || 'Delete failed'
  } finally {
    saving.value = false
  }
}

function validateChargeForm(data) {

  const errors = {}

  if (isAdmin.value && !data.business_code) {
    errors.business_code = 'Business is required'
  }

  if (!data.name) {
    errors.name = 'Name is required'
  }

  if (!data.charge_uom) {
    errors.charge_uom = 'Charge UOM is required'
  }

  if (!data.charge_value) {
    errors.charge_value = 'Charge value is required'
  }

  return errors
}

async function createCharge() {
  const validationErrors = validateChargeForm(createForm.value)
  createErrors.value = validationErrors
  if (Object.keys(validationErrors).length > 0) return

  saving.value = true
  createError.value = ''
  try {
    const payload = { ...createForm.value,
      business_code: isAdmin.value
          ? createForm.value.business_code
          : authStore.user?.business_code }
    if (!payload.description) delete payload.description
    await api.post('/charges', payload)
    showCreateModal.value = false
    createForm.value = { business_code: isAdmin.value ? '' : (authStore.user?.business_code || ''), name: '', charge_uom: '', charge_value: '', description: '' }
    createErrors.value = {}
    await fetchCharges()
  } catch (err) {
    console.log('CREATE ERROR:', err.response?.data)
    alert(
        JSON.stringify(
            err.response?.data,
            null,
            2
        )
    )
    createError.value =
        err.response?.data?.message ||
        'Create failed'
  } finally {
    saving.value = false
  }
}

async function updateCharge() {
  saving.value = true
  formError.value = ''
  try {

    console.log('EDIT FORM:', editForm)
    console.log('AUTO APPLY TYPE:', typeof editForm.auto_apply)
    console.log('PAYLOAD:', JSON.stringify(editForm))

    await api.put(`/charges/${selected.value.code}`, editForm)
    showEditModal.value = false
    await fetchCharges()
  } catch (err) {

    console.log('UPDATE ERROR:', err.response?.data)

    alert(
        JSON.stringify(
            err.response?.data,
            null,
            2
        )
    )

    formError.value = err.response?.data?.message || 'Update failed'
  } finally {
    saving.value = false
  }
}

async function deactivateCharge() {
  saving.value = true
  try {
    await api.put(`/charges/${selected.value.code}`, { status: 'INACTIVE' })
    showDeactivateModal.value = false
    await fetchCharges()
  } catch (err) {
    error.value = err.response?.data?.message || 'Deactivation failed'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {

  console.log('USER:', authStore.user)
  console.log('IS ADMIN:', isAdmin.value)

  if (!isAdmin.value) {

    createForm.value.business_code =
        authStore.user?.business_code || ''

    await fetchCharges()

    return
  }

  const [_, bizRes] = await Promise.allSettled([
    fetchCharges(),
    api.get('/businesses')
  ])

  if (bizRes.status === 'fulfilled') {
    businesses.value =
        bizRes.value.data.data.data ||
        bizRes.value.data.data ||
        []
  }
})
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