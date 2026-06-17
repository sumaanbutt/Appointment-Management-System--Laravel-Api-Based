import api from '@/services/api'
import { API_CONFIG } from './apiConfig.ts'

export async function apiHandler(section:any, action:any,
     {
       params = {},
       body = {},
       pathParams = {}
       } = {}
) {

    const config = (API_CONFIG as any)?.[section]?.[action]

    if (!config) {
        throw new Error(`API not found: ${section}.${action}`)
    }

    let endpoint = config.endpoint

    Object.entries(pathParams).forEach(([key, value]) => {
        endpoint = endpoint.replace(`:${key}`, value)
    })

    const requestConfig: any = {
        url: endpoint,
        method: config.method,
        params
    }

    if (
        ['POST', 'PUT', 'PATCH'].includes(
            config.method.toUpperCase()
        )
    ) {
        requestConfig.data = body
    }

    return api(requestConfig)
}