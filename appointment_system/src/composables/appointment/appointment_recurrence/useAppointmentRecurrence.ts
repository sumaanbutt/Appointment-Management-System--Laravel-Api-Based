import { ref } from 'vue'
import recurrenceService from '@/services/appointment/appointment_recurrence'

export function useAppointmentRecurrence() {

    const recurrences = ref([])
    const loading = ref(false)
    const error = ref('')

    async function fetchRecurrences(params = {}) {
        loading.value = true
        error.value = ''

        try {
            const res = await recurrenceService.getAll(params)

            console.log('API RESPONSE:', res.data)

            recurrences.value = (res.data.data?.data || []).map((mapRecurrence:any) => ({
                ...mapRecurrence,
                business_name: mapRecurrence.business?.name
            }))
        } catch (err:any) {
            error.value = err.response?.data?.message || 'Failed to load recurrences'
        } finally {
            loading.value = false
        }
    }

    async function createRecurrence(data :any) {
        return recurrenceService.create(data)
    }

    async function updateRecurrence(code:any, data:any) {
        return recurrenceService.update(code, data)
    }

    async function deleteRecurrence(code:any) {
        return recurrenceService.remove(code)
    }

    return {
        recurrences,
        loading,
        error,
        fetchRecurrences,
        createRecurrence,
        updateRecurrence,
        deleteRecurrence
    }
}