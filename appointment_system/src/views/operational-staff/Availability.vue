<template>
  <div class="page">
    <div class="header"><h2>Check Staff Availability</h2></div>
    <div class="card">
      <form class="form" @submit.prevent="checkAvailability">
        <div class="field">
          <label>Location *</label>
          <select v-model="form.location_code" class="field-select" required>
            <option value="">Select Location</option>
            <option v-for="loc in locations" :key="loc.code" :value="loc.code">
              {{ loc.address }}
              {{ loc.street }}
              {{ loc.city }}
            </option>
          </select>
        </div>
        <div class="field"><label>Date *</label><input v-model="form.date" type="date" required /></div>
        <div class="field"><label>Start Time *</label><input v-model="form.startTime" type="time" required /></div>
        <div class="field"><label>End Time *</label><input v-model="form.endTime" type="time" required /></div>
        <button type="submit" class="check-btn" :disabled="loading">{{ loading ? 'Checking...' : 'Check Availability' }}</button>
      </form>
    </div>
    <div v-if="checked" class="card result-card">
      <h3>Results</h3>
      <div v-if="error" class="error-msg">{{ error }}</div>
      <div v-else-if="available.length === 0" class="empty">No available staff found</div>
      <table v-else class="table">
        <thead><tr><th>Staff Code</th><th>Employee Type</th><th>Working Days</th><th>Start Time</th><th>End Time</th></tr></thead>
        <tbody>
          <tr v-for="s in available" :key="s.code">
            <td><code>{{ s.user_code }}</code></td>
            <td>{{ s.employee_type || '—' }}</td>
            <td>{{ s.working_days }}</td>
            <td>{{ s.start_time }}</td>
            <td>{{ s.end_time }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'
import { onMounted } from 'vue'

const authStore = useAuthStore()
const form = reactive({ location_code: '', date: '', startTime: '', endTime: '' })
const locations = ref([])
const available = ref([])
const loading = ref(false)
const error = ref('')
const checked = ref(false)

async function checkAvailability() {
  loading.value = true
  error.value = ''
  checked.value = false
  try {
    const biz = authStore.user?.business_code
    const res = await api.get('/user-shift-schedules/check-staff-availability', {
      params: {
        business_code: biz,
        location_code: form.location_code,
        date: form.date,
        start_time: form.startTime,
        end_time: form.endTime,
      }
    })
    available.value = res.data.available || []
    checked.value = true
  } catch (err) {

    console.log('FULL ERROR:', err)

    console.log(
        'RESPONSE:',
        err.response?.data
    )

    console.log(
        'MESSAGE:',
        err.message
    )

    alert(
        err.response?.data?.error
        ||
        err.response?.data?.message
        ||
        err.message
        ||
        'Unknown Error'
    )

  }finally {
    loading.value = false
  }
}

onMounted(async () => {

  try{

    const biz =
        authStore.user?.business_code

    const res =
        await api.get(
            '/business-locations',
            {
              params:{
                business_code:biz
              }
            }
        )

    locations.value =
        res.data.data.data || []

  }
  catch(err){

    console.log(
        err.response?.data
    )

  }

})
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 16px; }
.header h2 { margin: 0; color: #1e293b; }
.card { background: white; border-radius: 10px; padding: 24px; max-width: 560px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.result-card { max-width: 100%; }
.form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input,
.field select { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; }
.check-btn { background: #6366f1; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; align-self: flex-start; }
.result-card h3 { margin: 0 0 12px; font-size: 16px; }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { text-align: left; padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
.table th { color: #64748b; font-weight: 600; }
.empty { text-align: center; color: #94a3b8; padding: 20px; font-size: 14px; }
.error-msg { color: #ef4444; font-size: 13px; }
code { font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
</style>
