<template>
  <div class="ams-page">

    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="mb-0">Services</h2>
        <p class="text-muted small mb-0">Manage all services</p>
      </div>
      <router-link to="/admin/services/create" class="btn btn-ams">+ New Service</router-link>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <select v-model="bizFilter" @change="fetchServices" class="form-select" style="max-width:220px">
        <option value="">All Businesses</option>
        <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>
      </select>
      <input v-model="search" class="form-control" style="max-width:240px" placeholder="Search services..." />
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
            <tr>
              <th class="ps-3">Business Name</th>
              <th>Service Name</th>
              <th>Description</th>
              <th>Price</th>
              <th>Cost</th>
<!--              <th>Duration</th>-->
              <th>Status</th>
              <th class="pe-3" style="width:150px">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="svc in filteredServices" :key="svc.code">
              <td class="ps-3">{{ svc.business_code || '—' }}</td>
              <td>{{ svc.service_name }}</td>
              <td>{{ svc.description || '—' }}</td>
              <td>{{ svc.charges != null ? svc.charges : '—' }}</td>
              <td>{{ svc.cost != null ? svc.cost : '—' }}</td>
<!--              <td>{{ svc.duration.value }} {{ svc.duration_uom }}</td>-->
              <td><span :class="['badge',
              svc.status === 'active' ? 'bg-success' :
              svc.status === 'inactive' ? 'bg-secondary':
              'bg-light text-dark'
              ]">{{ svc.status }}</span></td>
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
            <tr v-if="filteredServices.length === 0">
              <td colspan="6" class="text-center text-muted py-4">No services found</td>
            </tr>
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
                <label class="form-label fw-semibold">Service Name *</label>
                <input v-model="editForm.service_name" class="form-control" placeholder="Service Name" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <input v-model="editForm.description" class="form-control" placeholder="Description" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Duration Time</label>
                <input v-model.number="editForm.time_duration" type="number" class="form-control" placeholder="Duration (minutes)" min="1" required />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Duration Unit Value</label>
                <select v-model="editForm.duration_uom" class="form-control"  >
                  <option :value="null">Select Duration Unit</option>
                  <option v-for="duration_uom in durationUnits" :key="duration_uom" :value="duration_uom">{{ duration_uom }}</option>
                </select>
                <!--                <input v-model.number="editForm.duration_uom" type="number" class="form-control" placeholder="Duration " min="1" />-->
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Price</label>
                <input v-model.number="editForm.charges" type="number" class="form-control" placeholder="Price" step="0.01" min="0" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Cost</label>
                <input v-model.number="editForm.cost" type="number" class="form-control" placeholder="Price" step="0.01" min="0" />
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select v-model="editForm.status" class="form-select">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
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

    <!-- DELETE CONFIRM MODAL -->
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
            <button class="btn btn-danger btn-sm" @click="deleteService" :disabled="saving">{{ saving ? '...' : 'Delete' }}</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '@/services/api'

const services = ref([])
const businesses = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')
const search = ref('')
const bizFilter = ref('')

const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selected = ref(null)
const editForm = reactive({
  service_name:'',
  description:'',
  time_duration:'',
  duration_uom:'',
  charges:'',
  cost:'',
  status:'active',
  business_code:''
})
const durationUnits = ['WEEK', 'DAY', 'HOUR', 'MINUTE']

const filteredServices = computed(() => {
  const s = search.value.toLowerCase()
  if (!s) return services.value
  return services.value.filter(svc => (svc.name || '').toLowerCase().includes(s))
})

async function fetchServices() {

  loading.value = true
  error.value = ''

  try {

    const params =
        bizFilter.value
            ? { business_code: bizFilter.value }
            : {}

    const res =
        await api.get(
            '/services',
            { params }
        )

    console.log(
        'SERVICES RESPONSE:',
        res.data
    )

    services.value =
        (res.data?.data?.data || [])
            .map(svc => ({

              ...svc,

              name:
                  svc.service_name
                  || svc.name
                  || '—',

              status:
                  svc.status
                  || '—',

              price:
                  svc.cost
                  ?? '—'
            }))

    console.log(
        'FIRST MAPPED SERVICE:',
        services.value[0]
    )

  }

  catch(err){

    error.value =
        err.response?.data?.message
        ||
        'Failed to load services'
  }

  finally{

    loading.value = false
  }
}

function openEdit(svc){

  selected.value = svc

  editForm.service_name =
      svc.service_name

  editForm.service_description =
      svc.service_description

  editForm.time_duration =
      svc.time_duration

  editForm.duration_uom =
      svc.duration_uom

  editForm.charges =
      svc.charges ?? ''

  editForm.cost =
      svc.cost ?? ''

  editForm.status =
      svc.status

  editForm.business_code =
      svc.business_code

  formError.value=''

  showEditModal.value=true
}

function openDelete(svc) {
  selected.value = svc
  showDeleteModal.value = true
}

async function updateService() {
  saving.value = true
  formError.value = ''
  try {
    await api.put(`/services/${selected.value.code}`, editForm)
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

onMounted(async () => {
  const [_, bizRes] = await Promise.allSettled([fetchServices(), api.get('/businesses')])
  if (bizRes.status === 'fulfilled') businesses.value = bizRes.value.data.data.data || []
})
</script>


