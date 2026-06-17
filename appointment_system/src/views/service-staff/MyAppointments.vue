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

<style scoped>
.ams-page{
  display:flex;
  flex-direction:column;
  gap:20px;
}
.card{
  border-radius:12px;
}
.ams-table th,
.ams-table td{
  vertical-align:middle;
  font-size:14px;
}
code{
  background:#f1f5f9;
  padding:3px 8px;
  border-radius:6px;
  color:#334155;
}
.btn-ams{
  background:#6366f1;
  color:#fff;
  border:none;
}
.btn-ams:hover{
  background:#4f46e5;
  color:#fff;
}
.form-control,
.form-select{
  border-radius:8px;
  font-size:14px;
}
.form-control:focus,
.form-select:focus{
  border-color:#6366f1;
  box-shadow:0 0 0 0.15rem rgba(99,102,241,.15);
}
.ams-badge{
  display:inline-block;
  padding:4px 10px;
  border-radius:999px;
  font-size:11px;
  font-weight:600;
  text-transform:capitalize;
}
.ams-badge.PENDING{ background:#fef3c7; color:#92400e; }
.ams-badge.APPROVED{ background:#dcfce7; color:#166534; }
.ams-badge.REJECTED{ background:#fee2e2; color:#991b1b; }
.ams-badge.COMPLETED{ background:#dbeafe; color:#1e40af; }
.ams-badge.RESCHEDULED{ background:#ede9fe; color:#6d28d9; }
.ams-badge.CANCELLED{ background:#f1f5f9; color:#475569; }
.ams-badge.IN_PROGRESS{ background:#cffafe; color:#155e75; }

.modal-content{
  border:none;
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 15px 45px rgba(0,0,0,.18);
}
.modal-header{ background:#f8fafc; }
.modal-title{ font-weight:700; }
.modal-footer{ background:#fafafa; }

.list-group-item{
  border-radius:10px !important;
  border:1px solid #e2e8f0;
}
.cursor-pointer {
  cursor: pointer;
}
.btn-success{ background:#22c55e; border-color:#22c55e; }
.btn-success:hover{ background:#16a34a; border-color:#16a34a; }
.btn-danger{ background:#ef4444; border-color:#ef4444; }
.btn-danger:hover{ background:#dc2626; border-color:#dc2626; }
.btn-outline-primary{ color:#6366f1; border-color:#6366f1; }
.btn-outline-primary:hover{ background:#6366f1; color:white; }
dl dt{ font-size:13px; }
dl dd{ font-size:14px; }

@media(max-width:768px){
  .ams-table{ min-width:1000px; }
  .d-flex.gap-2.flex-wrap{ flex-direction:column; }
  .form-control, .form-select{ max-width:100% !important; }
}
</style>