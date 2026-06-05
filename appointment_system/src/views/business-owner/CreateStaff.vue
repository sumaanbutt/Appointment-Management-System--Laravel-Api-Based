<!--  <template>-->
<!--    <div class="card border-0 shadow-sm rounded-3 max-width-md mx-auto p-4">-->
<!--      <div class="mb-4">-->
<!--        <h3 class="text-dark fw-bold mb-1">Onboard New Staff Member</h3>-->
<!--        <p class="text-muted small">Fill out profile data to add a new operational or service staff member.</p>-->
<!--      </div>-->

<!--      <form @submit.prevent="handleCreateStaffSubmit">-->
<!--        <div class="row g-3">-->

<!--          <div class="col-md-6">-->
<!--            <label class="form-label fw-semibold small text-secondary">Full Name *</label>-->
<!--            <input type="text" v-model="form.name" class="form-control" placeholder="John Doe" required />-->
<!--          </div>-->

<!--          <div class="col-md-6">-->
<!--            <label class="form-label fw-semibold small text-secondary">Email Address</label>-->
<!--            <input type="email" v-model="form.email" class="form-control" placeholder="john.doe@company.com" />-->
<!--          </div>-->

<!--          <div class="col-md-6">-->
<!--            <label class="form-label fw-semibold small text-secondary">Phone Number</label>-->
<!--            <input type="text" v-model="form.phone" class="form-control" placeholder="+1 (555) 000-0000" />-->
<!--          </div>-->

<!--          <div class="col-md-6">-->
<!--            <label class="form-label fw-semibold small text-secondary">System User Type / Role *</label>-->
<!--            <select v-model="form.user_type" class="form-select" required>-->
<!--              <option value="">Select organizational role...</option>-->
<!--              <option value="OPERATION_STAFF">OPERATION STAFF</option>-->
<!--              <option value="SERVICE_STAFF">SERVICE STAFF</option>-->
<!--            </select>-->
<!--          </div>-->

<!--          <div class="col-12" v-if="['OPERATION_STAFF', 'SERVICE_STAFF'].includes(form.user_type)">-->
<!--            <div class="p-3 bg-light rounded-3 border">-->
<!--              <label class="form-label fw-bold text-primary small d-block mb-2">-->
<!--                <i class="bi bi-briefcase me-1"></i> Employment Type Assignment *-->
<!--              </label>-->
<!--              <select v-model="form.employee_type" class="form-select bg-white" required>-->
<!--                <option value="">Choose profile structure...</option>-->
<!--                <option value="PERMANENT">PERMANENT (Full-Time Fixed Core Staff)</option>-->
<!--                <option value="VISITING">VISITING (Part-Time / Seasonal Contractor)</option>-->
<!--                <option value="REMOTE">REMOTE (Off-Site Digital Execution Unit)</option>-->
<!--              </select>-->
<!--            </div>-->
<!--          </div>-->

<!--          <div class="col-md-6">-->
<!--            <label class="form-label fw-semibold small text-secondary">Security Password *</label>-->
<!--            <input type="password" v-model="form.password" class="form-control" placeholder="••••••••" required />-->
<!--          </div>-->

<!--          <div class="col-md-6">-->
<!--            <label class="form-label fw-semibold small text-secondary">Confirm Password *</label>-->
<!--            <input type="password" v-model="form.password_confirmation" class="form-control" placeholder="••••••••" required />-->
<!--          </div>-->

<!--        </div>-->

<!--        <div class="d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">-->
<!--          <button type="button" class="btn btn-light px-4" @click="$emit('cancel')">Cancel</button>-->
<!--          <button type="submit" class="btn btn-primary px-4 fw-semibold" :disabled="loading">-->
<!--            {{ loading ? 'Saving Profile...' : 'Register Profile' }}-->
<!--          </button>-->
<!--        </div>-->
<!--      </form>-->
<!--    </div>-->
<!--  </template>-->

<!--  <script setup>-->
<!--  import { ref } from 'vue'-->
<!--  import { useAuthStore } from '@/stores/auth.store'-->
<!--  import api from '@/services/api'-->

<!--  const emit = defineEmits(['saved', 'cancel'])-->
<!--  const authStore = useAuthStore()-->
<!--  const loading = ref(false)-->

<!--  const form = ref({-->
<!--    name: '',-->
<!--    email: '',-->
<!--    phone: '',-->
<!--    user_type: '',-->
<!--    employee_type: '',-->
<!--    password: '',-->
<!--    password_confirmation: '',-->
<!--    business_code: authStore.user?.business_code || null,-->
<!--    status: 'ACTIVE'-->
<!--  })-->

