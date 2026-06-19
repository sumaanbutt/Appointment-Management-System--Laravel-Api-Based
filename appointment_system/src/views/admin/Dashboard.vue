<template>

  <div class="dashboard">

    <div>
      <h1>Admin Dashboard</h1>
      <p>Welcome Super Admin</p>
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
        <div class="stat-icon client">👥</div>
        <div class="stat-info">
          <p class="stat-label">Clients</p>
          <h3 class="stat-value">{{ stats.clients }}</h3>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon app">📅</div>
        <div class="stat-info">
          <p class="stat-label">Appointments</p>
          <h3 class="stat-value">{{ stats.appointments }}</h3>
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
        <div class="stat-icon svc">⚕️</div>
        <div class="stat-info">
          <p class="stat-label">Services</p>
          <h3 class="stat-value">{{ stats.services }}</h3>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon inv">🧾</div>
        <div class="stat-info">
          <p class="stat-label">Invoices</p>
          <h3 class="stat-value">{{ stats.invoices }}</h3>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon loc">📍</div>
        <div class="stat-info">
          <p class="stat-label">Locations</p>
          <h3 class="stat-value">{{ stats.locations }}</h3>
        </div>
      </div>

    </div>

    <!-- RECENT APPOINTMENTS -->
    <div class="card">
      <div class="card-header">
        <h3>Recent Appointments</h3>
        <router-link to="/appointments" class="view-all">View All</router-link>
      </div>

      <div v-if="loading" class="loading">Loading...</div>

      <table v-else class="table">
        <thead>
        <tr>
          <th>Code</th>
          <th>Client Name</th>
          <th>Service Name</th>
          <th>Date</th>
          <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="appt in recentAppointments" :key="appt.code">
          <td><code>{{ appt.code }}</code></td>
          <td>{{ appt.client?.user?.name || appt.client?.name || '—' }}</td>
          <td>{{ appt.service?.service_name }}</td>
          <td>{{ appt.appointment_start_date || '—' }}</td>
          <td>
            <span :class="['badge',appt.status?.toLowerCase()]">{{ appt.status }}</span>
          </td>
        </tr>
        <tr v-if="recentAppointments.length === 0">
          <td colspan="5" class="empty">No appointments found</td>
        </tr>
        </tbody>
      </table>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api.ts'
import {apiHandler} from "@/services/api/apiHandler.ts";


const loading = ref(true)
const recentAppointments = ref([])

const stats = ref({
  organizations: 0,
  businesses: 0,
  clients: 0,
  appointments: 0,
  users: 0,
  services: 0,
  invoices: 0,
  locations: 0,
})

function getCount(res){

  const response =
      res?.data

  const data =
      response?.data?.data
      ??
      response?.data
      ??
      []

  if(Array.isArray(data))
    return data.length

  if(typeof data==='object')
    return Number(
        data.total
        ??
        data.count
        ??
        0
    )
  return 0
}

// function getCount(res) {
//
//   const response = res?.data
//
//   if (
//       response?.data &&
//       typeof response.data === 'object' &&
//       response.data.total !== undefined
//   ) {
//     return Number(response.data.total)
//   }
//
//   const data =
//       response?.data?.data
//       ??
//       response?.data
//       ??
//       []
//
//   if (Array.isArray(data)) {
//     return data.length
//   }
//
//   return Number(
//       data?.count
//       ??
//       0
//   )
// }


