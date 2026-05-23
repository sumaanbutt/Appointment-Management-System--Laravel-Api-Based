<template>
  <div class="page">

    <div class="page-header">
      <h2>Browse Services</h2>
      <p class="sub">Explore available services and pricing. Click a service to book.</p>
    </div>

    <!-- FILTER -->
    <div class="filter-bar">
      <input v-model="search" type="text" placeholder="Search services..." class="search-input" />
      <select v-model="locationFilter" class="filter-select">
        <option value="">All Locations</option>
        <option v-for="loc in locations" :key="loc.location_code" :value="loc.location_code">
          {{ loc.name }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="loading">Loading services...</div>
    <div v-else-if="filteredServices.length === 0" class="empty">No services available</div>

    <div v-else class="service-grid">
      <div v-for="svc in filteredServices" :key="svc.service_code" class="service-card">
        <div class="service-header">
          <h3 class="service-name">{{ svc.name }}</h3>
          <span class="service-duration">{{ svc.duration_minutes }} min</span>
        </div>
        <p v-if="svc.description" class="service-desc">{{ svc.description }}</p>

        <!-- Business Charges -->
        <div v-if="svc.charges && svc.charges.length > 0" class="charges">
          <p class="charges-title">Charges</p>
          <div v-for="ch in svc.charges" :key="ch.charge_code" class="charge-row">
            <span>{{ ch.name }}</span>
            <span class="charge-amount">{{ ch.currency || '$' }}{{ ch.amount }}</span>
          </div>
        </div>
        <div v-else class="no-charge">No additional charges</div>

        <!-- Locations -->
        <div class="locations">
          <p class="locations-title">Available At</p>
          <div class="location-chips">
            <span
                v-for="loc in (svc.locations?.length ? svc.locations : locations)"
                :key="loc.location_code || loc"
                class="chip"
            >{{ loc.name || loc }}</span>
            <span v-if="!svc.locations?.length" class="chip all">All Locations</span>
          </div>
        </div>

        <button class="btn-book" @click="bookService(svc)">Book This Service</button>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const loading = ref(true)
const services = ref([])
const locations = ref([])
const search = ref('')
const locationFilter = ref('')

const filteredServices = computed(() => {
  let list = services.value
  if (search.value) {
    const q = search.value.toLowerCase()
    list = list.filter(s => s.name?.toLowerCase().includes(q) || s.description?.toLowerCase().includes(q))
  }
  if (locationFilter.value) {
    list = list.filter(s => {
      if (!s.locations?.length) return true // available at all locations
      return s.locations.some(l => l.location_code === locationFilter.value)
    })
  }
  return list
})

function bookService(svc) {
  router.push({ path: '/client/book', query: { service_code: svc.service_code, service_name: svc.name } })
}

onMounted(async () => {
  try {
    const [svcRes, locRes] = await Promise.all([
      api.get('/services/get-service'),
      api.get('/locations/get-location'),
    ])
    services.value = svcRes.data.data || []
    locations.value = locRes.data.data || []
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 20px; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.filter-bar { display: flex; gap: 12px; }
.search-input { flex: 1; padding: 9px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
.filter-select { padding: 9px 14px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; }
.service-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.service-card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 12px; }
.service-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 8px; }
.service-name { margin: 0; font-size: 16px; font-weight: 700; color: #1e293b; }
.service-duration { background: #dbeafe; color: #1e40af; padding: 3px 10px; border-radius: 20px; font-size: 12px; white-space: nowrap; }
.service-desc { margin: 0; font-size: 13px; color: #64748b; }
.charges { background: #f8fafc; border-radius: 8px; padding: 10px; }
.charges-title, .locations-title { margin: 0 0 6px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; }
.charge-row { display: flex; justify-content: space-between; font-size: 13px; padding: 3px 0; }
.charge-amount { font-weight: 600; color: #1e293b; }
.no-charge { font-size: 12px; color: #94a3b8; }
.locations { }
.location-chips { display: flex; flex-wrap: wrap; gap: 6px; }
.chip { padding: 3px 10px; border-radius: 20px; background: #f1f5f9; color: #374151; font-size: 12px; }
.chip.all { background: #dcfce7; color: #166534; }
.btn-book { background: #3b82f6; color: white; border: none; border-radius: 8px; padding: 10px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: auto; }
.btn-book:hover { background: #2563eb; }
.loading, .empty { text-align: center; padding: 40px; color: #94a3b8; }
</style>
