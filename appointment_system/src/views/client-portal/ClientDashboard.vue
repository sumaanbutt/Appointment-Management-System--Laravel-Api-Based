<template>
  <div class="dashboard">

    <div class="page-header">
      <h2>Welcome Back!</h2>
      <p class="sub">Manage your appointments and book new services</p>
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
          <p class="stat-label">Approved</p>
          <h3 class="stat-value">{{ countByStatus('approved') }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon completed">🏁</div>
        <div class="stat-info">
          <p class="stat-label">Completed</p>
          <h3 class="stat-value">{{ countByStatus('completed') }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon rejected">❌</div>
        <div class="stat-info">
          <p class="stat-label">Rejected</p>
          <h3 class="stat-value">{{ countByStatus('rejected') }}</h3>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="quick-actions">
      <router-link to="/client/services" class="action-card">
        <span class="action-icon">🔍</span>
        <div>
          <p class="action-title">Browse Services</p>
          <p class="action-sub">Explore available services and pricing</p>
        </div>
      </router-link>
      <router-link to="/client/book" class="action-card primary">
        <span class="action-icon">📅</span>
        <div>
          <p class="action-title">Book Appointment</p>
          <p class="action-sub">Schedule a new appointment</p>
        </div>
      </router-link>
    </div>

    <!-- RECENT APPOINTMENTS -->
    <div class="card">
      <div class="card-header">
        <h3>Recent Appointments</h3>
        <router-link to="/client/appointments" class="view-all">View All →</router-link>
      </div>
      <div v-if="loading" class="loading">Loading...</div>
      <table v-else class="table">
        <thead>
        <tr><th>Code</th><th>Date</th><th>Time</th><th>Status</th></tr>
        </thead>
        <tbody>
        <tr v-for="appt in recentAppointments" :key="appt.appointment_code">
          <td><code>{{ appt.appointment_code }}</code></td>
          <td>{{ appt.appointment_date }}</td>
          <td>{{ appt.start_time }}</td>
          <td><span :class="['badge', appt.status]">{{ appt.status }}</span></td>
        </tr>
        <tr v-if="recentAppointments.length === 0">
          <td colspan="4" class="empty">No appointments yet. <router-link to="/client/book">Book one now</router-link></td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- RESCHEDULE REQUESTS -->
    <div v-if="rescheduleRequests.length > 0" class="card">
      <div class="card-header">
        <h3>⚠️ Reschedule Requests</h3>
      </div>
      <div v-for="appt in rescheduleRequests" :key="appt.appointment_code" class="reschedule-item">
        <div class="reschedule-info">
          <p><strong>{{ appt.appointment_code }}</strong> — Proposed: {{ appt.reschedule_date || appt.appointment_date }}</p>
          <p class="muted">{{ appt.reschedule_reason || 'New time proposed by staff' }}</p>
        </div>
        <div class="reschedule-actions">
          <button class="btn-sm success" @click="respondReschedule(appt, 'approved')">Accept</button>
          <button class="btn-sm danger" @click="respondReschedule(appt, 'rejected')">Decline</button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const appointments = ref([])

const recentAppointments = computed(() => appointments.value.slice(0, 5))
const rescheduleRequests = computed(() => appointments.value.filter(a => a.status === 'rescheduled'))

function countByStatus(status) {
  return appointments.value.filter(a => a.status === status).length
}

async function respondReschedule(appt, status) {
  try {
    await api.patch(`/appointments/${appt.appointment_code}/status`, { status })
    appt.status = status
  } catch (err) {
    alert(err.response?.data?.message || 'Failed')
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
.stat-icon.pending   { background: #fef9c3; }
.stat-icon.approved  { background: #dcfce7; }
.stat-icon.completed { background: #dbeafe; }
.stat-icon.rejected  { background: #fee2e2; }
.stat-label { margin: 0; font-size: 13px; color: #64748b; }
.stat-value { margin: 4px 0 0; font-size: 26px; font-weight: 700; color: #1e293b; }
.quick-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.action-card { background: white; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); text-decoration: none; color: inherit; border: 1px solid #e2e8f0; }
.action-card.primary { background: #3b82f6; color: white; border-color: #3b82f6; }
.action-card.primary .action-sub { color: #bfdbfe; }
.action-icon { font-size: 28px; }
.action-title { margin: 0; font-weight: 600; font-size: 15px; }
.action-sub { margin: 4px 0 0; font-size: 12px; color: #64748b; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.view-all { font-size: 13px; color: #3b82f6; text-decoration: none; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.table th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
.empty { text-align: center; color: #94a3b8; padding: 20px; }
.loading { text-align: center; padding: 20px; color: #94a3b8; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
.badge.pending    { background: #fef9c3; color: #854d0e; }
.badge.approved   { background: #dcfce7; color: #166534; }
.badge.rejected   { background: #fee2e2; color: #991b1b; }
.badge.completed  { background: #dbeafe; color: #1e40af; }
.badge.rescheduled{ background: #ede9fe; color: #5b21b6; }
.reschedule-item { display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; margin-bottom: 8px; }
.reschedule-info p { margin: 0; font-size: 14px; }
.muted { color: #64748b; font-size: 12px !important; }
.reschedule-actions { display: flex; gap: 8px; }
.btn-sm { padding: 6px 14px; border-radius: 6px; border: none; cursor: pointer; font-size: 13px; font-weight: 500; }
.btn-sm.success { background: #dcfce7; color: #166534; }
.btn-sm.danger  { background: #fee2e2; color: #991b1b; }
</style>
