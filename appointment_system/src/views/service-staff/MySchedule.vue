<template>
  <div class="page">

    <div class="page-header">
      <h2>My Schedule</h2>
      <p class="sub">Your assigned shifts and working hours</p>
    </div>

    <div class="card">
      <div v-if="loading" class="loading">Loading schedule...</div>
      <div v-else-if="schedules.length === 0" class="empty">No schedule assigned yet. Contact your manager.</div>
      <table v-else class="table">
        <thead>
        <tr>
          <th>Code</th>
          <th>Day / Date</th>
          <th>Start Time</th>
          <th>End Time</th>
          <th>Location</th>
          <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="s in schedules" :key="s.schedule_code">
          <td><code>{{ s.schedule_code }}</code></td>
          <td>{{ s.day_of_week || s.schedule_date }}</td>
          <td>{{ s.start_time }}</td>
          <td>{{ s.end_time }}</td>
          <td>{{ s.location?.name || s.location_code || '—' }}</td>
          <td><span :class="['badge', s.is_active ? 'active' : 'inactive']">{{ s.is_active ? 'Active' : 'Inactive' }}</span></td>
        </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const schedules = ref([])

onMounted(async () => {
  try {
    const res = await api.get('/schedules/get-schedule')
    schedules.value = res.data.data || []
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 20px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.table th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
.loading, .empty { text-align: center; padding: 20px; color: #94a3b8; }
.badge { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.badge.active   { background: #dcfce7; color: #166534; }
.badge.inactive { background: #f1f5f9; color: #64748b; }
</style>