<!--  async function handleCreateStaffSubmit() {-->
<!--    if (form.value.password !== form.value.password_confirmation) {-->
<!--      alert('Passwords do not match.')-->
<!--      return-->
<!--    }-->

<!--    loading.value = true-->

<!--    // Format the request explicitly to make sure everything maps to your Laravel User model constraints-->
<!--    const payload = {-->
<!--      name: form.value.name,-->
<!--      email: form.value.email || null, // Ensure empty strings pass as proper SQL null types-->
<!--      phone: form.value.phone || null,-->
<!--      user_type: form.value.user_type,-->
<!--      password: form.value.password,-->
<!--      password_confirmation: form.value.password_confirmation, // Needed for Laravel's 'confirmed' password rule hook-->
<!--      organization_code: authStore.user?.organization_code,-->
<!--      business_code: form.value.business_code || authStore.user?.business_code,-->
<!--      status: form.value.status || 'ACTIVE'-->
<!--    }-->

<!--    // Only assign employment structure tracking values if roles apply contextually-->
<!--    if (['OPERATION_STAFF', 'SERVICE_STAFF'].includes(form.value.user_type) && form.value.employee_type) {-->
<!--      payload.employee_type = form.value.employee_type-->
<!--    }-->

<!--    try {-->
<!--      await api.post('/users', payload)-->

<!--      // Reset local state tracking configuration elements after successful completion-->
<!--      form.value = {-->
<!--        name: '',-->
<!--        email: '',-->
<!--        phone: '',-->
<!--        user_type: '',-->
<!--        employee_type: '',-->
<!--        password: '',-->
<!--        password_confirmation: '',-->
<!--        business_code: authStore.user?.business_code || null,-->
<!--        status: 'ACTIVE'-->
<!--      }-->

<!--      emit('saved')-->
<!--    } catch (err) {-->
<!--      console.log(err.response?.data)-->

<!--      alert(-->
<!--          JSON.stringify(-->
<!--              err.response?.data,-->
<!--              null,-->
<!--              2-->
<!--          )-->
<!--      )-->
<!--    } finally {-->
<!--      loading.value = false-->
<!--    }-->
<!--  }-->
<!--  </script>-->



<template>
  <div class="page">
    <div class="page-header">
      <h2>Add Staff</h2>
      <router-link to="/business/staff" class="back-link"><- Back</router-link>
    </div>
    <div class="card">
      <form class="form" @submit.prevent="submit">
        <div class="field"><label>Full Name *</label><input v-model="form.name" placeholder="Enter full name" required /></div>
        <div class="field"><label>Email *</label><input v-model="form.email" type="email" placeholder="Enter email" autocomplete="off" required /></div>
        <div class="field"><label>Phone *</label><input v-model="form.phone" placeholder="Phone number" required /></div>
        <div class="field"><label>Password *</label><input v-model="form.password" type="password" placeholder="Password" autocomplete="new-password" required /></div>
        <div class="field"><label>Confirm Password *</label><input v-model="form.password_confirmation" type="password" placeholder="Confirm password" required/>
        </div>
        <div class="field">
          <label>Role *</label>
          <select v-model="form.user_type" required>
            <option value="">Select role</option>
            <option value="OPERATION_STAFF">Operational Staff</option>
            <option value="SERVICE_STAFF">Service Staff</option>
          </select>
        </div>
        <div class="field">
          <label>Employee Type *</label>
          <select v-model="form.employee_type" required>
            <option value="">Select Employee Type</option>
            <option value="PERMANENT">Permanent</option>
            <option value="VISITING">Visiting</option>
            <option value="REMOTE">Remote</option>
          </select>
        </div>
        <div class="field"><label>Status *</label>
          <select v-model="form.status" required>
            <option value="">Select status</option>
            <option value="ACTIVE">Active</option>
            <option value="INACTIVE">Inactive</option>
          </select>
        </div>
        <p v-if="error" class="error-msg">{{ error }}</p>
        <div class="form-actions">
          <router-link to="/business/staff" class="cancel-btn">Cancel</router-link>
          <button type="submit" class="submit-btn" :disabled="loading">{{ loading ? 'Creating...' : 'Add Staff' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({ name: '', email: '', phone: '', password: '', password_confirmation:'', user_type: '', employee_type: '', status: '' })
const loading = ref(false)
const error = ref('')

async function submit() {
  loading.value = true
  error.value = ''
  try {
    await api.post('/users', { ...form, business_code: authStore.user?.business_code })
    router.push('/business/staff')
  } catch (err) {

    console.log(
        'FULL ERROR:',
        err.response?.data
    )

    if (err.response?.data?.errors) {

      error.value =
          Object.values(
              err.response.data.errors
          )
              .flat()
              .join(', ')

    } else {

      error.value =
          err.response?.data?.message
          || 'Failed to create staff'

    }

  }finally {
    loading.value = false
  }
}
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 16px; }
.page-header { display: flex; align-items: center; justify-content: space-between; }
.page-header h2 { margin: 0; color: #1e293b; }
.back-link { font-size: 14px; color: #6366f1; text-decoration: none; }
.card { background: white; border-radius: 10px; padding: 24px; max-width: 540px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.form { display: flex; flex-direction: column; gap: 16px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; }
.error-msg { color: #ef4444; font-size: 13px; margin: 0; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.cancel-btn { padding: 9px 16px; border-radius: 6px; background: #f1f5f9; color: #374151; text-decoration: none; font-size: 14px; }
.submit-btn { background: #0f172a; color: white; border: none; padding: 9px 20px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; }
</style>