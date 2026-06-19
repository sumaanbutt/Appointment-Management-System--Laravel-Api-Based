<template>
  <div class="page">

    <div class="page-header">
      <h2>New Service</h2>
      <router-link :to="backLink" class="back-link">← Back</router-link>
    </div>

    <div class="card">
      <form class="form" @submit.prevent="submit">

        <div v-if="isAdmin" class="field">
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
          <label>Service Name *</label>
          <input v-model="form.service_name" placeholder="Enter service name" :class="{ 'field-input-error': errors.name }" @blur="validateField('service_name')" />
          <p v-if="errors.name" class="field-error">{{ errors.name }}</p>
        </div>

        <div class="field">
          <label>Description</label>
          <textarea v-model="form.description" placeholder="Optional description..." rows="3"></textarea>
        </div>

        <div class="field">
          <label>Price</label>
          <input v-model.number="form.charges" type="number" placeholder="e.g. 50.00" step="0.01" min="0" :class="{ 'field-input-error': errors.price }" @blur="validateField('charges')" />
          <p v-if="errors.price" class="field-error">{{ errors.price }}</p>
        </div>

        <div class="field">
          <label>Cost</label>
          <input v-model.number="form.cost" type="number" placeholder="e.g. 50.00" step="0.01" min="0" />
        </div>

        <div class="field">
          <label>Currency</label>
          <select v-model="form.currency" required>
            <option value="">Select Currency</option>

            <option v-for="currency in currencies" :key="currency" :value="currency">
              {{ currency }}
            </option>
          </select>
        </div>

        <div class="field">
          <label>Duration (value)</label>
          <input v-model.number="form.time_duration" type="number" placeholder="e.g. 30" min="1" :class="{ 'field-input-error': errors.duration_value }" @blur="validateField('time_duration')" />
          <p v-if="errors.duration_value" class="field-error">{{ errors.duration_value }}</p>
        </div>

        <div class="field">
          <label>Duration Unit</label>
          <select v-model="form.duration_uom" :class="{ 'field-input-error': errors.duration_uom }" @change="validateField('duration_uom')">
            <option :value="null">Select Duration Unit</option>
            <option v-for="duration_uom in durationUnits" :key="duration_uom" :value="duration_uom">
              {{ duration_uom }}
            </option>
          </select>
          <p v-if="errors.duration_uom" class="field-error">{{ errors.duration_uom }}</p>
        </div>

        <p v-if="error" class="error-msg">{{ error }}</p>

        <div class="form-actions">
          <router-link :to="backLink" class="cancel-btn">Cancel</router-link>
          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'Creating...' : 'Create Service' }}
          </button>
        </div>

      </form>
    </div>

  </div>
</template>

<script setup>
import { reactive, ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'
import {apiHandler} from "@/services/api/apiHandler.ts";

const router = useRouter()
const authStore = useAuthStore()
const isAdmin = computed(
    () => authStore.user?.user_type === 'SUPER_ADMIN'
)
const backLink = computed(() => isAdmin.value ? '/admin/services' : '/owner/services')

const currencies = ['PKR', 'USD', 'EUR']
const durationUnits = ['WEEK', 'DAY', 'HOUR', 'MINUTE']

const form = reactive({
  business_code:'',
  service_name:'',
  description:'',
  charges:'',
  cost:'',
  currency:'',
  time_duration:null,
  duration_uom:null,
  status:'active'
})

const businesses = ref([])
const loading = ref(false)
const error = ref('')
const errors = reactive({})

function validateServiceForm(data) {

  const errors = {}

  if (!data.business_code)
    errors.business_code =
        'Business is required'

  if (!data.service_name)
    errors.service_name =
        'Service name is required'

  if (
      data.charges !== '' &&
      data.charges < 0
  )
    errors.charges =
        'Price cannot be negative'

  if (
      data.time_duration &&
      data.time_duration <= 0
  )
    errors.time_duration =
        'Duration must be greater than 0'

  return errors
}
function validateField(field) {
  const result = validateServiceForm(form)
  if (result[field]) { errors[field] = result[field] } else { delete errors[field] }
}

onMounted(async () => {
  if (!isAdmin.value) {
    form.business_code = authStore.user?.business_code || ''
    return
  }
  try {
    const res = await apiHandler('business', 'getAllBusinesses')

    businesses.value =
        res.data?.data?.data ??
        res.data?.data ??
        []
  } catch (_) {}
})

async function submit() {

  console.log('SUBMIT CLICKED')

  const validationErrors =
      validateServiceForm(form)

  console.log('VALIDATION:', validationErrors)

  Object.keys(errors).forEach(
      k => delete errors[k]
  )

  Object.assign(errors, validationErrors)

  if (Object.keys(errors).length > 0)
    return

  console.log('PASS VALIDATION')

  loading.value = true
  error.value = ''
  try {
    const payload = { ...form }
    if (!payload.price && payload.price !== 0) delete payload.price
    if (!payload.description) delete payload.description
    if (!payload.cost && payload.cost !== 0) delete payload.cost

    if (!payload.duration_value) {
      delete payload.duration_value
    }

    if (!payload.duration_uom) {
      delete payload.duration_uom
    }
    const res = await apiHandler('service', 'createService',
        {
          body: payload
        })

    router.push(backLink.value)
  } catch (err) {

    console.log(err.response?.data)

    if(err.response?.data?.errors){

      error.value =
          Object.values(
              err.response.data.errors
          )
              .flat()
              .join('\n')

    }else{

      error.value =
          err.response?.data?.error
          || err.response?.data?.message
          || 'Create failed'

    }

  } finally {
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
.field input, .field select, .field textarea { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; font-family: inherit; }
.field input:focus, .field select:focus, .field textarea:focus { border-color: #6366f1; }
.field-input-error { border-color: #ef4444 !important; }
.field-error { color: #ef4444; font-size: 12px; margin: 2px 0 0; }
.error-msg { color: #ef4444; font-size: 13px; margin: 0; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.cancel-btn { padding: 9px 16px; border-radius: 6px; background: #f1f5f9; color: #64748b; text-decoration: none; font-size: 14px; }
.submit-btn { background: #6366f1; color: white; border: none; padding: 9px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
.submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }
</style>
