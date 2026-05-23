<template>
  <div class="page">

    <div class="page-header">
      <h2>Staff Availability Checker</h2>
      <p class="sub">Check which service staff are available for a given date, time, and location</p>
    </div>

    <!-- FILTER FORM -->
    <div class="card">
      <h3 class="card-title">Filter Criteria</h3>
      <div class="filter-grid">
        <div class="field">
          <label>Date *</label>
          <input type="date" v-model="filters.date" />
        </div>
        <div class="field">
          <label>Start Time *</label>
          <input type="time" v-model="filters.start_time" />
        </div>
        <div class="field">
          <label>End Time *</label>
          <input type="time" v-model="filters.end_time" />
        </div>
        <div class="field">
          <label>Location</label>
          <select v-model="filters.location_code">
            <option value="">All Locations</option>
            <option v-for="loc in locations" :key="loc.location_code" :value="loc.location_code">
              {{ loc.name }}
            </option>
          </select>
        </div>
      </div>
      <button class="btn primary" @click="checkAvailability" :disabled="checking">
        {{ checking ? 'Checking...' : 'Check Availability' }}
      </button>
    </div>

    <!-- RESULTS -->
    <div v-if="checked" class="card">
      <h3 class="card-title">Availability Results
        <span v-if="available.length > 0" class="badge success">{{ available.length }} available</span>
        <span v-if="unavailable.length > 0" class="badge danger">{{ unavailable.length }} unavailable</span>
      </h3>

      <div v-if="available.length > 0">
        <h4 class="section-sub">✅ Available Staff</h4>
        <div class="staff-grid">
          <div v-for="s in available" :key="s.user_code" class="staff-card available">
            <div class="staff-name">{{ s.name || s.user_code }}</div>
            <div class="staff-meta">{{ s.user_type }}</div>
            <div v-if="s.location" class="staff-location">📍 {{ s.location }}</div>
          </div>
        </div>
      </div>

      <div v-if="unavailable.length > 0" style="margin-top:16px">
        <h4 class="section-sub">❌ Unavailable Staff</h4>
        <div class="staff-grid">
          <div v-for="s in unavailable" :key="s.user_code" class="staff-card unavailable">
            <div class="staff-name">{{ s.name || s.user_code }}</div>
            <div class="staff-meta">{{ s.reason || 'Not available' }}</div>
          </div>
        </div>
      </div>

      <div v-if="available.length === 0 && unavailable.length === 0" class="empty">
        No staff found for selected criteria
      </div>
    </div>

    <!-- CONFLICT NOTICE & RESCHEDULE -->
    <div v-if="checked && available.length === 0 && appointments.length > 0" class="card warning-card">
      <h3 class="card-title">⚠️ No Staff Available — Send Reschedule Request</h3>
      <p style="color:#64748b;font-size:14px">Select an appointment and propose a new date/time for the client</p>

      <div class="field" style="margin-bottom:12px">
        <label>Select Appointment</label>
        <select v-model="reschedule.appointment_code">
          <option value="">Choose appointment...</option>
          <option v-for="a in appointments" :key="a.appointment_code" :value="a.appointment_code">
            {{ a.appointment_code }} — {{ a.appointment_date }}
          </option>
        </select>
      </div>
      <div class="filter-grid">
        <div class="field">
          <label>New Date *</label>
          <input type="date" v-model="reschedule.appointment_date" />
        </div>
        <div class="field">
          <label>New Start Time *</label>
          <input type="time" v-model="reschedule.start_time" />
        </div>
        <div class="field">
          <label>New End Time *</label>
          <input type="time" v-model="reschedule.end_time" />
        </div>
      </div>
      <div class="field" style="margin-top:8px">
        <label>Reason</label>
        <textarea v-model="reschedule.reason" rows="2" placeholder="Reason for reschedule"></textarea>
      </div>
      <button class="btn accent" @click="sendReschedule" :disabled="sending" style="margin-top:12px">
        {{ sending ? 'Sending...' : 'Send Reschedule Request to Client' }}
      </button>
      <p v-if="rescheduleMsg" :class="['msg', rescheduleMsg.type]">{{ rescheduleMsg.text }}</p>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

