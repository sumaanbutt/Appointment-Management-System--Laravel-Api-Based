<template>
  <aside :class="['sidebar', { collapsed }]">

    <!-- Logo -->
    <div class="logo">
      <span v-if="!collapsed">AMS Portal</span>
      <button class="toggle" @click="collapsed = !collapsed">☰</button>
    </div>

    <!-- Role Badge -->
    <div v-if="!collapsed" class="role-badge" :class="userRole">
      {{ roleLabel }}
    </div>

    <!-- Menu -->
    <nav class="menu">

      <!-- ── ADMIN MENU ── -->
      <template v-if="isAdmin">
        <router-link to="/admin/dashboard" class="item">
          <i class="icon">📊</i><span v-if="!collapsed">Dashboard</span>
        </router-link>

        <div class="group">
          <div class="group-title" @click="toggle('org')">
            <i class="icon">🏢</i>
            <span v-if="!collapsed">Organizations</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.org }">›</i>
          </div>
          <div v-show="open.org && !collapsed" class="submenu">
            <router-link to="/admin/organizations" class="sub-item">All Organizations</router-link>
            <router-link to="/admin/organizations/create" class="sub-item">New Organization</router-link>
          </div>
        </div>

        <div class="group">
          <div class="group-title" @click="toggle('biz')">
            <i class="icon">🏪</i>
            <span v-if="!collapsed">Businesses</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.biz }">›</i>
          </div>
          <div v-show="open.biz && !collapsed" class="submenu">
            <router-link to="/admin/businesses" class="sub-item">All Businesses</router-link>
            <router-link to="/admin/businesses/create" class="sub-item">New Business</router-link>
          </div>
        </div>

        <div class="group">
          <div class="group-title" @click="toggle('usr')">
            <i class="icon">👤</i>
            <span v-if="!collapsed">Users</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.usr }">›</i>
          </div>
          <div v-show="open.usr && !collapsed" class="submenu">
            <router-link to="/admin/users" class="sub-item">All Users</router-link>
            <router-link to="/admin/users/create" class="sub-item">New User</router-link>
          </div>
        </div>

<!--        <router-link to="/admin/invoices" class="item">-->
<!--          <i class="icon">🧾</i><span v-if="!collapsed">Invoices</span>-->
<!--        </router-link>-->
      </template>

      <!-- ── BUSINESS OWNER MENU ── -->
      <template v-else-if="isBusinessOwner">
        <router-link to="/owner/dashboard" class="item">
          <i class="icon">📊</i><span v-if="!collapsed">Dashboard</span>
        </router-link>

        <div class="group">
          <div class="group-title" @click="toggle('usr')">
            <i class="icon">👤</i>
            <span v-if="!collapsed">Staff</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.usr }">›</i>
          </div>
          <div v-show="open.usr && !collapsed" class="submenu">
            <router-link to="/owner/users" class="sub-item">All Staff</router-link>
            <router-link to="/owner/users/create" class="sub-item">Add Staff</router-link>
          </div>
        </div>

        <div class="group">
          <div class="group-title" @click="toggle('client')">
            <i class="icon">👥</i>
            <span v-if="!collapsed">Clients</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.client }">›</i>
          </div>
          <div v-show="open.client && !collapsed" class="submenu">
            <router-link to="/owner/clients" class="sub-item">All Clients</router-link>
            <router-link to="/owner/clients/create" class="sub-item">New Client</router-link>
          </div>
        </div>

        <div class="group">
          <div class="group-title" @click="toggle('svc')">
            <i class="icon">⚕️</i>
            <span v-if="!collapsed">Services</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.svc }">›</i>
          </div>
          <div v-show="open.svc && !collapsed" class="submenu">
            <router-link to="/owner/services" class="sub-item">All Services</router-link>
            <router-link to="/owner/services/create" class="sub-item">New Service</router-link>
          </div>
        </div>

        <div class="group">
          <div class="group-title" @click="toggle('loc')">
            <i class="icon">📍</i>
            <span v-if="!collapsed">Locations</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.loc }">›</i>
          </div>
          <div v-show="open.loc && !collapsed" class="submenu">
            <router-link to="/owner/locations" class="sub-item">All Locations</router-link>
            <router-link to="/owner/locations/create" class="sub-item">New Location</router-link>
            <router-link to="/owner/location-services" class="sub-item">Location Services</router-link>
          </div>
        </div>

        <router-link to="/owner/schedules" class="item">
          <i class="icon">🗓️</i><span v-if="!collapsed">Schedules</span>
        </router-link>

        <router-link to="/owner/charges" class="item">
          <i class="icon">💰</i><span v-if="!collapsed">Charges</span>
        </router-link>

        <router-link to="/owner/user-abilities" class="item">
          <i class="icon">🔑</i><span v-if="!collapsed">Staff Abilities</span>
        </router-link>

        <router-link to="/owner/staff-availability" class="item">
          <i class="icon">🔍</i><span v-if="!collapsed">Staff Availability</span>
        </router-link>

        <div class="group">
          <div class="group-title" @click="toggle('app')">
            <i class="icon">📅</i>
            <span v-if="!collapsed">Appointments</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.app }">›</i>
          </div>
          <div v-show="open.app && !collapsed" class="submenu">
            <router-link to="/owner/appointments" class="sub-item">All Appointments</router-link>
          </div>
        </div>

        <router-link to="/owner/invoices" class="item">
          <i class="icon">🧾</i><span v-if="!collapsed">Invoices</span>
        </router-link>
      </template>

      <!-- ── OPERATIONAL STAFF MENU ── -->
      <template v-else-if="isOperationalStaff">
        <router-link to="/ops/dashboard" class="item">
          <i class="icon">📊</i><span v-if="!collapsed">Dashboard</span>
        </router-link>

        <div class="group">
          <div class="group-title" @click="toggle('app')">
            <i class="icon">📅</i>
            <span v-if="!collapsed">Appointments</span>
            <i v-if="!collapsed" class="arrow" :class="{ rotated: open.app }">›</i>
          </div>
          <div v-show="open.app && !collapsed" class="submenu">
            <router-link to="/ops/appointments" class="sub-item">All Appointments</router-link>
          </div>
        </div>

        <router-link to="/ops/availability" class="item">
          <i class="icon">🔍</i><span v-if="!collapsed">Check Availability</span>
        </router-link>

        <router-link to="/ops/schedules" class="item">
          <i class="icon">🗓️</i><span v-if="!collapsed">Schedules</span>
        </router-link>
      </template>

      <!-- ── SERVICE STAFF MENU ── -->
      <template v-else-if="isServiceStaff">
        <router-link to="/staff/dashboard" class="item">
          <i class="icon">📊</i><span v-if="!collapsed">Dashboard</span>
        </router-link>

        <router-link to="/staff/my-schedule" class="item">
          <i class="icon">🗓️</i><span v-if="!collapsed">My Schedule</span>
        </router-link>

        <router-link to="/staff/my-appointments" class="item">
          <i class="icon">📅</i><span v-if="!collapsed">My Appointments</span>
        </router-link>
      </template>

      <!-- ── CLIENT MENU ── -->
      <template v-else-if="isClient">
        <router-link to="/client/dashboard" class="item">
          <i class="icon">🏠</i><span v-if="!collapsed">Home</span>
        </router-link>

        <router-link to="/client/services" class="item">
          <i class="icon">⚕️</i><span v-if="!collapsed">Browse Services</span>
        </router-link>

        <router-link to="/client/appointments" class="item">
          <i class="icon">📅</i><span v-if="!collapsed">My Appointments</span>
        </router-link>
      </template>

    </nav>

    <!-- LOGOUT -->
    <div class="logout-section">
      <button class="logout-btn" @click="handleLogout">
        <i class="icon">🚪</i>
        <span v-if="!collapsed">Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store.ts'

