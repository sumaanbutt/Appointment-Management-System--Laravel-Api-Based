<template>
  <div class="page">

    <div class="page-header">
      <h2>My Appointments</h2>
      <p class="sub">All appointments assigned to you</p>
    </div>

    <!-- FILTER TABS -->
    <div class="tabs">
      <button
          v-for="tab in tabs"
          :key="tab.value"
          :class="['tab', { active: activeTab === tab.value }]"
          @click="activeTab = tab.value"
      >{{ tab.label }}</button>
    </div>

    <div class="card">
      <div v-if="loading" class="loading">Loading appointments...</div>
      <table v-else class="table">
        <thead>
        <tr>
          <th>Code</th>
          <th>Date</th>
          <th>Time</th>
          <th>Notes</th>
          <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="appt in filteredAppointments" :key="appt.appointment_code">
          <td><code>{{ appt.appointment_code }}</code></td>
          <td>{{ appt.appointment_date }}</td>
          <td>{{ appt.start_time }} – {{ appt.end_time }}</td>
          <td>{{ appt.notes || '—' }}</td>
          <td><span :class="['badge', appt.status]">{{ appt.status }}</span></td>
        </tr>
        <tr v-if="filteredAppointments.length === 0">
          <td colspan="5" class="empty">No appointments</td>
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
const activeTab = ref('all')

const tabs = [
  { value: 'all', label: 'All' },
  { value: 'approved', label: 'Approved' },
  { value: 'pending', label: 'Pending' },
  { value: 'completed', label: 'Completed' },
  { value: 'rescheduled', label: 'Rescheduled' },
]

const filteredAppointments = computed(() => {
  if (activeTab.value === 'all') return appointments.value
  return appointments.value.filter(a => a.status === activeTab.value)
})

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
.page { display: flex; flex-direction: column; gap: 20px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.tabs { display: flex; gap: 8px; }
.tab { padding: 8px 18px; border-radius: 8px; border: 1px solid #e2e8f0; background: white; cursor: pointer; font-size: 13px; color: #374151; }
.tab.active { background: #3b82f6; color: white; border-color: #3b82f6; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.table th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
.loading, .empty { text-align: center; padding: 20px; color: #94a3b8; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; text-transform: capitalize; }
.badge.pending    { background: #fef9c3; color: #854d0e; }
.badge.approved   { background: #dcfce7; color: #166534; }
.badge.rejected   { background: #fee2e2; color: #991b1b; }
.badge.completed  { background: #dbeafe; color: #1e40af; }
.badge.rescheduled{ background: #ede9fe; color: #5b21b6; }
</style>
