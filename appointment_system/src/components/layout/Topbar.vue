<template>
  <header class="topbar">
    <div class="topbar-left">
      <h3 class="page-title">{{ pageTitle }}</h3>
    </div>
    <div class="topbar-right">
      <span class="user-name">{{ authStore.user?.name || authStore.user?.email || 'Admin' }}</span>
      <span class="user-role">{{ authStore.user?.user_type || 'admin' }}</span>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'

const route = useRoute()
const authStore = useAuthStore()

const pageTitle = computed(() => {
  const map = {
    // Admin
    '/admin/dashboard': 'Dashboard',
    '/admin/organizations': 'Organizations',
    '/admin/organizations/create': 'New Organization',
    '/admin/businesses': 'Businesses',
    '/admin/businesses/create': 'New Business',
    '/admin/users': 'Users',
    '/admin/users/create': 'New User',
    '/admin/invoices': 'Invoices',
    // Business Owner
    '/owner/dashboard': 'Dashboard',
    '/owner/users': 'Staff',
    '/owner/users/create': 'New Staff',
    '/owner/clients': 'Clients',
    '/owner/clients/create': 'New Client',
    '/owner/services': 'Services',
    '/owner/services/create': 'New Service',
    '/owner/locations': 'Locations',
    '/owner/locations/create': 'New Location',
    '/owner/location-services': 'Location Services',
    '/owner/schedules': 'Schedules',
    '/owner/charges': 'Charges',
    '/owner/user-abilities': 'Staff Abilities',
    '/owner/staff-availability': 'Staff Availability',
    '/owner/appointments': 'Appointments',
    '/owner/invoices': 'Invoices',
    // Operational Staff
    '/ops/dashboard': 'Dashboard',
    '/ops/appointments': 'Appointments',
    '/ops/availability': 'Check Availability',
    '/ops/schedules': 'Schedules',
    // Service Staff
    '/staff/dashboard': 'Dashboard',
    '/staff/my-schedule': 'My Schedule',
    '/staff/my-appointments': 'My Appointments',
    // Client
    '/client/dashboard': 'Home',
    '/client/services': 'Browse Services',
    '/client/book': 'Book Appointment',
    '/client/appointments': 'My Appointments',
  }
  return map[route.path] || 'AMS Portal'
})
</script>

<style scoped>
.topbar {
  height: 60px;
  background: white;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 24px;
  border-bottom: 1px solid #e2e8f0;
  flex-shrink: 0;
}

.page-title {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
}

.topbar-right {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.user-name {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}

.user-role {
  font-size: 12px;
  color: #64748b;
  text-transform: capitalize;
}
</style>
