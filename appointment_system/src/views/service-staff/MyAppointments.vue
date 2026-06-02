<template>
  <div class="ams-page">
    <div><h2 class="mb-0">My Appointments</h2></div>
    <div class="d-flex gap-2">
      <select v-model="statusFilter" @change="fetch" class="form-select" style="max-width:200px">
        <option value="">All Statuses</option>
        <option value="PENDING">Pending</option>
        <option value="APPROVED">Approved</option>
        <option value="IN_PROGRESS">In Progress</option>
        <option value="COMPLETED">Completed</option>
        <option value="REJECTED">Rejected</option>
        <option value="CANCELLED">Canceled</option>
      </select>
    </div>
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
            <tr><th class="ps-3">Code</th><th>Date</th><th>Start</th><th>End</th><th>Location</th><th class="pe-3">Status</th></tr>
          </thead>
          <tbody>
            <tr v-for="appt in appointments" :key="appt.code">
              <td class="ps-3"><code>{{ appt.code }}</code></td>
              <td>{{ appt.appointment_start_date?.split('T')[0] ?? '—' }}</td>
              <td>{{ appt.start_time ?? '—' }}</td>
              <td>{{ appt.end_time ?? '—' }}</td>
              <td>{{ [appt.location.apartment, appt.location.street, appt.location.address, appt.location.city ] .filter(Boolean)
                  .join(', ')|| '—' }}</td>
              <td class="pe-3"><span :class="['ams-badge', appt.status]">{{ appt.status }}</span></td>
            </tr>
            <tr v-if="appointments.length === 0"><td colspan="6" class="text-center text-muted py-4">No appointments found</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const appointments = ref([])
const loading = ref(true)
const error = ref('')
const statusFilter = ref('')

async function fetch() {
  loading.value = true
  error.value = ''
  try {
    const params = {}
    if (statusFilter.value) params.status = statusFilter.value
    const res = await api.get('/appointments', { params })
    appointments.value = res.data.data.data || []
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load appointments'
  } finally {
    loading.value = false
  }
}

onMounted(fetch)
</script>