const router = useRouter()
const authStore = useAuthStore()

const collapsed = ref(false)

const isAdmin = computed(() => authStore.isAdmin)
const isBusinessOwner = computed(() => authStore.isBusinessOwner)
const isOperationalStaff = computed(() => authStore.isOperationalStaff)
const isServiceStaff = computed(() => authStore.isServiceStaff)
const isClient = computed(() => authStore.isClient)
const userRole = computed(() => authStore.userRole)

const ROLE_LABELS = {
  admin: 'Administrator',
  business_owner: 'Business Owner',
  operational_staff: 'Operational Staff',
  service_staff: 'Service Staff',
  client: 'Client',
}
const roleLabel = computed(() => ROLE_LABELS[userRole.value] || userRole.value)

const open = reactive({
  org: false, biz: false, app: false, client: false, svc: false, loc: false, usr: false,
})

function toggle(key) {
  open[key] = !open[key]
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.sidebar {
  width: 260px;
  height: 100vh;
  background: #1e293b;
  color: white;
  transition: 0.3s;
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
  overflow-y: auto;
}
.sidebar.collapsed { width: 72px; }

.logo {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 16px;
  font-weight: 700;
  font-size: 16px;
  border-bottom: 1px solid #334155;
  flex-shrink: 0;
}
.toggle {
  background: none;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 18px;
}

.role-badge {
  margin: 8px 12px;
  padding: 4px 10px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-align: center;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.role-badge.admin            { background: #7c3aed; color: #ede9fe; }
.role-badge.business_owner   { background: #0369a1; color: #e0f2fe; }
.role-badge.operational_staff{ background: #065f46; color: #d1fae5; }
.role-badge.service_staff    { background: #92400e; color: #fef3c7; }
.role-badge.client           { background: #1e40af; color: #dbeafe; }

.menu {
  padding: 10px 8px;
  overflow-y: auto;
  flex: 1;
}

.item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  cursor: pointer;
  border-radius: 6px;
  color: #94a3b8;
  text-decoration: none;
  font-size: 14px;
  transition: background 0.2s, color 0.2s;
  margin-bottom: 2px;
}
.item:hover { background: #334155; color: white; }
.item.router-link-active { background: #334155; color: #60a5fa; }

.group { margin-bottom: 2px; }
.group-title {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  cursor: pointer;
  border-radius: 6px;
  color: #94a3b8;
  font-size: 14px;
  transition: background 0.2s, color 0.2s;
}
.group-title:hover { background: #334155; color: white; }

.submenu { margin-left: 28px; display: flex; flex-direction: column; }
.sub-item {
  padding: 7px 8px;
  font-size: 13px;
  color: #94a3b8;
  text-decoration: none;
  border-radius: 4px;
  transition: color 0.2s;
}
.sub-item:hover { color: white; }
.sub-item.router-link-active { color: #60a5fa; font-weight: 600; }

.arrow { margin-left: auto; transition: transform 0.2s; display: inline-block; }
.arrow.rotated { transform: rotate(90deg); }

.icon { width: 20px; text-align: center; }

.logout-section { padding: 12px 8px; border-top: 1px solid #334155; }
.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 9px 10px;
  background: none;
  border: none;
  color: #f87171;
  cursor: pointer;
  border-radius: 6px;
  font-size: 14px;
  transition: background 0.2s;
}
.logout-btn:hover { background: #334155; }
</style>