const checking = ref(false)
const sending = ref(false)
const checked = ref(false)
const available = ref([])
const unavailable = ref([])
const locations = ref([])
const appointments = ref([])
const rescheduleMsg = ref(null)

const filters = reactive({ date: '', start_time: '', end_time: '', location_code: '' })
const reschedule = reactive({ appointment_code: '', appointment_date: '', start_time: '', end_time: '', reason: '' })

async function checkAvailability() {
  if (!filters.date || !filters.start_time) return
  checking.value = true
  checked.value = false
  available.value = []
  unavailable.value = []
  try {
    const params = new URLSearchParams()
    if (filters.date) params.set('date', filters.date)
    if (filters.start_time) params.set('start_time', filters.start_time)
    if (filters.end_time) params.set('end_time', filters.end_time)
    if (filters.location_code) params.set('location_code', filters.location_code)
    const res = await api.get(`/user-shift-schedules/check-staff-availability?${params}`)
    const data = res.data || {}
    available.value = data.available || []
    unavailable.value = data.unavailable || []
    checked.value = true
  } catch (err) {
    alert(err.response?.data?.message || 'Availability check failed')
  } finally {
    checking.value = false
  }
}

async function sendReschedule() {
  if (!reschedule.appointment_code || !reschedule.appointment_date) return
  sending.value = true
  rescheduleMsg.value = null
  try {
    await api.post(`/appointments/${reschedule.appointment_code}/reschedule`, {
      appointment_date: reschedule.appointment_date,
      start_time: reschedule.start_time,
      end_time: reschedule.end_time,
      reason: reschedule.reason,
    })
    rescheduleMsg.value = { type: 'success', text: 'Reschedule request sent to client.' }
  } catch (err) {
    rescheduleMsg.value = { type: 'error', text: err.response?.data?.message || 'Failed to send reschedule.' }
  } finally {
    sending.value = false
  }
}

onMounted(async () => {
  const [locsRes, apptsRes] = await Promise.allSettled([
    api.get('/business-locations'),
    api.get('/appointments?status=pending'),
  ])
  if (locsRes.status === 'fulfilled') locations.value = locsRes.value.data || []
  if (apptsRes.status === 'fulfilled') appointments.value = apptsRes.value.data || []
})
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 20px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.card-title { margin: 0 0 16px; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px; }
.warning-card { border-left: 4px solid #f59e0b; }
.filter-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select, .field textarea { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
.section-sub { margin: 0 0 12px; font-size: 14px; color: #374151; }
.staff-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; }
.staff-card { padding: 12px; border-radius: 8px; }
.staff-card.available { background: #f0fdf4; border: 1px solid #bbf7d0; }
.staff-card.unavailable { background: #fef2f2; border: 1px solid #fecaca; }
.staff-name { font-weight: 600; font-size: 14px; color: #1e293b; }
.staff-meta { font-size: 12px; color: #64748b; margin-top: 2px; }
.staff-location { font-size: 12px; color: #3b82f6; margin-top: 4px; }
.empty { text-align: center; color: #94a3b8; padding: 20px; }
.btn { padding: 9px 18px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; }
.btn.primary { background: #3b82f6; color: white; }
.btn.accent { background: #7c3aed; color: white; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge.success { background: #dcfce7; color: #166534; }
.badge.danger { background: #fee2e2; color: #991b1b; }
.msg { margin-top: 8px; font-size: 13px; padding: 8px; border-radius: 6px; }
.msg.success { background: #f0fdf4; color: #166534; }
.msg.error { background: #fef2f2; color: #991b1b; }
</style>
