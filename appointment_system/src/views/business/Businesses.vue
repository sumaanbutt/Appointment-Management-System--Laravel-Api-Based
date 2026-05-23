<template>
  <div class="page">

    <!-- HEADER -->
    <div class="header">
      <div>
        <h2>Businesses</h2>
        <p class="sub">Manage all businesses</p>
      </div>
      <router-link to="/businesses/create" class="btn">+ New Business</router-link>
    </div>


    <div class="card">
      <div v-if="loading" class="loading">Loading...</div>
      <div v-else-if="error" class="error-msg">{{ error }}</div>

      <table v-else class="table">
        <thead>
        <tr>
          <th>Name</th>
          <th>Code</th>
          <th>Organization</th>
          <th>Status</th>
          <th width="180">Actions</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="biz in businesses" :key="biz.code">
          <td>{{ biz.name }}</td>
          <td><code>{{ biz.code }}</code></td>
          <td>{{ biz.organization_name || biz.organization_code || '—' }}</td>
          <td>
            <span :class="['badge', biz.status]">{{ biz.status }}</span>
          </td>
          <td>
            <router-link :to="`/businesses/${biz.code}`" class="view-btn">View</router-link>
            <button class="edit-btn" @click="openEdit(biz)">Edit</button>
            <button class="delete-btn" @click="openDelete(biz)">Delete</button>
          </td>
        </tr>
        <tr v-if="businesses.length === 0">
          <td colspan="5" class="empty">No businesses found</td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" class="biz-modal-overlay">
      <div class="biz-modal">
    <div class="modal-header">
          <h3>Edit Business</h3>
          <button class="close" @click="showEditModal = false">✕</button>
        </div>
        <form class="form" @submit.prevent="updateBusiness">
          <input v-model="editForm.name" placeholder="Business Name" required />
          <input v-model="editForm.email" placeholder="Email"/>
          <input v-model="editForm.phone" placeholder="Phone"/>
          <textarea v-model="editForm.description" placeholder="Description"/>
          <input v-model="editForm.timezone" placeholder="Timezone"/>
          <select v-model="editForm.status">
            <option value="ACTIVE">Active</option>
            <option value="INACTIVE">Inactive</option>
          </select>
          <p v-if="formError" class="error-msg">{{ formError }}</p>
          <button type="submit" class="save-btn" :disabled="saving">
            {{ saving ? 'Saving...' : 'Save Changes' }}
          </button>
        </form>
      </div>
    </div>

    <!-- DELETE MODAL -->
    <div v-if="showDeleteModal" class="biz-modal-overlay">
      <div class="biz-modal delete-modal">
        <h3>Delete Business</h3>
        <p>Are you sure you want to delete <strong>{{ selected?.name }}</strong>?</p>
        <div class="actions">
          <button class="cancel-btn" @click="showDeleteModal = false">Cancel</button>
          <button class="delete-confirm-btn" @click="deleteBusiness" :disabled="saving">
            {{ saving ? 'Deleting...' : 'Delete' }}
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '@/services/api'

const businesses = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')

const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selected = ref(null)

const editForm = reactive({ organization_code:'', name:'', email:'', phone:'', description:'', timezone:'', status:'ACTIVE'})

async function fetchBusinesses() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get('/businesses')
    businesses.value = res.data.data.data || []
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load businesses'
  } finally {
    loading.value = false
  }
}

function openEdit(biz){
  console.log(
      'BUSINESS EDIT',
      biz
  )
  selected.value = biz
  editForm.organization_code = biz.organization_code || ''
  editForm.name = biz.name || ''
  editForm.email = biz.email || ''
  editForm.phone = biz.phone || ''
  editForm.description = biz.description || ''
  editForm.timezone = biz.timezone || ''
  editForm.status = biz.status || 'ACTIVE'
  formError.value=''
  showEditModal.value=true
}

function openDelete(biz) {
  console.log(
      'BUSINESS DELETE',
      biz
  )
  selected.value = biz
  showDeleteModal.value = true
}

async function updateBusiness() {
  saving.value = true
  formError.value = ''
  try {
    await api.put(`/businesses/${selected.value.code}`, editForm)
    showEditModal.value = false
    await fetchBusinesses()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Update failed'
  } finally {
    saving.value = false
  }
}

async function deleteBusiness() {
  saving.value = true
  try {
    await api.delete(`/businesses/${selected.value.code}`)
    showDeleteModal.value = false
    await fetchBusinesses()
  } catch (err) {
    error.value = err.response?.data?.message || 'Delete failed'
  } finally {
    saving.value = false
  }
}

onMounted(fetchBusinesses)
</script>

<style scoped>
.page { display: flex; flex-direction: column; gap: 16px; }

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.header h2 { margin: 0; color: #1e293b; }
.sub { margin: 2px 0 0; font-size: 13px; color: #64748b; }

.btn {
  background: #6366f1;
  color: white;
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
}

.card {
  background: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}

.table { width: 100%; border-collapse: collapse; }
.table th, .table td {
  text-align: left;
  padding: 10px 12px;
  font-size: 13px;
  border-bottom: 1px solid #f1f5f9;
}
.table th { color: #64748b; font-weight: 600; }

.badge {
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: capitalize;
}
.badge.ACTIVE   { background: #dcfce7; color: #16a34a; }
.badge.INACTIVE { background: #fee2e2; color: #dc2626; }

.view-btn {
  background: #e0f2fe;
  color: #0284c7;
  padding: 5px 10px;
  border-radius: 5px;
  text-decoration: none;
  font-size: 12px;
  margin-right: 4px;
}
.edit-btn, .delete-btn {
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 12px;
  margin-right: 4px;
}
.edit-btn   { background: #ede9fe; color: #6366f1; }
.delete-btn { background: #fee2e2; color: #dc2626; }

.loading, .empty { text-align: center; color: #94a3b8; padding: 20px; font-size: 14px; }
.error-msg { color: #ef4444; font-size: 13px; margin: 0; }

.biz-modal-overlay{

  position:fixed;
  inset:0;

  background:rgba(0,0,0,.5);

  display:flex;

  align-items:center;

  justify-content:center;

  z-index:99999;

}

.biz-modal{

  background:white;

  padding:24px;

  border-radius:12px;

  width:520px;

  max-width:95vw;

}
.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
}
.modal-header h3 { margin: 0; }
.close { background: none; border: none; font-size: 18px; cursor: pointer; color: #64748b; }

.form { display: flex; flex-direction: column; gap: 12px; }
.form input, .form select {
  padding: 9px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  outline: none;
}
.save-btn {
  background: #6366f1;
  color: white;
  border: none;
  padding: 10px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}

.delete-modal { text-align: center; }
.delete-modal h3 { margin: 0 0 12px; }
.delete-modal p { color: #64748b; margin-bottom: 16px; }

.actions { display: flex; gap: 10px; justify-content: center; }
.cancel-btn { background: #f1f5f9; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }
.delete-confirm-btn { background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; }

code { font-size: 12px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; }
</style>
