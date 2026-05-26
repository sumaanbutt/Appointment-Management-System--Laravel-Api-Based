<template>
  <div class="page">

    <div class="page-header">
      <h2>New Appointment</h2>
      <a class="back-link" style="cursor:pointer" @click="router.back()">← Back</a>
    </div>

    <div class="card">
      <form class="form" @submit.prevent="submit">

        <div v-if="isAdmin" class="field">
          <label>Business *</label>
          <select v-model="form.business_code" :class="{ 'field-input-error': errors.business_code }" @change="onBusinessChange">
            <option value="">Select business</option>
            <option v-for="biz in businesses" :key="biz.code" :value="biz.code">
              {{ biz.name }}
            </option>
          </select>
          <p v-if="errors.business_code" class="field-error">{{ errors.business_code }}</p>
        </div>

        <div class="field">
          <label>Client</label>
          <select v-model="form.client_code">
            <option value="">Select client (optional)</option>
            <option v-for="client in clients" :key="client.code" :value="client.code">
              {{ client.name }}
            </option>
          </select>
        </div>

        <div class="field">
          <label>Service *</label>
          <select v-model="form.service_code" :class="{ 'field-input-error': errors.service_code }" @change="validateField('service_code')">
            <option value="">Select service</option>
            <option v-for="svc in services" :key="svc.code" :value="svc.code">
              {{ svc.name }}
            </option>
          </select>
          <p v-if="errors.service_code" class="field-error">{{ errors.service_code }}</p>
        </div>

        <div class="field">
          <label>Location</label>
          <select v-model="form.location_code">
            <option value="">Select location</option>
            <option v-for="loc in locations" :key="loc.code" :value="loc.code">
              {{ [loc.address, loc.street, loc.city].filter(Boolean).join(', ') || 'No Location' }}
            </option>
          </select>
        </div>

        <div class="row two-columns">
          <div class="field">
            <label>Start Date *</label>
            <input type="date" v-model="form.appointment_start_date" :class="{ 'field-input-error': errors.appointment_start_date }" @change="validateField('appointment_start_date')" />
            <p v-if="errors.appointment_start_date" class="field-error">{{ errors.appointment_start_date }}</p>
          </div>
          <div class="field">
            <label>End Date *</label>
            <input type="date" v-model="form.appointment_end_date" :class="{ 'field-input-error': errors.appointment_end_date }" @change="validateField('appointment_end_date')" />
            <p v-if="errors.appointment_end_date" class="field-error">{{ errors.appointment_end_date }}</p>
          </div>
          <div class="field">
            <label>Start Time *</label>
            <input type="time" v-model="form.start_time" :class="{ 'field-input-error': errors.start_time }" @change="validateField('start_time')" />
            <p v-if="errors.start_time" class="field-error">{{ errors.start_time }}</p>
          </div>
          <div class="field">
            <label>End Time *</label>
            <input type="time" v-model="form.end_time" :class="{ 'field-input-error': errors.end_time }" @change="validateField('end_time')" />
            <p v-if="errors.end_time" class="field-error">{{ errors.end_time }}</p>
          </div>
        </div>

        <div class="field">
          <label>Notes</label>
          <textarea v-model="form.notes" placeholder="Optional notes..." rows="3"></textarea>
        </div>

        <p v-if="error" class="error-msg">{{ error }}</p>

        <div class="form-actions">
          <router-link to="/appointments" class="cancel-btn">Cancel</router-link>
          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'Creating...' : 'Create Appointment' }}
          </button>
        </div>

      </form>
    </div>

  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth.store'

const router = useRouter()
const authStore = useAuthStore()
const isAdmin = computed(() => authStore.user?.user_type === 'SUPER_ADMIN')

const form = reactive({
  business_code: '',
  client_code: '',
  service_code: '',
  location_code: '',
  appointment_start_date: '',
  appointment_end_date: '',
  start_time: '',
  end_time: '',
  notes: '',
})

const businesses = ref([])
const clients = ref([])
const services = ref([])
const locations = ref([])
const loading = ref(false)
const error = ref('')
const errors = reactive({})

