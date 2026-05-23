<template>
  <div class="dashboard">

    <div class="page-header">
      <h2>Admin Dashboard</h2>
      <p class="sub">System-wide overview</p>
    </div>

    <!-- STAT CARDS -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon org">🏢</div>
        <div class="stat-info">
          <p class="stat-label">Organizations</p>
          <h3 class="stat-value">{{ stats.organizations }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon biz">🏪</div>
        <div class="stat-info">
          <p class="stat-label">Businesses</p>
          <h3 class="stat-value">{{ stats.businesses }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon usr">👤</div>
        <div class="stat-info">
          <p class="stat-label">Users</p>
          <h3 class="stat-value">{{ stats.users }}</h3>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon app">📅</div>
        <div class="stat-info">
          <p class="stat-label">Total Appointments</p>
          <h3 class="stat-value">{{ stats.appointments }}</h3>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="card">
      <h3 class="card-title">Quick Actions</h3>
      <div class="actions-grid">
        <router-link to="/admin/organizations/create" class="action-btn primary">
          + New Organization
        </router-link>
        <router-link to="/admin/businesses/create" class="action-btn secondary">
          + New Business
        </router-link>
        <router-link to="/admin/users/create" class="action-btn secondary">
          + New User
        </router-link>
      </div>
    </div>

    <!-- RECENT APPOINTMENTS -->
    <div class="card">
      <div class="card-header">
        <h3>Recent Appointments</h3>
        <router-link to="/admin/invoices" class="view-all">View Invoices →</router-link>
      </div>
      <div v-if="loading" class="loading">Loading...</div>
      <table v-else class="table">
        <thead>
        <tr>
          <th>Code</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="appt in recentAppointments" :key="appt.appointment_code">
          <td><code>{{ appt.appointment_code }}</code></td>
          <td>{{ appt.appointment_date }}</td>
          <td><span :class="['badge', appt.status]">{{ appt.status }}</span></td>
        </tr>
        <tr v-if="recentAppointments.length === 0">
          <td colspan="3" class="empty">No recent appointments</td>
        </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api.ts'

const loading = ref(true)
const recentAppointments = ref([])
const stats = ref({ organizations: 0, businesses: 0, users: 0, appointments: 0 })

onMounted(async () => {
  try {
    const [orgs, bizs, users, appts] = await Promise.allSettled([
      api.get('/organizations'),
      api.get('/businesses'),
      api.get('/users'),
      api.get('/appointments'),
    ])
    stats.value.organizations = orgs.status === 'fulfilled' ? (orgs.value.data.data?.length ?? 0) : 0
    stats.value.businesses = bizs.status === 'fulfilled' ? (bizs.value.data.data.data?.length ?? 0) : 0
    stats.value.users = users.status === 'fulfilled' ? (users.value.data.data.data?.length ?? 0) : 0
    stats.value.appointments = appts.status === 'fulfilled' ? (appts.value.data.data.data?.length ?? 0) : 0
    if (appts.status === 'fulfilled') {
      recentAppointments.value = (appts.value.data.data || []).slice(0, 6)
    }
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.dashboard { display: flex; flex-direction: column; gap: 20px; }
.page-header { margin-bottom: 4px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }

.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.stat-card {
  background: white; padding: 20px; border-radius: 12px;
  display: flex; align-items: center; gap: 16px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.stat-icon {
  width: 48px; height: 48px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; font-size: 22px;
}
.stat-icon.org   { background: #ede9fe; }
.stat-icon.biz   { background: #dbeafe; }
.stat-icon.usr   { background: #ffe4e6; }
.stat-icon.app   { background: #fef9c3; }
.stat-label { margin: 0; font-size: 13px; color: #64748b; }
.stat-value { margin: 4px 0 0; font-size: 26px; font-weight: 700; color: #1e293b; }

.card {
  background: white; border-radius: 12px;
  padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.card-title { margin: 0 0 16px; font-size: 16px; color: #1e293b; }
.card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
.card-header h3 { margin: 0; font-size: 16px; color: #1e293b; }
.view-all { font-size: 13px; color: #3b82f6; text-decoration: none; }

.actions-grid { display: flex; gap: 12px; flex-wrap: wrap; }
.action-btn {
  padding: 10px 20px; border-radius: 8px; font-size: 14px;
  font-weight: 500; text-decoration: none; transition: opacity 0.2s;
}
.action-btn:hover { opacity: 0.85; }
.action-btn.primary { background: #3b82f6; color: white; }
.action-btn.secondary { background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0; }

.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.table th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
.empty { text-align: center; color: #94a3b8; padding: 20px !important; }
.loading { text-align: center; padding: 20px; color: #94a3b8; }

.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
.badge.pending    { background: #fef9c3; color: #854d0e; }
.badge.approved   { background: #dcfce7; color: #166534; }
.badge.rejected   { background: #fee2e2; color: #991b1b; }
.badge.completed  { background: #dbeafe; color: #1e40af; }
.badge.rescheduled{ background: #ede9fe; color: #5b21b6; }
</style>
