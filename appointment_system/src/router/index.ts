import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore, ROLES } from '@/stores/auth.store.ts'
import AdminLayout from '@/components/layout/AdminLayout.vue'
import BusinessOwnerLayout from '@/components/layout/BusinessOwnerLayout.vue'
import OperationalStaffLayout from '@/components/layout/OperationalStaffLayout.vue'
import ServiceStaffLayout from '@/components/layout/ServiceStaffLayout.vue'
import ClientLayout from '@/components/layout/ClientLayout.vue'
import LoginView from '@/views/auth/LoginView.vue'
import ForgotPasswordView from '@/views/auth/ForgotPasswordView.vue'
import SettingsView from '@/views/settings/SettingsView.vue'
import NotificationsView from '@/views/notifications/NotificationsView.vue'
import AdminDashboard from "@/views/admin/Dashboard.vue"


// Declare TypeScript definitions for your custom meta properties
declare module 'vue-router' {
    interface RouteMeta {
        requiresAuth?: boolean
        role?: string
    }
}

const routes = [
    { path: '/', redirect: '/dashboard' },
    { path: '/login', name: 'login', component: LoginView },
    { path: '/reset-password', name: 'reset-password', component: ForgotPasswordView },
    { path: '/settings', name: 'settings', component: SettingsView },
    { path: '/notifications', name: 'notifications', component: NotificationsView },

    // FIXED: Changed layout path from '/' to '/admin' to stop route matching conflicts
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true, role: ROLES.ADMIN },
        children: [
            { path: "dashboard", component: AdminDashboard },
            { path: "organizations", component: () => import("@/views/organization/Organizations.vue") },
            { path: "organizations/create", component: () => import("@/views/organization/CreateOrganization.vue") },
            { path: "businesses", component: () => import("@/views/business/Businesses.vue") },
            { path: "businesses/create", component: () => import("@/views/business/CreateBusiness.vue") },
            { path: "businesses/:business_code", component: () => import("@/views/business/BusinessDetail.vue") },
            { path: "clients", component: () => import("@/views/client/Clients.vue") },
            { path: "clients/create", component: () => import("@/views/client/CreateClient.vue") },
            { path: "appointments", component: () => import("@/views/appointments/Appointments.vue") },
            { path: "appointments/create", component: () => import("@/views/appointments/CreateAppointments.vue") },
            { path: "services", component: () => import("@/views/service/Services.vue") },
            { path: "services/create", component: () => import("@/views/service/CreateService.vue") },
            { path: "locations", component: () => import("@/views/location/Locations.vue") },
            { path: "locations/create", component: () => import("@/views/location/CreateLocation.vue") },
            { path: "location-services", component: () => import("@/views/location/LocationServices.vue") },
            { path: "schedules", component: () => import("@/views/schedule/Schedules.vue") },
            { path: "charges", component: () => import("@/views/charge/charges.vue") },
            { path: "invoices", component: () => import("@/views/invoice/Invoices.vue") },
            { path: "users", component: () => import("@/views/users/Users.vue") },
            { path: "users/create", component: () => import("@/views/users/CreateUser.vue") },
            { path: 'settings' , component: SettingsView },
            { path: 'notifications', component: NotificationsView },
        ],
    },

    // BUSINESS OWNER
    {
        path: '/owner',
        component: BusinessOwnerLayout,
        meta: { requiresAuth: true, role: ROLES.BUSINESS_OWNER },
        children: [
            { path: "dashboard", component: () => import("@/views/business-owner/Dashboard.vue") },
            { path: "appointments", component: () => import("@/views/business-owner/Appointments.vue") },
            { path: "appointments/create", component: () => import("@/views/appointments/CreateAppointments.vue") },
            { path: "services", component: () => import("@/views/business-owner/Services.vue") },
            { path: "services/create", component: () => import("@/views/service/CreateService.vue") },
            { path: "locations", component: () => import("@/views/business-owner/Locations.vue") },
            { path: "locations/create", component: () => import("@/views/location/CreateLocation.vue") },
            { path: "location-services", component: () => import("@/views/location/LocationServices.vue") },
            { path: "staff", component: () => import("@/views/business-owner/Staff.vue") },
            { path: "staff/create", component: () => import("@/views/business-owner/CreateStaff.vue") },
            { path: "clients", component: () => import("@/views/business-owner/Clients.vue") },
            { path: "clients/create", component: () => import("@/views/client/CreateClient.vue") },
            { path: "schedules", component: () => import("@/views/schedule/Schedules.vue") },
            { path: "charges", component: () => import("@/views/charge/charges.vue") },
            { path: "invoices", component: () => import("@/views/business-owner/Invoices.vue") },
            { path: 'settings', component: SettingsView },
            { path: 'notifications',component: NotificationsView },
        ],
    },

    // OPERATIONAL STAFF
    {
        path: '/ops',
        component: OperationalStaffLayout,
        meta: { requiresAuth: true, role: ROLES.OPERATION_STAFF },
        children: [
            { path: "dashboard", component: () => import("@/views/operational-staff/Dashboard.vue") },
            { path: "appointments", component: () => import("@/views/operational-staff/Appointments.vue") },
            { path: "pending", component: () => import("@/views/operational-staff/PendingAppointments.vue") },
            { path: "schedules", component: () => import("@/views/operational-staff/Schedules.vue") },
            { path: "availability", component: () => import("@/views/operational-staff/Availability.vue") },
            { path: "clients", component: () => import("@/views/operational-staff/Clients.vue") },
            { path: 'settings', component: SettingsView },
            { path: 'notifications', component: NotificationsView },
        ],
    },

    // SERVICE STAFF
    {
        path: '/staff',
        component: ServiceStaffLayout,
        meta: { requiresAuth: true, role: ROLES.SERVICE_STAFF },
        children: [
            { path: "dashboard", component: () => import("@/views/service-staff/Dashboard.vue") },
            { path: "schedule", component: () => import("@/views/service-staff/MySchedule.vue") },
            { path: "appointments", component: () => import("@/views/service-staff/MyAppointments.vue") },
            { path: 'settings', component: SettingsView },
            { path: 'notifications', component: NotificationsView },
        ],
    },

    // CLIENT
    {
        path: '/client',
        component: ClientLayout,
        meta: { requiresAuth: true, role: ROLES.CLIENT },
        children: [
            { path: "dashboard", component: () => import("@/views/client/Dashboard.vue") },
            { path: "book", component: () => import("@/views/client/BookAppointment.vue") },
            { path: "appointments", component: () => import("@/views/client/MyAppointments.vue") },
            { path: 'settings', component: SettingsView },
            { path: 'notifications', component: NotificationsView },
        ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/login' }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to) => {
    // FIXED: Moved inside the guard loop so it waits until Pinia initializes safely
    const authStore = useAuthStore()

    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        return '/login'
    }

    if (to.path === '/login' && authStore.isAuthenticated) {
        return authStore.homeRoute
    }

    if (to.meta.role && authStore.isAuthenticated) {
        // FIXED: Cleaned up unwrapping logic (.value isn't used on plain Pinia state values)
        const userRoleValue = authStore.userRole && typeof authStore.userRole === 'object' && 'value' in authStore.userRole
            ? (authStore.userRole as any).value
            : authStore.userRole;

        if (userRoleValue !== to.meta.role) {
            return authStore.homeRoute
        }
    }

    return true
})

export default router
