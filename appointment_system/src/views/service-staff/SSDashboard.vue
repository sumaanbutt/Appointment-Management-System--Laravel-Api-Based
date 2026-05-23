<template>
  <div class="dashboard">

    <div class="page-header">
      <h2>My Dashboard</h2>
      <p class="sub">Your schedule and upcoming appointments</p>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon today">📅</div>
        <div class="stat-info">
          <p class="stat-label">Today's Appointments</p>
          <h3 class="stat-value">{{ todayCount }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon upcoming">🗓️</div>
        <div class="stat-info">
          <p class="stat-label">Upcoming</p>
          <h3 class="stat-value">{{ upcomingCount }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon completed">✅</div>
        <div class="stat-info">
          <p class="stat-label">Completed</p>
          <h3 class="stat-value">{{ completedCount }}</h3>
        </div>
      </div>
    </div>

    <!-- TODAY'S SCHEDULE -->
    <div class="card">
      <div class="card-header">
        <h3>Today's Schedule — {{ todayDate }}</h3>
        <router-link to="/staff/my-schedule" class="view-all">View Full Schedule →</router-link>
      </div>
      <div v-if="loading" class="loading">Loading...</div>
      <div v-else-if="todayAppointments.length === 0" class="empty">No appointments scheduled today</div>
      <div v-else class="appt-list">
        <div v-for="appt in todayAppointments" :key="appt.appointment_code" class="appt-item">
          <div class="appt-time">{{ appt.start_time }} – {{ appt.end_time }}</div>
          <div class="appt-info">
            <div class="appt-code"><code>{{ appt.appointment_code }}</code></div>
            <div v-if="appt.notes" class="appt-notes">{{ appt.notes }}</div>
          </div>
          <span :class="['badge', appt.status]">{{ appt.status }}</span>
        </div>
      </div>
    </div>

    <!-- UPCOMING APPOINTMENTS -->
    <div class="card">
      <div class="card-header">
        <h3>Upcoming Appointments</h3>
        <router-link to="/staff/my-appointments" class="view-all">View All →</router-link>
      </div>
      <div v-if="loading" class="loading">Loading...</div>
      <table v-else class="table">
        <thead>
        <tr><th>Code</th><th>Date</th><th>Time</th><th>Status</th></tr>
        </thead>
        <tbody>
        <tr v-for="appt in upcomingAppointments" :key="appt.appointment_code">
          <td><code>{{ appt.appointment_code }}</code></td>
          <td>{{ appt.appointment_date }}</td>
          <td>{{ appt.start_time }}</td>
          <td><span :class="['badge', appt.status]">{{ appt.status }}</span></td>
        </tr>
        <tr v-if="upcomingAppointments.length === 0">
          <td colspan="4" class="empty">No upcoming appointments</td>
        </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const appointments = ref([])

const todayDate = new Date().toISOString().split('T')[0]

const todayAppointments = computed(() =>
    appointments.value.filter(a => a.appointment_date === todayDate)
)
const upcomingAppointments = computed(() =>
    appointments.value.filter(a => a.appointment_date > todayDate && a.status === 'approved').slice(0, 8)
)
const todayCount = computed(() => todayAppointments.value.length)
const upcomingCount = computed(() => appointments.value.filter(a => a.appointment_date > todayDate).length)
const completedCount = computed(() => appointments.value.filter(a => a.status === 'completed').length)

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
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.stat-card { background: white; padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.stat-icon.today     { background: #fef9c3; }
.stat-icon.upcoming  { background: #dbeafe; }
.stat-icon.completed { background: #dcfce7; }
.stat-label { margin: 0; font-size: 13px; color: #64748b; }
.stat-value { margin: 4px 0 0; font-size: 26px; font-weight: 700; color: #1e293b; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.view-all { font-size: 13px; color: #3b82f6; text-decoration: none; }
.appt-list { display: flex; flex-direction: column; gap: 10px; }
.appt-item { display: flex; align-items: center; gap: 16px; padding: 12px; border-radius: 8px; background: #f8fafc; }
.appt-time { font-weight: 700; font-size: 14px; color: #374151; white-space: nowrap; min-width: 120px; }
.appt-info { flex: 1; }
.appt-code code { font-size: 13px; color: #3b82f6; }
.appt-notes { font-size: 12px; color: #64748b; margin-top: 2px; }
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
</style>
