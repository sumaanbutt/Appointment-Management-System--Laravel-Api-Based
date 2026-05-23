<template>
  <div class="page">

    <div class="page-header">
      <h2>Book an Appointment</h2>
      <p class="sub">Select a service and choose your preferred date and time</p>
    </div>

    <div class="booking-layout">

      <!-- FORM -->
      <div class="card form-card">
        <form @submit.prevent="submitBooking">

          <!-- Service -->
          <div class="field">
            <label>Service *</label>
            <select v-model="form.service_code" required @change="onServiceChange">
              <option value="">Select a service</option>
              <option v-for="svc in services" :key="svc.service_code" :value="svc.service_code">
                {{ svc.name }} ({{ svc.duration_minutes }} min)
              </option>
            </select>
          </div>

          <!-- Location -->
          <div class="field">
            <label>Location *</label>
            <select v-model="form.location_code" required>
              <option value="">Select a location</option>
              <option v-for="loc in availableLocations" :key="loc.location_code" :value="loc.location_code">
                {{ loc.name }}
              </option>
            </select>
          </div>

          <!-- Date -->
          <div class="field">
            <label>Date *</label>
            <input type="date" v-model="form.appointment_date" :min="minDate" required />
          </div>

          <!-- Time -->
          <div class="two-col">
            <div class="field">
              <label>Start Time *</label>
              <input type="time" v-model="form.start_time" required />
            </div>
            <div class="field">
              <label>End Time *</label>
              <input type="time" v-model="form.end_time" required />
            </div>
          </div>

          <!-- Notes -->
          <div class="field">
            <label>Notes</label>
            <textarea v-model="form.notes" rows="3" placeholder="Any special notes or requests"></textarea>
          </div>

          <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
          <p v-if="successMsg" class="success-msg">{{ successMsg }}</p>

          <button type="submit" class="btn-submit" :disabled="submitting">
            {{ submitting ? 'Booking...' : 'Request Appointment' }}
          </button>
        </form>
      </div>

      <!-- SERVICE DETAILS (shown after selecting service) -->
      <div v-if="selectedService" class="card info-card">
        <h3>{{ selectedService.name }}</h3>
        <p class="desc">{{ selectedService.description || 'No description' }}</p>
        <div class="detail-row"><span class="label">Duration</span><span>{{ selectedService.duration_minutes }} minutes</span></div>

        <div v-if="selectedService.charges?.length" class="charges-block">
          <p class="block-title">Business Charges</p>
          <div v-for="ch in selectedService.charges" :key="ch.charge_code" class="charge-row">
            <span>{{ ch.name }}</span>
            <span class="amount">{{ ch.currency || '$' }}{{ ch.amount }}</span>
          </div>
        </div>
        <p v-else class="muted">No additional charges</p>

        <div class="notice">
          <p>📋 Your request will be reviewed by the business. You will be notified once approved or rescheduled.</p>
        </div>
      </div>

      <div v-else class="card info-card placeholder">
        <p>Select a service to see details and pricing</p>
      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'

const route = useRoute()
const router = useRouter()

const services = ref([])
const locations = ref([])
const submitting = ref(false)
const errorMsg = ref('')
const successMsg = ref('')

const minDate = new Date().toISOString().split('T')[0]

const form = reactive({
  service_code: route.query.service_code || '',
  location_code: '',
  appointment_date: '',
  start_time: '',
  end_time: '',
  notes: '',
})

const selectedService = computed(() =>
    services.value.find(s => s.service_code === form.service_code) || null
)

// If service has locations, show only those; otherwise show all
const availableLocations = computed(() => {
  if (!selectedService.value) return locations.value
  const locs = selectedService.value.locations || []
  if (!locs.length) return locations.value
  return locs
})

function onServiceChange() {
  form.location_code = ''
}

async function submitBooking() {
  submitting.value = true
  errorMsg.value = ''
  successMsg.value = ''
  try {
    await api.post('/appointments', {
      service_code: form.service_code,
      location_code: form.location_code,
      appointment_date: form.appointment_date,
      start_time: form.start_time,
      end_time: form.end_time,
      notes: form.notes,
    })
    successMsg.value = 'Appointment request submitted! You will be notified once reviewed.'
    Object.assign(form, { service_code: '', location_code: '', appointment_date: '', start_time: '', end_time: '', notes: '' })
    setTimeout(() => router.push('/client/appointments'), 2000)
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Booking failed. Please try again.'
  } finally {
    submitting.value = false
  }
}

onMounted(async () => {
  try {
    const [svcRes, locRes] = await Promise.all([
      api.get('/services/get-service'),
      api.get('/locations/get-location'),
    ])
    services.value = svcRes.data.data || []
    locations.value = locRes.data.data || []
  } catch (_) {}
})
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 20px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.booking-layout { display: grid; grid-template-columns: 1fr 360px; gap: 20px; align-items: start; }
.card { background: white; border-radius: 12px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.form-card form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select, .field textarea { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
.two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.btn-submit { background: #3b82f6; color: white; border: none; border-radius: 8px; padding: 12px; font-size: 15px; font-weight: 600; cursor: pointer; }
.btn-submit:disabled { opacity: 0.6; cursor: not-allowed; }
.error-msg { color: #ef4444; font-size: 13px; }
.success-msg { color: #16a34a; font-size: 13px; }
.info-card h3 { margin: 0 0 8px; font-size: 18px; color: #1e293b; }
.desc { font-size: 14px; color: #64748b; margin: 0 0 12px; }
.detail-row { display: flex; justify-content: space-between; font-size: 14px; padding: 6px 0; border-bottom: 1px solid #f1f5f9; }
.label { color: #64748b; }
.charges-block { margin-top: 12px; }
.block-title { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 8px; }
.charge-row { display: flex; justify-content: space-between; font-size: 14px; padding: 4px 0; }
.amount { font-weight: 700; color: #1e293b; }
.muted { font-size: 13px; color: #94a3b8; margin: 8px 0 0; }
.notice { margin-top: 16px; background: #eff6ff; border-radius: 8px; padding: 12px; }
.notice p { margin: 0; font-size: 13px; color: #1e40af; }
.placeholder { display: flex; align-items: center; justify-content: center; min-height: 200px; color: #94a3b8; }
</style>
