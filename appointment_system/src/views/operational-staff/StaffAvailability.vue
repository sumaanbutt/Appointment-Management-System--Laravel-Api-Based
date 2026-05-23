<template>
  <div class="page">

    <div class="page-header">
      <h2>Staff Availability Check</h2>
      <p class="sub">Check service staff availability before approving or rescheduling appointments</p>
    </div>

    <!-- FILTER -->
    <div class="card">
      <h3 class="card-title">Check Parameters</h3>
      <div class="filter-grid">
        <div class="field">
          <label>Date *</label>
          <input type="date" v-model="filters.date" />
        </div>
        <div class="field">
          <label>Start Time *</label>
          <input type="time" v-model="filters.start_time" />
        </div>
        <div class="field">
          <label>End Time</label>
          <input type="time" v-model="filters.end_time" />
        </div>
        <div class="field">
          <label>Location</label>
          <select v-model="filters.location_code">
            <option value="">All Locations</option>
            <option v-for="loc in locations" :key="loc.location_code" :value="loc.location_code">
              {{ loc.name }}
            </option>
          </select>
        </div>
      </div>
      <button class="btn primary" @click="checkAvailability" :disabled="checking || !filters.date || !filters.start_time">
        {{ checking ? 'Checking...' : 'Check Availability' }}
      </button>
    </div>

    <!-- RESULTS -->
    <div v-if="checked" class="card">
      <h3 class="card-title">
        Results for {{ filters.date }} at {{ filters.start_time }}
        <span v-if="available.length" class="pill success">{{ available.length }} available</span>
        <span v-if="unavailable.length" class="pill danger">{{ unavailable.length }} busy</span>
      </h3>

      <div v-if="available.length > 0">
        <p class="section-label">✅ Available</p>
        <div class="staff-grid">
          <div v-for="s in available" :key="s.user_code" class="staff-card available">
            <strong>{{ s.name || s.user_code }}</strong>
            <span>{{ s.user_type }}</span>
            <span v-if="s.shift">{{ s.shift?.start_time }} – {{ s.shift?.end_time }}</span>
          </div>
        </div>
      </div>

      <div v-if="unavailable.length > 0" class="mt-16">
        <p class="section-label">❌ Not Available</p>
        <div class="staff-grid">
          <div v-for="s in unavailable" :key="s.user_code" class="staff-card unavailable">
            <strong>{{ s.name || s.user_code }}</strong>
            <span>{{ s.reason || 'Conflict or no shift' }}</span>
          </div>
        </div>
      </div>

      <div v-if="available.length === 0 && unavailable.length === 0" class="empty">
        No service staff found for selected criteria. Check your schedules.
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

const checking = ref(false)
const checked = ref(false)
const available = ref([])
const unavailable = ref([])
const locations = ref([])
const filters = reactive({ date: '', start_time: '', end_time: '', location_code: '' })

async function checkAvailability() {
  checking.value = true
  checked.value = false
  available.value = []
  unavailable.value = []
  try {
    const params = new URLSearchParams()
    Object.entries(filters).forEach(([k, v]) => { if (v) params.set(k, v) })
    const res = await api.get(`/schedules/check-staff-availability?${params}`)
    const data = res.data.data || {}
    available.value = data.available || []
    unavailable.value = data.unavailable || []
    checked.value = true
  } catch (err) {
    alert(err.response?.data?.message || 'Check failed')
  } finally {
    checking.value = false
  }
}

onMounted(async () => {
  try {
    const res = await api.get('/locations/get-location')
    locations.value = res.data.data || []
  } catch (_) {}
})
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 20px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.card-title { margin: 0 0 16px; font-size: 16px; color: #1e293b; display: flex; align-items: center; gap: 8px; }
.filter-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
.section-label { font-size: 14px; font-weight: 600; color: #374151; margin: 0 0 10px; }
.staff-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
.staff-card { padding: 12px; border-radius: 8px; display: flex; flex-direction: column; gap: 4px; font-size: 13px; }
.staff-card.available { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.staff-card.unavailable { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
.empty { text-align: center; color: #94a3b8; padding: 20px; }
.btn { padding: 9px 18px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; }
.btn:disabled { opacity: 0.6; cursor: not-allowed; }
.btn.primary { background: #3b82f6; color: white; }
.pill { padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.pill.success { background: #dcfce7; color: #166534; }
.pill.danger { background: #fee2e2; color: #991b1b; }
.mt-16 { margin-top: 16px; }
</style>
