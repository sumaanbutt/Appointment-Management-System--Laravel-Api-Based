import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore, ROLES } from '@/stores/auth.store.ts'
import AppLayout from '@/components/layout/AdminLayout.vue'
import LoginView from '@/views/auth/LoginView.vue'
import ForgotPasswordView from '@/views/auth/ForgotPasswordView.vue'
import SettingsView from '@/views/settings/SettingsView.vue'
import NotificationsView from '@/views/notifications/NotificationsView.vue'
import AdminDashboard from "@/views/admin/AdminDashboard.vue";


const routes = [

    {
        path:'/',
        redirect:'/login'
    },

    {
        path:'/login',
        name:'login',
        component:LoginView
    },

    {
        path:'/reset-password',
        name:'reset-password',
        component:ForgotPasswordView
    },

    {
        path:'/settings',
        name:'settings',
        component:SettingsView
    },

    {
        path:'/notifications',
        name:'notifications',
        component:NotificationsView
    },

// ADMIN

    {
        path:'/admin',
        component:AppLayout,
        meta:{requiresAuth:true, role:ROLES.ADMIN},

        children: [
            { path: "dashboard", component: () => import("@/views/admin/AdminDashboard.vue") },
            { path: "organizations", component: () => import("@/views/organization/Organizations.vue") },
            { path: "organizations/create", component: () => import("@/views/organization/CreateOrganization.vue") },
            { path: "businesses", component: () => import("@/views/business/Businesses.vue") },
            { path: "businesses/create", component: () => import("@/views/business/CreateBusiness.vue") },
            { path: "businesses/:business_code", component: () => import("@/views/business/BusinessDetail.vue") },
            { path: "users", component: () => import("@/views/users/Users.vue") },
            { path: "users/create", component: () => import("@/views/users/CreateUser.vue") },
            // { path: "invoices", component: () => import("@/views/invoice/Invoices.vue") },
        ],
    },

// BUSINESS OWNER

    {
        path:'/owner',
        component:AppLayout,
        meta:{requiresAuth:true, role:ROLES.BUSINESS_OWNER},

        children: [
            { path: "dashboard", component: () => import("@/views/business-owner/BODashboard.vue") },
            { path: "users", component: () => import("@/views/users/Users.vue") },
            { path: "users/create", component: () => import("@/views/users/CreateUser.vue") },
            { path: "clients", component: () => import("@/views/client/Clients.vue") },
            { path: "clients/create", component: () => import("@/views/client/CreateClient.vue") },
            { path: "services", component: () => import("@/views/service/Services.vue") },
            { path: "services/create", component: () => import("@/views/service/CreateService.vue") },
            { path: "locations", component: () => import("@/views/location/Locations.vue") },
            { path: "locations/create", component: () => import("@/views/location/CreateLocation.vue") },
            { path: "location-services", component: () => import("@/views/location/LocationServices.vue") },
            { path: "schedules", component: () => import("@/views/schedule/Schedules.vue") },
            { path: "charges", component: () => import("@/views/charge/charges.vue") },
            { path: "user-abilities", component: () => import("@/views/business-owner/UserAbilities.vue") },
            { path: "staff-availability", component: () => import("@/views/business-owner/StaffAvailability.vue") },
            { path: "appointments", component: () => import("@/views/appointments/Appointments.vue") },
            { path: "invoices", component: () => import("@/views/invoice/Invoices.vue") },
        ],
    },

// OPERATIONAL STAFF

    {
        path:'/ops',
        component:AppLayout,
        meta:{requiresAuth:true, role:ROLES.OPERATIONAL_STAFF},

        children: [
            { path: "dashboard", component: () => import("@/views/operational-staff/OSDashboard.vue") },
            { path: "appointments", component: () => import("@/views/appointments/Appointments.vue") },
            { path: "availability", component: () => import("@/views/operational-staff/StaffAvailability.vue") },
            { path: "schedules", component: () => import("@/views/schedule/Schedules.vue") },
        ],
    },

// SERVICE STAFF

    {
        path:'/staff',
        component:AppLayout,
        meta:{requiresAuth:true, role:ROLES.SERVICE_STAFF},

        children: [
            { path: "dashboard", component: () => import("@/views/service-staff/SSDashboard.vue") },
            { path: "my-schedule", component: () => import("@/views/service-staff/MySchedule.vue") },
            { path: "my-appointments", component: () => import("@/views/service-staff/MyAppointments.vue") },
        ],
    },

// CLIENT

    {
        path:'/client',
        component:AppLayout,
        meta:{requiresAuth:true, role:ROLES.CLIENT},

        children: [
            { path: "dashboard", component: () => import("@/views/client-portal/ClientDashboard.vue") },
            { path: "services", component: () => import("@/views/client-portal/ServiceBrowse.vue") },
            { path: "book", component: () => import("@/views/client-portal/BookAppointment.vue") },
            { path: "appointments", component: () => import("@/views/client-portal/MyAppointments.vue") },
        ],
    },

    {
        path:'/:pathMatch(.*)*',
        redirect:'/login'
    }

]


const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach(

    (to,_from,next)=>{

        const authStore =
            useAuthStore()

        if(to.meta.requiresAuth && !authStore.isAuthenticated){

            return next('/login')
        }

        if(to.path === '/login' && authStore.isAuthenticated){

            return next(authStore.homeRoute)
        }

        if(to.meta.role && authStore.userRole !== to.meta.role){

            if(authStore.isAuthenticated){

                return next(
                    authStore.homeRoute
                )
            }

            return next('/login')
        }

        next()

    })

export default router