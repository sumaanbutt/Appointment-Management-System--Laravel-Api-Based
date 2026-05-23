<template>
  <div class="dashboard">

    <div class="page-header">
      <h2>Business Dashboard</h2>
      <p class="sub">Manage your business operations</p>
    </div>

    <!-- STATS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon staff">👤</div>
        <div class="stat-info">
          <p class="stat-label">Staff Members</p>
          <h3 class="stat-value">{{ stats.users }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon client">👥</div>
        <div class="stat-info">
          <p class="stat-label">Clients</p>
          <h3 class="stat-value">{{ stats.clients }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon svc">⚕️</div>
        <div class="stat-info">
          <p class="stat-label">Services</p>
          <h3 class="stat-value">{{ stats.services }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon loc">📍</div>
        <div class="stat-info">
          <p class="stat-label">Locations</p>
          <h3 class="stat-value">{{ stats.locations }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon app">📅</div>
        <div class="stat-info">
          <p class="stat-label">Pending Appointments</p>
          <h3 class="stat-value">{{ stats.pendingAppts }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon charge">💰</div>
        <div class="stat-info">
          <p class="stat-label">Active Charges</p>
          <h3 class="stat-value">{{ stats.charges }}</h3>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="card">
      <h3 class="card-title">Quick Actions</h3>
      <div class="actions-grid">
        <router-link to="/owner/users/create" class="action-btn primary">+ Add Staff</router-link>
        <router-link to="/owner/clients/create" class="action-btn secondary">+ Add Client</router-link>
        <router-link to="/owner/services/create" class="action-btn secondary">+ Add Service</router-link>
        <router-link to="/owner/locations/create" class="action-btn secondary">+ Add Location</router-link>
        <router-link to="/owner/staff-availability" class="action-btn accent">Check Staff Availability</router-link>
      </div>
    </div>

    <!-- PENDING APPOINTMENTS -->
    <div class="card">
      <div class="card-header">
        <h3>Pending Appointments</h3>
        <router-link to="/owner/appointments" class="view-all">View All →</router-link>
      </div>
      <div v-if="loading" class="loading">Loading...</div>
      <table v-else class="table">
        <thead>
        <tr>
          <th>Code</th>
          <th>Date</th>
          <th>Start Time</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="appt in pendingAppointments" :key="appt.appointment_code">
          <td><code>{{ appt.appointment_code }}</code></td>
          <td>{{ appt.appointment_date }}</td>
          <td>{{ appt.start_time }}</td>
          <td><span :class="['badge', appt.status]">{{ appt.status }}</span></td>
          <td>
            <button class="btn-sm success" @click="changeStatus(appt, 'approved')">Approve</button>
            <button class="btn-sm danger" @click="changeStatus(appt, 'rejected')">Reject</button>
          </td>
        </tr>
        <tr v-if="pendingAppointments.length === 0">
          <td colspan="5" class="empty">No pending appointments</td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- RESCHEDULE REQUESTS -->
    <div v-if="rescheduleRequests.length > 0" class="card">
      <div class="card-header">
        <h3>Reschedule Requests</h3>
        <span class="badge-count">{{ rescheduleRequests.length }}</span>
      </div>
      <table class="table">
        <thead>
        <tr><th>Code</th><th>Original Date</th><th>New Date</th><th>Reason</th></tr>
        </thead>
        <tbody>
        <tr v-for="req in rescheduleRequests" :key="req.appointment_code">
          <td><code>{{ req.appointment_code }}</code></td>
          <td>{{ req.appointment_date }}</td>
          <td>{{ req.new_date || '—' }}</td>
          <td>{{ req.reason || '—' }}</td>
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
const stats = ref({ users: 0, clients: 0, services: 0, locations: 0, pendingAppts: 0, charges: 0 })

const pendingAppointments = computed(() =>
    appointments.value.filter(a => a.status === 'pending').slice(0, 6)
)
const rescheduleRequests = computed(() =>
    appointments.value.filter(a => a.status === 'rescheduled')
)

async function changeStatus(appt, status) {
  try {
    await api.patch(`/appointments/${appt.appointment_code}/status`, { status })
    appt.status = status
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to update status')
  }
}

onMounted(async () => {
  try {
    const [users, clients, svcs, locs, appts, charges] = await Promise.allSettled([
      api.get('/users'),
      api.get('/clients'),
      api.get('/services'),
      api.get('/business-locations'),
      api.get('/appointments'),
      api.get('/charges'),
    ])
    stats.value.users = users.status === 'fulfilled' ? (users.value.data?.length ?? 0) : 0
    stats.value.clients = clients.status === 'fulfilled' ? (clients.value.data?.length ?? 0) : 0
    stats.value.services = svcs.status === 'fulfilled' ? (svcs.value.data?.length ?? 0) : 0
    stats.value.locations = locs.status === 'fulfilled' ? (locs.value.data?.length ?? 0) : 0
    stats.value.charges = charges.status === 'fulfilled' ? (charges.value.data?.length ?? 0) : 0
    if (appts.status === 'fulfilled') {
      appointments.value = appts.value.data || []
      stats.value.pendingAppts = appointments.value.filter(a => a.status === 'pending').length
    }
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
.stat-card {
  background: white; padding: 20px; border-radius: 12px;
  display: flex; align-items: center; gap: 16px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.stat-icon { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
.stat-icon.staff  { background: #ffe4e6; }
.stat-icon.client { background: #dcfce7; }
.stat-icon.svc    { background: #e0f2fe; }
.stat-icon.loc    { background: #fef3c7; }
.stat-icon.app    { background: #fef9c3; }
.stat-icon.charge { background: #f0fdf4; }
.stat-label { margin: 0; font-size: 13px; color: #64748b; }
.stat-value { margin: 4px 0 0; font-size: 26px; font-weight: 700; color: #1e293b; }

.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.card-title { margin: 0 0 16px; font-size: 16px; color: #1e293b; }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.view-all { font-size: 13px; color: #3b82f6; text-decoration: none; }
.badge-count { background: #ef4444; color: white; border-radius: 50%; padding: 2px 7px; font-size: 12px; }

.actions-grid { display: flex; gap: 12px; flex-wrap: wrap; }
.action-btn {
  padding: 10px 20px; border-radius: 8px; font-size: 14px;
  font-weight: 500; text-decoration: none; transition: opacity 0.2s;
}
.action-btn:hover { opacity: 0.85; }
.action-btn.primary { background: #3b82f6; color: white; }
.action-btn.secondary { background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0; }
.action-btn.accent { background: #7c3aed; color: white; }

.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.table th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
.empty { text-align: center; color: #94a3b8; padding: 20px !important; }
.loading { text-align: center; padding: 20px; color: #94a3b8; }

.btn-sm { padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; margin-right: 4px; }
.btn-sm.success { background: #dcfce7; color: #166534; }
.btn-sm.danger  { background: #fee2e2; color: #991b1b; }

.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
.badge.pending    { background: #fef9c3; color: #854d0e; }
.badge.approved   { background: #dcfce7; color: #166534; }
.badge.rejected   { background: #fee2e2; color: #991b1b; }
.badge.completed  { background: #dbeafe; color: #1e40af; }
.badge.rescheduled{ background: #ede9fe; color: #5b21b6; }
</style>
