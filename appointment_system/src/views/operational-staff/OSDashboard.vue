<template>
  <div class="dashboard">

    <div class="page-header">
      <h2>Operational Staff Dashboard</h2>
      <p class="sub">Manage and process appointment requests</p>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon pending">⏳</div>
        <div class="stat-info">
          <p class="stat-label">Pending</p>
          <h3 class="stat-value">{{ countByStatus('pending') }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon approved">✅</div>
        <div class="stat-info">
          <p class="stat-label">Approved Today</p>
          <h3 class="stat-value">{{ countByStatus('approved') }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon rescheduled">🔄</div>
        <div class="stat-info">
          <p class="stat-label">Rescheduled</p>
          <h3 class="stat-value">{{ countByStatus('rescheduled') }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon total">📅</div>
        <div class="stat-info">
          <p class="stat-label">Total</p>
          <h3 class="stat-value">{{ appointments.length }}</h3>
        </div>
      </div>
    </div>

    <!-- APPOINTMENT QUEUE -->
    <div class="card">
      <div class="card-header">
        <h3>Appointment Queue</h3>
        <div class="header-actions">
          <select v-model="statusFilter" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rescheduled">Rescheduled</option>
            <option value="rejected">Rejected</option>
          </select>
          <router-link to="/ops/availability" class="btn accent">Check Availability</router-link>
        </div>
      </div>

      <div v-if="loading" class="loading">Loading appointments...</div>
      <table v-else class="table">
        <thead>
        <tr>
          <th>Code</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="appt in filteredAppointments" :key="appt.appointment_code">
          <td><code>{{ appt.appointment_code }}</code></td>
          <td>{{ appt.appointment_date }}</td>
          <td>{{ appt.start_time }} – {{ appt.end_time }}</td>
          <td><span :class="['badge', appt.status]">{{ appt.status }}</span></td>
          <td>
            <button
                v-if="appt.status === 'pending'"
                class="btn-sm success"
                @click="changeStatus(appt, 'approved')"
            >Approve</button>
            <button
                v-if="appt.status === 'pending' || appt.status === 'approved'"
                class="btn-sm warning"
                @click="openReschedule(appt)"
            >Reschedule</button>
            <button
                v-if="appt.status === 'pending'"
                class="btn-sm danger"
                @click="changeStatus(appt, 'rejected')"
            >Reject</button>
          </td>
        </tr>
        <tr v-if="filteredAppointments.length === 0">
          <td colspan="5" class="empty">No appointments found</td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- RESCHEDULE MODAL -->
    <div v-if="showReschedule" class="modal-overlay" @click.self="showReschedule = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Reschedule Appointment</h3>
          <button class="close" @click="showReschedule = false">✕</button>
        </div>
        <p class="modal-sub">Check staff availability first, then propose a new date/time to the client.</p>
        <form class="form" @submit.prevent="submitReschedule">
          <div class="field">
            <label>New Date *</label>
            <input type="date" v-model="rescheduleForm.appointment_date" required />
          </div>
          <div class="field">
            <label>Start Time *</label>
            <input type="time" v-model="rescheduleForm.start_time" required />
          </div>
          <div class="field">
            <label>End Time *</label>
            <input type="time" v-model="rescheduleForm.end_time" required />
          </div>
          <div class="field">
            <label>Reason</label>
            <textarea v-model="rescheduleForm.reason" rows="3" placeholder="Reason for reschedule"></textarea>
          </div>
          <p v-if="rescheduleError" class="error-msg">{{ rescheduleError }}</p>
          <div class="form-actions">
            <button type="button" class="btn secondary" @click="showReschedule = false">Cancel</button>
            <button type="submit" class="btn primary" :disabled="saving">
              {{ saving ? 'Sending...' : 'Send Reschedule' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const saving = ref(false)
const showReschedule = ref(false)
const appointments = ref([])
const selectedAppt = ref(null)
const statusFilter = ref('')
const rescheduleError = ref('')

const rescheduleForm = reactive({ appointment_date: '', start_time: '', end_time: '', reason: '' })

const filteredAppointments = computed(() => {
  if (!statusFilter.value) return appointments.value
  return appointments.value.filter(a => a.status === statusFilter.value)
})

function countByStatus(status) {
  return appointments.value.filter(a => a.status === status).length
}

async function changeStatus(appt, status) {
  try {
    await api.patch(`/appointments/${appt.appointment_code}/status`, { status })
    appt.status = status
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to update status')
  }
}

function openReschedule(appt) {
  selectedAppt.value = appt
  rescheduleForm.appointment_date = appt.appointment_date || ''
  rescheduleForm.start_time = ''
  rescheduleForm.end_time = ''
  rescheduleForm.reason = ''
  rescheduleError.value = ''
  showReschedule.value = true
}

async function submitReschedule() {
  saving.value = true
  rescheduleError.value = ''
  try {
    await api.post(`/appointments/${selectedAppt.value.appointment_code}/reschedule`, rescheduleForm)
    selectedAppt.value.status = 'rescheduled'
    showReschedule.value = false
  } catch (err) {
    rescheduleError.value = err.response?.data?.message || 'Reschedule failed'
  } finally {
    saving.value = false
  }
}

onMounted(async () => {
  try {
    const res = await api.get('/appointments')
    appointments.value = res.data.data || []
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.dashboard { display: flex; flex-direction: column; gap: 20px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.stat-card { background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.stat-icon.pending     { background: #fef9c3; }
.stat-icon.approved    { background: #dcfce7; }
.stat-icon.rescheduled { background: #ede9fe; }
.stat-icon.total       { background: #dbeafe; }
.stat-label { margin: 0; font-size: 13px; color: #64748b; }
.stat-value { margin: 4px 0 0; font-size: 26px; font-weight: 700; color: #1e293b; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.header-actions { display: flex; gap: 12px; align-items: center; }
.filter-select { padding: 7px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.table th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
.empty { text-align: center; color: #94a3b8; padding: 20px !important; }
.loading { text-align: center; padding: 20px; color: #94a3b8; }
.btn { padding: 9px 18px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; display: inline-block; }
.btn.primary { background: #3b82f6; color: white; }
.btn.secondary { background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0; }
.btn.accent { background: #7c3aed; color: white; }
.btn-sm { padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; margin-right: 4px; }
.btn-sm.success  { background: #dcfce7; color: #166534; }
.btn-sm.warning  { background: #fef3c7; color: #92400e; }
.btn-sm.danger   { background: #fee2e2; color: #991b1b; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
.badge.pending    { background: #fef9c3; color: #854d0e; }
.badge.approved   { background: #dcfce7; color: #166534; }
.badge.rejected   { background: #fee2e2; color: #991b1b; }
.badge.completed  { background: #dbeafe; color: #1e40af; }
.badge.rescheduled{ background: #ede9fe; color: #5b21b6; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal { background: white; border-radius: 12px; width: 480px; max-width: 95vw; padding: 24px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
.modal-header h3 { margin: 0; }
.modal-sub { margin: 0 0 16px; color: #64748b; font-size: 13px; }
.close { background: none; border: none; font-size: 18px; cursor: pointer; color: #64748b; }
.form { display: flex; flex-direction: column; gap: 14px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field textarea { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.error-msg { color: #ef4444; font-size: 13px; }
</style>
