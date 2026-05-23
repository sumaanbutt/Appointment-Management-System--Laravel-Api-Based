<template>
  <div class="page">

    <div class="page-header">
      <h2>New Client</h2>
      <router-link to="/clients" class="back-link">← Back</router-link>
    </div>

    <div class="card">
      <form class="form" @submit.prevent="submit">

        <div class="field">
          <label>Business</label>

          <select v-model="form.business_code">
            <option value="">
              Select Business
            </option>

            <option v-for="biz in businesses" :key="biz.business_code" :value="biz.business_code">
              {{ biz.name }}
            </option>

          </select>
        </div>

        <!-- ADD THIS -->

        <div class="field">
          <label>User *</label>
          <select v-model="form.user_code" required>
            <option value="">
              Select User
            </option>

            <option v-for="user in users" :key="user.code" :value="user.code">
              {{ user.name }} — {{ user.email }}
            </option>

          </select>
        </div>

        <div class="field">
          <label>Address</label>
          <input v-model="form.address" placeholder="Enter address"/>
        </div>

        <!-- ADD THESE -->

        <div class="field">
          <label>City</label>
          <input v-model="form.city" placeholder="Enter city"/>
        </div>

        <div class="field">
          <label>State</label>
          <input v-model="form.state" placeholder="Enter state"/>
        </div>

        <div class="field">
          <label>Country</label>
          <input v-model="form.country" placeholder="Enter country"/>
        </div>

        <p v-if="error" class="error-msg">
          {{ error }}
        </p>

        <div class="form-actions">
          <router-link to="/clients" class="cancel-btn">
            Cancel
          </router-link>

          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'Creating...' : 'Create Client' }}
          </button>
        </div>

      </form>
    </div>

  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'

const router = useRouter()

const form = reactive({
  user_code: '',
  business_code: '',
  address: '',
  city: '',
  state: '',
  country : '',
})
const businesses = ref([])
const users = ref([])
const loading = ref(false)
const error = ref('')

onMounted(async()=>{

  try{
    const [bizRes, usersRes] = await Promise.all([
        api.get('/businesses'),
        api.get('/users')
    ])

    businesses.value = bizRes.data.data.data || []
    users.value = usersRes.data.data.data || []
  }
  catch(_){}
})

async function submit() {
  loading.value = true
  error.value = ''
  try {
    const payload = { ...form, user_type: 'client' }
    if (!payload.business_code) delete payload.business_code
    if (!payload.phone) delete payload.phone
    await api.post('/clients', payload)
    router.push('/clients')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to create client'
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

.card {
  background: white;
  border-radius: 10px;
  padding: 24px;
  max-width: 600px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select {
  padding: 9px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
}
.field input:focus, .field select:focus { border-color: #6366f1; }

.error-msg { color: #ef4444; font-size: 13px; margin: 0; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.cancel-btn {
  padding: 9px 16px;
  border-radius: 6px;
  background: #f1f5f9;
  color: #64748b;
  text-decoration: none;
  font-size: 14px;
}
.submit-btn {
  background: #6366f1;
  color: white;
  border: none;
  padding: 9px 20px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
}
.submit-btn:disabled { opacity: 0.7; cursor: not-allowed; }
</style>
