import api from './api'

export function loginUser(data:any){

    return api.post(
        '/login',
        data
    )

}