// Local Form Validation Implementation
function validateAppointmentForm(data) {
  const localErrors = {}
  if (isAdmin.value && !data.business_code) localErrors.business_code = 'Business selection is required'
  if (!data.service_code) localErrors.service_code = 'Service selection is required'
  if (!data.appointment_start_date) localErrors.appointment_start_date = 'Start date is required'
  if (!data.appointment_end_date) localErrors.appointment_end_date = 'End date is required'
  if (!data.start_time) localErrors.start_time = 'Start time is required'
  if (!data.end_time) localErrors.end_time = 'End time is required'
  return localErrors
}

function validateField(field) {
  const result = validateAppointmentForm(form)
  if (result[field]) { errors[field] = result[field] } else { delete errors[field] }
}

// Cleaned up Data Fetcher that accounts for Laravel pagination structures
async function fetchServicesAndLocations(bizCode) {
  if (!bizCode) { services.value = []; locations.value = []; return }

  const [svcRes, locRes] = await Promise.allSettled([
    api.get('/services', { params: { business_code: bizCode } }),
    api.get('/business-locations', { params: { business_code: bizCode } }),
  ])

  if (svcRes.status === 'fulfilled') {
    const rawServices = svcRes.value.data?.data?.data ?? svcRes.value.data?.data ?? []
    // Map backend 'service_name' down to '.name' for your layout loop template
    services.value = rawServices.map(s => ({
      code: s.code || s.id || '',
      name: s.service_name || 'Unnamed Service'
    }))
  }

  if (locRes.status === 'fulfilled') {
    const rawLocations = locRes.value.data?.data?.data ?? locRes.value.data?.data ?? []
    // Safely structure fallback address fields
    locations.value = rawLocations.map(l => ({
      code: l.code || l.id || '',
      address: l.address || '',
      street: l.street || '',
      city: l.city || l.location_name || ''
    }))
  }

  console.log('SERVICES:', services.value)
  console.log('LOCATIONS:', locations.value)
}

onMounted(async () => {
  const [bizRes, clientRes] = await Promise.allSettled([
    api.get('/businesses'),
    api.get('/clients'),
  ])

  if (bizRes.status === 'fulfilled') {
    businesses.value = bizRes.value.data?.data?.data ?? bizRes.value.data?.data ?? []
  }

  if (clientRes.status === 'fulfilled') {
    const backendClients = clientRes.value.data?.data?.data ?? clientRes.value.data?.data ?? []
    // Extract nested relational structure Client -> User
    clients.value = backendClients.map(c => ({
      code: c.code || c.id || '',
      name: c.user?.name || 'Unknown Client'
    }))
  }

  // Non-Admin Initialization Block
  if (!isAdmin.value) {
    form.business_code = authStore.user?.business_code || ''
    if (form.business_code) {
      await fetchServicesAndLocations(form.business_code)
    }
  }
})

async function onBusinessChange() {
  validateField('business_code')
  form.service_code = ''
  form.location_code = ''
  await fetchServicesAndLocations(form.business_code)
}

async function submit() {
  const validationErrors = validateAppointmentForm(form)
  Object.keys(errors).forEach(k => delete errors[k])
  Object.assign(errors, validationErrors)
  if (Object.keys(errors).length > 0) return

  loading.value = true
  error.value = ''
  try {
    const payload = { ...form, status: 'PENDING' }
    if (!payload.location_code) delete payload.location_code
    if (!payload.notes) delete payload.notes
    if (!payload.client_code) delete payload.client_code

    await api.post('/appointments', payload)
    router.push('/appointments')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create appointment'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 16px; }
.page-header { display: flex; align-items: center; justify-content: space-between; }
.page-header h2 { margin: 0; color: #1e293b; }
.back-link { font-size: 14px; color: #6366f1; text-decoration: none; }
.card { background: white; border-radius: 10px; padding: 24px; max-width: 700px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.form { display: flex; flex-direction: column; gap: 16px; }
.row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
.two-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select, .field textarea { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; font-family: inherit; }
.field input:focus, .field select:focus, .field textarea:focus { border-color: #6366f1; }
.field-input-error { border-color: #ef4444 !important; }
.field-error { color: #ef4444; font-size: 12px; margin: 2px 0 0; }
.error-msg { color: #ef4444; font-size: 13px; margin: 0; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.cancel-btn { padding: 9px 16px; border-radius: 6px; background: #f1f5f9; color: #64748b; text-decoration: none; font-size: 14px; }
.submit-btn { background: #6366f1; color: white; border: none; padding: 9px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
.submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }
</style>