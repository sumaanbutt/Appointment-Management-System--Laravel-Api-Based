<template>
  <div class="page">

    <div class="page-header">
      <h2>New Location</h2>
      <router-link :to="backLink" class="back-link">← Back</router-link>
    </div>

    <div class="card">
      <form class="form" @submit.prevent="submit">

        <div v-if="form.location_type === 'BUSINESS' && isAdmin" class="field">
          <label>Business *</label>
          <select v-model="form.business_code" :class="{ 'field-input-error': errors.business_code }" @change="validateField('business_code')">
            <option value="">Select business</option>
            <option v-for="biz in businesses" :key="biz.code" :value="biz.code">
              {{ biz.name }}
            </option>
          </select>
          <p v-if="errors.business_code" class="field-error">{{ errors.business_code }}</p>
        </div>

        <div class="field">
          <label>Type *</label>
          <select v-model="form.location_type" :class="{ 'field-input-error': errors.location_type }" @change="validateField('location_type')">
            <option value="">Select Type</option>
            <option value="BUSINESS">Business</option>
            <option value="CLIENT">Client</option>
          </select>
          <p v-if="errors.location_type" class="field-error">{{ errors.location_type }}</p>
        </div>

        <div
            v-if="form.location_type === 'CLIENT'"
            class="field">

          <label>Client *</label>

          <select v-model="form.client_code">

            <option value="">
              Select Client
            </option>

            <option
                v-for="client in clients"
                :key="client?.code"
                :value="client?.code">

              {{ client?.name }}

            </option>

          </select>

        </div>

        <div class="field">
          <label>Address</label>
          <input v-model="form.address" placeholder="Address" />
        </div>

        <div class="field">
          <label>Street</label>
          <input v-model="form.street" placeholder="Street" />
        </div>

        <div class="field">
          <label>Apartment</label>
          <input v-model="form.apartment" placeholder="Street" />
        </div>

        <div class="field">
          <label>City</label>
          <input v-model="form.city" placeholder="City" />
        </div>

        <div class="field">
          <label>Province</label>
          <input v-model="form.state" placeholder="Province / State" />
        </div>

        <div class="field">
          <label>Postal Code</label>
          <input v-model="form.postal_code" placeholder="Postal Code" />
        </div>

        <div class="field">
          <label>Country</label>
          <input v-model="form.country" placeholder="Country" />
        </div>

        <p v-if="error" class="error-msg">{{ error }}</p>

        <div class="form-actions">
          <router-link :to="backLink" class="cancel-btn">Cancel</router-link>
          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'Creating...' : 'Create Location' }}
          </button>
        </div>

      </form>
    </div>

  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'
import {apiHandler} from "@/services/api/apiHandler.ts";

const router = useRouter()
const authStore = useAuthStore()
const isAdmin = computed(
    () => authStore.user?.user_type === 'SUPER_ADMIN'
)
console.log('isAdmin', isAdmin.value)
console.log(authStore.user)
const backLink = computed(() => isAdmin.value ? '/admin/locations' : '/owner/locations')

const form = reactive({
  business_code:'',
  client_code:'',
  location_type:'',
  address:'',
  street:'',
  apartment:'',
  city:'',
  state:'',
  postal_code:'',
  country:'',
  status:'active'
})
const businesses = ref([])
const clients = ref([])
const loading = ref(false)
const error = ref('')
const errors = reactive({})

function validateLocationForm(data){

  const errors = {}

  if(!data.business_code)
    errors.business_code =
        'Business is required'

  if(!data.location_type)
    errors.location_type =
        'Location type is required'

  if(!data.address)
    errors.address =
        'Address is required'

  if(!data.street)
    errors.street =
        'Street is required'

  if(!data.city)
    errors.city =
        'City is required'

  if(!data.state)
    errors.state =
        'State is required'

  if(!data.postal_code)
    errors.postal_code =
        'Postal code is required'

  if(!data.country)
    errors.country =
        'Country is required'

  return errors
}

function validateField(field) {
  const result = validateLocationForm(form)
  if (result[field]) { errors[field] = result[field] } else { delete errors[field] }
}

watch(
    () => form.location_type,
    (type) => {

      if (
          type === 'BUSINESS'
          && !isAdmin.value
      ) {
        form.business_code =
            authStore.user?.business_code
      }

      if(type !== 'BUSINESS'){
        form.business_code = ''
      }

      if(type !== 'CLIENT'){
        form.client_code = ''
      }
    }
)

onMounted(async () => {

  if (!isAdmin.value) {
    form.business_code =
        authStore.user?.business_code || ''
  }

  try {

    if (isAdmin.value) {

      const res = await apiHandler('business', 'getAllBusinesses')
      businesses.value =
          res.data.data?.data ||
          res.data.data ||
          []

    }

    const res = await apiHandler('user', 'getAllUsers', {
      params: {
        user_type: 'CLIENT'
      }
    })

    clients.value =
        res.data.data?.data ||
        res.data.data ||
        []

  } catch (e) {
    console.error(e)
  }
})

async function submit() {

  const validationErrors =
      validateLocationForm(form)

  Object.keys(errors)
      .forEach(
          k => delete errors[k]
      )

  Object.assign(
      errors,
      validationErrors
  )

  if(
      Object.keys(errors)
          .length > 0
  ) return

  loading.value = true
  error.value = ''

  try {

    const payload = {
      ...form
    }

    Object.keys(payload)
        .forEach(k => {

          if(!payload[k])
            delete payload[k]

        })

    payload.business_code =
        form.business_code

    console.log(
        'PAYLOAD:',
        payload
    )

    const res = await apiHandler('location', 'createLocation',
        {
          body: payload
        })

    console.log(
        'SUCCESS:',
        res.data
    )

    console.log(
        'REDIRECT:',
        backLink.value
    )

    router.push(backLink.value)


  }

  catch(err){

    console.log(
        err.response?.data
    )

    error.value =
        err.response?.data?.message
        ||
        'Failed to create location'

  }

  finally{

    loading.value = false

  }

}
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 16px; }
.page-header { display: flex; align-items: center; justify-content: space-between; }
.page-header h2 { margin: 0; color: #1e293b; }
.back-link { font-size: 14px; color: #6366f1; text-decoration: none; }
.card { background: white; border-radius: 10px; padding: 24px; max-width: 600px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; }
.field input:focus, .field select:focus { border-color: #6366f1; }
.field-input-error { border-color: #ef4444 !important; }
.field-error { color: #ef4444; font-size: 12px; margin: 2px 0 0; }
.error-msg { color: #ef4444; font-size: 13px; margin: 0; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.cancel-btn { padding: 9px 16px; border-radius: 6px; background: #f1f5f9; color: #64748b; text-decoration: none; font-size: 14px; }
.submit-btn { background: #6366f1; color: white; border: none; padding: 9px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
.submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }
</style>
