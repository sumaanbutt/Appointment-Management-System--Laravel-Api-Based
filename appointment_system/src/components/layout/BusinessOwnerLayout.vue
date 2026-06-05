<template>
  <div class="layout">
    <aside :class="['sidebar', { collapsed }]">
      <div class="logo">
        <span v-if="!collapsed">Business Portal</span>
        <button class="toggle" @click="collapsed = !collapsed">☰</button>
      </div>

      <nav class="menu">
        <router-link to="/owner/dashboard" class="item">
          <i class="icon">📊</i>
          <span v-if="!collapsed">Dashboard</span>
        </router-link>

<!--        Appointments-->

        <div class="group">
          <router-link to="/owner/appointments" class="group-title">
            <i class="icon">📅</i>
            <span v-if="!collapsed">Appointments</span>
          </router-link>
        </div>

<!--        Services-->

        <div class="group">
          <div class="group-title" @click="toggle('svc')">

            <i class="icon">⚕️</i>

            <span v-if="!collapsed">Services</span>

            <i
                v-if="!collapsed"
                class="arrow"
                :class="{ rotated: open.svc }"
            >
              ›
            </i>
          </div>

          <div v-show="open.svc && !collapsed" class="submenu">
            <router-link to="/owner/services" class="sub-item">
              All Services
            </router-link>

            <router-link to="/owner/location-services" class="sub-item">
              Location Services
            </router-link>
          </div>
        </div>

<!--        Location-->

        <div class="group">
          <router-link to="/owner/locations" class="group-title">
            <i class="icon">📍</i>
            <span v-if="!collapsed">Locations</span>
          </router-link>
        </div>

<!--        Staff-->

        <div class="group">
          <router-link to="/owner/staff" class="group-title">
            <i class="icon">👤</i>
            <span v-if="!collapsed">Staff</span>
          </router-link>
        </div>

<!--        Clients-->

        <div class="group">
          <router-link to="/owner/clients" class="group-title">
            <i class="icon">👥</i>
            <span v-if="!collapsed">Clients</span>
          </router-link>
        </div>

<!--        Schedules-->

        <router-link to="/owner/schedules" class="item">
          <i class="icon">🗓️</i>
          <span v-if="!collapsed">Schedules</span>
        </router-link>

<!--        Charges-->

        <router-link to="/owner/charges" class="item">
          <i class="icon">💰</i>
          <span v-if="!collapsed">Charges</span>
        </router-link>

<!--        Invoices-->

        <router-link to="/owner/invoices" class="item">
          <i class="icon">🧾</i>
          <span v-if="!collapsed">Invoices</span>
        </router-link>
      </nav>

<!--      <div class="logout-section">-->
<!--        <div v-if="!collapsed" class="user-info">-->
<!--          <span class="user-name">{{ authStore.user?.name || authStore.user?.email }}</span>-->
<!--          <span class="user-role">{{ authStore.user?.user_type }}</span>-->
<!--        </div>-->
<!--        <button class="logout-btn" @click="handleLogout">-->
<!--          <i class="icon">🚪</i>-->
<!--          <span v-if="!collapsed">Logout</span>-->
<!--        </button>-->
<!--      </div>-->
    </aside>

    <div class="main">
      <Navbar />
      <div class="content">
        <router-view />
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import Navbar from '@/components/layout/Navbar.vue'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const collapsed = ref(false)

const open = reactive({ app: false, svc: false, loc: false, staff: false, client: false })
function toggle(key) { open[key] = !open[key] }

const titleMap = {
  '/owner/dashboard': 'Dashboard',
  '/owner/appointments': 'Appointments',
  '/owner/appointments/create': 'New Appointment',
  '/owner/services': 'Services',
  '/owner/services/create': 'New Service',
  '/owner/locations': 'Locations',
  '/owner/locations/create': 'New Location',
  '/owner/location-services': 'Location Services',
  '/owner/users': 'Staff',
  '/owner/users/create': 'Add Staff',
  '/owner/clients': 'Clients',
  '/owner/clients/create': 'Add Client',
  '/owner/schedules': 'Schedules',
  '/owner/charges': 'Charges',
  '/owner/invoices': 'Invoices',
}
const pageTitle = computed(() => titleMap[route.path] || 'Business Portal')

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.layout { display: flex; }
.sidebar { width: 260px; height: 100vh; background: #0f172a; color: white; display: flex; flex-direction: column; position: sticky; top: 0; transition: 0.3s; overflow-y: auto; }
.sidebar.collapsed { width: 72px; }
.logo { height: 64px; display: flex; align-items: center; justify-content: space-between; padding: 0 16px; font-weight: 700; font-size: 15px; border-bottom: 1px solid #1e293b; flex-shrink: 0; }
.toggle { background: none; border: none; color: white; cursor: pointer; font-size: 18px; }
.menu { padding: 10px 8px; flex: 1; overflow-y: auto; }
.item { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 6px; text-decoration: none; color: #94a3b8; font-size: 13px; font-weight: 500; transition: all 0.15s; }
.item:hover, .item.router-link-active { background: #1e293b; color: white; }
.icon { font-size: 16px; flex-shrink: 0; width: 22px; text-align: center; }
.group { margin-bottom: 2px; }
.group-title { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 6px; cursor: pointer; color: #94a3b8; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.15s; }
.group-title:hover { background: #1e293b; color: white; }
.arrow { font-size: 16px; margin-left: auto; transition: transform 0.2s; }
.arrow.rotated { transform: rotate(90deg); }
.submenu { padding-left: 32px; }
.sub-item { display: block; padding: 7px 10px; border-radius: 5px; text-decoration: none; color: #64748b; font-size: 12px; transition: all 0.15s; }
.sub-item:hover, .sub-item.router-link-active { color: white; background: #1e293b; }
.logout-section { padding: 12px 8px; border-top: 1px solid #1e293b; }
.user-info { padding: 8px 10px 6px; }
.user-name { display: block; font-size: 13px; font-weight: 600; color: #e2e8f0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.user-role { display: block; font-size: 11px; color: #64748b; margin-top: 2px; }
.logout-btn { display: flex; align-items: center; gap: 10px; width: 100%; padding: 9px 10px; border-radius: 6px; background: none; border: none; color: #94a3b8; cursor: pointer; font-size: 13px; transition: all 0.15s; }
.logout-btn:hover { background: #1e293b; color: #f87171; }
.main { flex: 1; background: #f1f5f9; min-height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
.topbar { height: 60px; background: white; display: flex; align-items: center; padding: 0 24px; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
.page-title { margin: 0; font-size: 18px; font-weight: 600; color: #1e293b; }
.content { padding: 20px; flex: 1; }
</style>
