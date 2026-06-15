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

    async function updateRecurrence(id:any, data:any) {
        return recurrenceService.update(id, data)
    }

    async function deleteRecurrence(id:any) {
        return recurrenceService.remove(id)
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