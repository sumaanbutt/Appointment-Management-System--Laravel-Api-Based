export const API_CONFIG = {
    organization: {
        createOrganization: {
            endpoint: '/organizations',
            method: 'POST',

            requestMapping: {
                title: 'org_title'
            },

            responseMapping: {
                org_title: 'title'
            },

            onSuccess(response:any) {
                console.log('Organization created', response)
            },

            onError(error:any) {
                console.error('Failed', error)
            }
        },
        getAllOrganizations: {
            endpoint: '/organizations',
            method: 'GET'
        },

        updateOrganization: {
            endpoint: '/organizations/:code',
            method: 'PUT'
        },

        deactivateOrganization: {
            endpoint: '/organizations/:code',
            method: 'PATCH'
        },
    },


    business: {
        createBusiness: {
            endpoint: '/businesses',
            method: 'POST',

            requestMapping: {
                title: 'business_title'
            },

            responseMapping: {
                business_title: 'title'
            },

            onSuccess(response:any) {
                console.log('Business created', response)
            },

            onError(error:any) {
                console.error('Failed', error)
            }
        },
        getAllBusinesses: {
            endpoint: '/businesses',
            method: 'GET'
        },

        updateBusiness: {
            endpoint: '/businesses/:code',
            method: 'PUT'
        },

        deactivateBusiness: {
            endpoint: '/businesses/:code',
            method: 'PATCH'
        },
    },

    user: {
        createUser: {
            endpoint: '/users',
            method: 'POST',

            requestMapping: {
                title: 'user_title'
            },

            responseMapping: {
                user_title: 'title'
            },

            onSuccess(response:any) {
                console.log('User created', response)
            },

            onError(error:any) {
                console.error('Failed', error)
            }
        },
        getAllUsers: {
            endpoint: '/users',
            method: 'GET'
        },

        updateUser: {
            endpoint: '/users/:code',
            method: 'PUT'
        },

        deactivateUser: {
            endpoint: '/users/:code',
            method: 'PATCH'
        },
    },

    invoice: {
        createInvoice: {
            endpoint: '/invoices',
            method: 'POST',

            requestMapping: {
                title: 'invoice_title'
            },

            responseMapping: {
                invoice_title: 'title'
            },

            onSuccess(response:any) {
                console.log('Invoice created', response)
            },

            onError(error:any) {
                console.error('Failed', error)
            }
        },
        getAllInvoices: {
            endpoint: '/invoices',
            method: 'GET'
        },

        updateInvoice: {
            endpoint: '/invoices/:code',
            method: 'PUT'
        },

        deactivateInvoice: {
            endpoint: '/invoices/:code',
            method: 'PATCH'
        },
    },

    service: {
        createService: {
            endpoint: '/services',
            method: 'POST',

            requestMapping: {
                title: 'service_title'
            },

            responseMapping: {
                service_title: 'title'
            },

            onSuccess(response:any) {
                console.log('Service created', response)
            },

            onError(error:any) {
                console.error('Failed', error)
            }
        },
        getAllServices: {
            endpoint: '/services',
            method: 'GET'
        },

        updateService: {
            endpoint: '/services/:code',
            method: 'PUT'
        },

        deleteService: {
            endpoint: '/services/:code',
            method: 'DELETE'
        },
    },

    location: {
        createLocation: {
            endpoint: '/business-locations',
            method: 'POST',

            requestMapping: {
                title: 'location_title'
            },

            responseMapping: {
                location_title: 'title'
            },

            onSuccess(response:any) {
                console.log('Location created', response)
            },

            onError(error:any) {
                console.error('Failed', error)
            }
        },
        getAllLocations: {
            endpoint: '/business-locations',
            method: 'GET'
        },

        updateLocation: {
            endpoint: '/business-locations/:code',
            method: 'PUT'
        },

        deleteLocation: {
            endpoint: '/business-locations/:code',
            method: 'DELETE'
        },
    },

    location_service: {
        createLocationService: {
            endpoint: '/location-services',
            method: 'POST',

            requestMapping: {
                title: 'loc_svc_title'
            },

            responseMapping: {
                loc_svc_title: 'title'
            },

            onSuccess(response:any) {
                console.log('Location Service created', response)
            },

            onError(error:any) {
                console.error('Failed', error)
            }
        },
        getAllLocationServices: {
            endpoint: '/location-services',
            method: 'GET'
        },

        updateLocationService: {
            endpoint: '/location-services/:code',
            method: 'PUT'
        },

        deleteLocationService: {
            endpoint: '/location-services/:code',
            method: 'DELETE'
        },
    },

}