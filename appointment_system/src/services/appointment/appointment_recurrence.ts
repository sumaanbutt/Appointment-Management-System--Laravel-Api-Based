import api from '@/services/api'

export default {
    getAll(params :any = {}) {
        return api.get('/appointment-recurrences',
            {
                params
            })
    },

    create(data :any) {
        return api.post('/appointment-recurrences', data)
    },

    update(code :any, data :any) {
        return api.put(`/appointment-recurrences/${code}`, data)    },

    remove(code :any) {
        return api.delete(`/appointment-recurrences/${code}`)
    }
}