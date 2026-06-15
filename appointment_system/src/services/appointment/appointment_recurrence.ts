import api from '@/services/api'

export default {
    getAll(params :any) {
        return api.get('/appointment-recurrences',
            {
                params: {
                    include: "business"
                }
            })
    },

    create(data :any) {
        return api.post('/appointment-recurrences', data)
    },

    update(id :any, data :any) {
        return api.put(`/appointment-recurrences`, data)
    },

    remove(id :any) {
        return api.delete(`/appointment-recurrences`)
    }
}