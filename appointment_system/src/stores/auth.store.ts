import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/services/api'

export const ROLES = {
    ADMIN: 'SUPER_ADMIN',
    BUSINESS_OWNER: 'BUSINESS_OWNER',
    OPERATIONAL_STAFF: 'OPERATION_STAFF',
    SERVICE_STAFF: 'SERVICE_STAFF',
    CLIENT: 'CLIENT',
}

export const ROLE_HOME = {
    [ROLES.ADMIN]: '/admin/dashboard',
    [ROLES.BUSINESS_OWNER]: '/owner/dashboard',
    [ROLES.OPERATIONAL_STAFF]: '/ops/dashboard',
    [ROLES.SERVICE_STAFF]: '/staff/dashboard',
    [ROLES.CLIENT]: '/client/dashboard',
}

export const useAuthStore = defineStore('auth', () => {
    const token = ref(localStorage.getItem('token') || null)
    const user = ref(JSON.parse(localStorage.getItem('user') || 'null'))

    const isAuthenticated = computed(() => !!token.value)
    const userRole = computed(() => user.value?.user_type || null)
    const homeRoute = computed(() => ROLE_HOME[userRole.value] || '/login')

    const isAdmin = computed(() => userRole.value === ROLES.ADMIN)
    const isBusinessOwner = computed(() => userRole.value === ROLES.BUSINESS_OWNER)
    const isOperationalStaff = computed(() => userRole.value === ROLES.OPERATIONAL_STAFF)
    const isServiceStaff = computed(() => userRole.value === ROLES.SERVICE_STAFF)
    const isClient = computed(() => userRole.value === ROLES.CLIENT)

    async function login(email:string, password:string){

        const res =
            await api.post('/login', {email, password})

        token.value = res.data.token

        user.value = res.data.user

        localStorage.setItem('token', token.value || '')

        localStorage.setItem('user', JSON.stringify(user.value))

        return res.data
    }

    async function forgotPassword(email:string){

        const res =
            await api.post('/forgot-password',
                {
                    email
                }
            )

        return res.data
    }

    async function logout() {
        try {
            await api.post('/logout')
        } catch (_) {}
        token.value = null
        user.value = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
    }

    return {
        token, user, isAuthenticated, userRole, homeRoute,
        isAdmin, isBusinessOwner, isOperationalStaff, isServiceStaff, isClient,
        login, logout, forgotPassword,
    }
})