onMounted(async () => {
  try {
    const [orgs, bizs, clients, appts, users, svcs, invs, locs] =
        await Promise.allSettled([

          // api.get('/organizations'),
          // api.get('/businesses'),
          // api.get('/clients'),
          // api.get('/appointments'),
          // api.get('/users'),
          // api.get('/services'),
          // api.get('/invoices'),
          // api.get('/business-locations'),

          apiHandler("organization", "getAllOrganizations",{
            params: {
              data_category: "stats"
            }
          }),
          apiHandler("business", "getAllBusinesses",{
            params: {
              data_category: "stats"
            }
          }),
          apiHandler("client", "getAllClients",{
            params: {
              data_category: "stats"
            }
          }),
          apiHandler("appointment", "getAllAppointments",{
            params: {
              data_category: "stats"
            }
          }),
          apiHandler("user", "getAllUsers",{
            params: {
              data_category: "stats"
            }
          }),
          apiHandler("service", "getAllServices",{
            params: {
              data_category: "stats"
            }
          }),
          apiHandler("invoice", "getAllInvoices",{
            params: {
              data_category: "stats"
            }
          }),
          apiHandler("location", "getAllLocations",{
            params: {
              data_category: "stats"
            }
          }),
        ])

    console.log('APPOINTMENTS RESPONSE:', appts)
    console.log(
        'FIRST APPOINTMENT:',
        appts.value?.data?.data?.data?.[0]
    )

    stats.value.organizations =
        orgs.status === 'fulfilled' ? getCount(orgs.value) : 0

    stats.value.businesses =
        bizs.status === 'fulfilled' ? getCount(bizs.value) : 0

    stats.value.clients =
        clients.status === 'fulfilled' ? getCount(clients.value) : 0

    stats.value.appointments =
        appts.status === 'fulfilled' ? getCount(appts.value) : 0

    stats.value.users =
        users.status === 'fulfilled' ? getCount(users.value) : 0

    stats.value.services =
        svcs.status === 'fulfilled' ? getCount(svcs.value) : 0

    stats.value.invoices =
        invs.status === 'fulfilled' ? getCount(svcs.value) : 0
            // ? (invs.value.data.invoices?.data?.length ||
            //     invs.value.data.invoices?.length ||
            //     0)
            // : 0

    stats.value.locations =
        locs.status === 'fulfilled' ? getCount(locs.value) : 0


    if (appts.status === 'fulfilled') {

      const appointmentsData =
          appts.value?.data?.data?.data ??
          appts.value?.data?.data ??
          appts.value?.data?.appointments ??
          []

      recentAppointments.value =
          Array.isArray(appointmentsData)
              ? appointmentsData.slice(0,5)
              : []
    }

  } finally {

    loading.value = false

  }

})
</script>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.stat-card {
  background: white;
  padding: 20px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: 16px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
}

.stat-icon.org    { background: #ede9fe; }
.stat-icon.biz    { background: #dbeafe; }
.stat-icon.client { background: #dcfce7; }
.stat-icon.app    { background: #fef9c3; }
.stat-icon.usr    { background: #ffe4e6; }
.stat-icon.svc    { background: #e0f2fe; }
.stat-icon.inv    { background: #f0fdf4; }
.stat-icon.loc    { background: #fdf4ff; }

.stat-label {
  margin: 0;
  font-size: 12px;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.stat-value {
  margin: 4px 0 0;
  font-size: 26px;
  font-weight: 700;
  color: #1e293b;
}

.card {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}

.card-header h3 {
  margin: 0;
  font-size: 16px;
  color: #1e293b;
}

.view-all {
  font-size: 13px;
  color: #6366f1;
  text-decoration: none;
}

.view-all:hover {
  text-decoration: underline;
}

.table {
  width: 100%;
  border-collapse: collapse;
}

.table th, .table td {
  text-align: left;
  padding: 10px 12px;
  font-size: 13px;
  border-bottom: 1px solid #f1f5f9;
}

.table th {
  color: #64748b;
  font-weight: 600;
}

.badge {
  padding: 3px 8px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
}

.badge.pending    { background: #fef3c7; color: #d97706; }
.badge.approved   { background: #dcfce7; color: #16a34a; }
.badge.rejected   { background: #fee2e2; color: #dc2626; }
.badge.rescheduled { background: #dbeafe; color: #2563eb; }
.badge.completed  { background: #f0fdf4; color: #15803d; }
.badge.cancelled { background: #e5e7eb; color: #374151; }
.badge.in_progress { background: #dbeafe; color: #2563eb; }


.loading, .empty {
  text-align: center;
  color: #94a3b8;
  padding: 20px;
  font-size: 14px;
}

:global(body.dark-mode) .stat-card {
  background: #1e293b;
}

:global(body.dark-mode) .card {
  background: #1e293b;
}

:global(body.dark-mode) .stat-value,
:global(body.dark-mode) .card-header h3,
:global(body.dark-mode) h1 {
  color: white;
}

:global(body.dark-mode) .stat-label,
:global(body.dark-mode) p,
:global(body.dark-mode) .table th {
  color: #94a3b8;
}

:global(body.dark-mode) .table td {
  color: #e5e7eb;
}

:global(body.dark-mode) .table th,
:global(body.dark-mode) .table td {
  border-bottom: 1px solid #334155;
}
</style>
