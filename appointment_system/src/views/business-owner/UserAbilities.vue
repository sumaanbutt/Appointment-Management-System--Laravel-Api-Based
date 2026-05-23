<template>
  <div class="page">

    <div class="page-header">
      <div>
        <h2>Staff Abilities</h2>
        <p class="sub">Assign permissions to staff members</p>
      </div>
      <button class="btn primary" @click="showForm = true">+ Assign Ability</button>
    </div>

    <!-- TABLE -->
    <div class="card">
      <div v-if="loading" class="loading">Loading...</div>
      <table v-else class="table">
        <thead>
        <tr>
          <th>User Code</th>
          <th>Ability / Action</th>
          <th>Resource</th>
          <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="ab in abilities" :key="ab.id">
          <td><code>{{ ab.user_code }}</code></td>
          <td>{{ ab.ability }}</td>
          <td>{{ ab.resource || '—' }}</td>
          <td>
            <button class="btn-sm danger" @click="deleteAbility(ab)">Remove</button>
          </td>
        </tr>
        <tr v-if="abilities.length === 0">
          <td colspan="4" class="empty">No abilities assigned yet</td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- ASSIGN MODAL -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal">
        <div class="modal-header">
          <h3>Assign Staff Ability</h3>
          <button class="close" @click="showForm = false">✕</button>
        </div>
        <form class="form" @submit.prevent="submitAbility">
          <div class="field">
            <label>Staff Member *</label>
            <select v-model="form.user_code" required>
              <option value="">Select staff...</option>
              <option v-for="u in staffUsers" :key="u.user_code" :value="u.user_code">
                {{ u.name || u.email }} ({{ u.user_type }})
              </option>
            </select>
          </div>
          <div class="field">
            <label>Ability / Action *</label>
            <select v-model="form.ability" required>
              <option value="">Select ability...</option>
              <option value="approve_appointment">Approve Appointment</option>
              <option value="reject_appointment">Reject Appointment</option>
              <option value="reschedule_appointment">Reschedule Appointment</option>
              <option value="view_appointments">View Appointments</option>
              <option value="manage_clients">Manage Clients</option>
              <option value="manage_schedules">Manage Schedules</option>
            </select>
          </div>
          <div class="field">
            <label>Resource (optional)</label>
            <input v-model="form.resource" placeholder="e.g. specific location or service code" />
          </div>
          <p v-if="formError" class="error-msg">{{ formError }}</p>
          <div class="form-actions">
            <button type="button" class="btn secondary" @click="showForm = false">Cancel</button>
            <button type="submit" class="btn primary" :disabled="saving">
              {{ saving ? 'Saving...' : 'Assign' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

const loading = ref(true)
const saving = ref(false)
const showForm = ref(false)
const abilities = ref([])
const staffUsers = ref([])
const formError = ref('')

const form = reactive({ user_code: '', ability: '', resource: '' })

async function fetchData() {
  loading.value = true
  try {
    const [absRes, usersRes] = await Promise.allSettled([
      api.get('/user-abilities'),
      api.get('/users'),
    ])
    if (absRes.status === 'fulfilled') abilities.value = absRes.value.data.data || []
    if (usersRes.status === 'fulfilled') {
      staffUsers.value = (usersRes.value.data.data || []).filter(
          u => u.user_type === 'OPERATION_STAFF' || u.user_type === 'SERVICE_STAFF'
      )
    }
  } finally {
    loading.value = false
  }
}

async function submitAbility() {
  saving.value = true
  formError.value = ''
  try {
    await api.post('/user-abilities', form)
    showForm.value = false
    form.user_code = ''
    form.ability = ''
    form.resource = ''
    await fetchData()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Failed to assign ability'
  } finally {
    saving.value = false
  }
}

async function deleteAbility(ability){
  if(!confirm('Remove this ability?'))
    return
  try{
    await api.delete(`/user-abilities/${ability.user_ability_code}`)

    abilities.value = abilities.value.filter(
            a => a.user_ability_code !== ability.user_ability_code
        )
  }
  catch(err){
    alert(err.response?.data?.message || 'Failed to remove')
  }
}

onMounted(fetchData)
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 20px; }
.page-header { display: flex; align-items: flex-start; justify-content: space-between; }
.page-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
.sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.card { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); }
.table { width: 100%; border-collapse: collapse; }
.table th, .table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
.table th { color: #64748b; font-weight: 600; font-size: 12px; text-transform: uppercase; }
.empty { text-align: center; color: #94a3b8; padding: 20px !important; }
.loading { text-align: center; padding: 20px; color: #94a3b8; }
.btn { padding: 9px 18px; border-radius: 8px; border: none; cursor: pointer; font-size: 14px; font-weight: 500; text-decoration: none; }
.btn.primary { background: #3b82f6; color: white; }
.btn.secondary { background: #f1f5f9; color: #374151; border: 1px solid #e2e8f0; }
.btn-sm { padding: 4px 10px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; }
.btn-sm.danger { background: #fee2e2; color: #991b1b; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; z-index: 1000; }
.modal { background: white; border-radius: 12px; width: 500px; max-width: 95vw; padding: 24px; }
.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.modal-header h3 { margin: 0; }
.close { background: none; border: none; font-size: 18px; cursor: pointer; color: #64748b; }
.form { display: flex; flex-direction: column; gap: 14px; }
.field { display: flex; flex-direction: column; gap: 6px; }
.field label { font-size: 13px; font-weight: 600; color: #374151; }
.field input, .field select { padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; }
.form-actions { display: flex; gap: 10px; justify-content: flex-end; }
.error-msg { color: #ef4444; font-size: 13px; }
</style>
