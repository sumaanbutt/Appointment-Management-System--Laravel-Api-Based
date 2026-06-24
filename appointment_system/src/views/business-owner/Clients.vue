<template>
  <div class="page">
    <div class="header">
      <div><h2>Clients</h2><p class="sub">Clients for your business</p></div>
    </div>
    <div class="toolbar">
      <div class="search-bar">
        <input v-model="searchQuery" placeholder="Search by name or email..." />
      </div>

      <router-link to="/owner/clients/create" class="btn btn-ams"> + New Client </router-link>
    </div>
    <div class="card">
      <div v-if="loading" class="loading">Loading...</div>
      <div v-else-if="error" class="error-msg">{{ error }}</div>
      <table v-else class="table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Code</th><th>Status</th></tr></thead>
        <tbody>
          <tr v-for="client in filtered" :key="client.user_code">
            <td>{{ client.user?.name || '—' }}</td>
            <td>{{ client.user?.email || '—' }}</td>
            <td>{{ client.user?.phone || '—' }}</td>
            <td><code>{{ client.code || '—' }}</code></td>
            <td><span :class="['badge',client.user?.status === 'ACTIVE'? 'active': 'inactive']">{{ client.user?.status || 'INACTIVE' }}</span></td>
          </tr>
          <tr v-if="filtered.length === 0"><td colspan="5" class="empty">No clients found</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

const authStore = useAuthStore()
const clients = ref([])
const loading = ref(true)
const error = ref('')
const searchQuery = ref('')

const filtered = computed(() => {

  const q =
      searchQuery.value
          .toLowerCase()
          .trim()

  return clients.value.filter(client => {

    const name =
        client.user?.name
        || ''

    const email =
        client.user?.email
        || ''

    return (
        !q
        ||
        name.toLowerCase().includes(q)
        ||
        email.toLowerCase().includes(q)
    )

  })

})

// const filtered = computed(() => {
//   if (!searchQuery.value.trim()) return clients.value
//   const q = searchQuery.value.toLowerCase()
//   return clients.value.filter(c => (c.name?.toLowerCase().includes(q)) || (c.email?.toLowerCase().includes(q)))
// })

async function fetchClients() {

  loading.value = true
  error.value = ''

  try {
    console.log('CLIENTS:', clients.value)
    console.log('FIRST CLIENT:', clients.value[0])

    const biz = authStore.user?.business_code

    const res = await api.get(
        '/clients',
        {
          params: biz
              ? { business_code: biz }
              : {}
        }
    )

    clients.value =
        res.data?.data?.data
        ??
        res.data?.data
        ??
        []

    console.log('CLIENTS:', clients.value)

  }

  catch (err) {

    console.log(err.response?.data)

    error.value =
        err.response?.data?.message
        ||
        'Failed to load clients'
  }

  finally {

    loading.value = false

  }
}

onMounted(fetchClients)
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 16px; }
.header { display: flex; align-items: center; justify-content: space-between; }
.header h2 { margin: 0; color: #1e293b; }
.sub { margin: 2px 0 0; font-size: 13px; color: #64748b; }
.search-bar input { padding: 9px 14px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; width: 100%; max-width: 320px; }
.card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { text-align: left; padding: 10px 12px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
.table th { color: #64748b; font-weight: 600; }
.loading, .empty { text-align: center; color: #94a3b8; padding: 20px; font-size: 14px; }
.error-msg { color: #ef4444; font-size: 13px; }
.badge { padding: 3px 8px; border-radius: 99px; font-size: 11px; font-weight: 600; }
.badge.active { background: #dcfce7; color: #166534; }
.badge.inactive { background: #fee2e2; color: #991b1b; }
code { font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
.toolbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:16px;
}

.search-bar{
  flex:1;
}

.search-bar input{
  width:100%;
  max-width:320px;
  padding:9px 14px;
  border:1px solid #e2e8f0;
  border-radius:6px;
  font-size:14px;
  outline:none;
}

.btn-ams{
  width:auto;
  white-space:nowrap;
  padding:9px 16px;
  background:#6366f1;
  color:#fff;
  border:none;
  border-radius:6px;
  text-decoration:none;
}

.btn-ams:hover{
  background:#4f46e5;
  color:#fff;
}
</style>
