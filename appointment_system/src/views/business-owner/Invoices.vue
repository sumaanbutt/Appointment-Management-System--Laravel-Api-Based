<template>
  <div class="ams-page">
    <div class="d-flex align-items-center justify-content-between">
      <div><h2 class="mb-0">Invoices</h2><p class="text-muted small mb-0">View and manage invoices</p></div>
    </div>
    <div class="d-flex gap-2">
      <select v-model="statusFilter" @change="fetchInvoices" class="form-select" style="max-width:200px">
        <option value="">All Statuses</option>
        <option value="draft">Draft</option>
        <option value="issued">Issued</option>
        <option value="paid">Paid</option>
        <option value="canceled">Cancelled</option>
      </select>
    </div>
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
            <tr><th class="ps-3">ID</th><th>Total</th><th>Status</th><th>Created</th><th class="pe-3" style="width:100px">Actions</th></tr>
          </thead>
          <tbody>
                <tr v-for="inv in invoices" :key="inv.code">
              <td class="ps-3">{{ inv.code }}</td>
              <td>{{ inv.total ?? '—' }}</td>
              <td><span :class="['ams-badge', inv.status]">{{ inv.status }}</span></td>
              <td>{{ formatDate(inv.created_at) }}</td>
                  <td class="pe-3">

                    <div class="dropdown">

                      <button
                          class="btn btn-sm btn-outline-secondary"
                          type="button"
                          data-bs-toggle="dropdown">

                        <i class="bi bi-three-dots-vertical"></i>

                      </button>

                      <ul class="dropdown-menu dropdown-menu-end">

                        <li>
                          <button
                              class="dropdown-item"
                              @click="openView(inv)">

                            <i class="bi bi-eye me-2"></i>
                            View

                          </button>
                        </li>

                      </ul>

                    </div>

                  </td>
            </tr>
            <tr v-if="invoices.length === 0"><td colspan="5" class="text-center text-muted py-4">No invoices found</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- VIEW MODAL -->
    <div v-if="showViewModal && selected" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Invoice #{{ selected.code }}</h5>
            <button type="button" class="btn-close" @click="showViewModal = false"></button>
          </div>
          <div class="modal-body">
            <dl class="row mb-3">
              <dt class="col-5 text-muted">Status</dt>
              <dd class="col-7"><span :class="['ams-badge', selected.invoice_status]">{{ selected.invoice_status }}</span></dd>
              <dt class="col-5 text-muted">Total</dt>
              <dd class="col-7"><strong>{{ selected.total }}</strong></dd>
              <dt class="col-5 text-muted">Created</dt>
              <dd class="col-7">{{ formatDate(selected.created_at) }}</dd>
            </dl>
            <div class="d-flex align-items-center gap-2">
              <label class="form-label fw-semibold mb-0">Update Status</label>
              <select v-model="newStatus" class="form-select form-select-sm">
                <option value="draft">Draft</option>
                <option value="issued">Issued</option>
                <option value="paid">Paid</option>
                <option value="canceled">Canceled</option>
              </select>
              <button
                  class="btn btn-primary btn-sm px-3"
                  @click="updateStatus"
                  :disabled="saving">
                {{ saving ? 'Updating...' : 'Update' }}

              </button>
            </div>
            <p v-if="formError" class="text-danger small mt-2 mb-0">{{ formError }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showViewModal = false">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'
import {apiHandler} from "@/services/api/apiHandler.ts";

const authStore = useAuthStore()
const invoices = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const formError = ref('')
const statusFilter = ref('')

const showViewModal = ref(false)
const selected = ref(null)
const newStatus = ref('')

function formatDate(d) { return d ? new Date(d).toLocaleDateString() : '—' }

async function fetchInvoices() {
  loading.value = true
  error.value = ''
  try {
    const biz = authStore.user?.business_code
    const params = {}
    if (biz) params.business_code = biz
    if (statusFilter.value) params.status = statusFilter.value

    const res = await apiHandler('invoice', 'getAllInvoices', {
      params: {}
    })

    invoices.value =
        res.data?.data.data
        ??
        []
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load invoices'
  } finally {
    loading.value = false
  }
}

function openView(inv) {
  selected.value = inv
  newStatus.value = inv.status
  formError.value = ''
  showViewModal.value = true
}

async function updateStatus() {
  saving.value = true
  formError.value = ''
  try {
    await apiHandler(
        'invoice',
        'deactivateInvoice',
        {
          pathParams: {
            code: selected.value.code
          },
          body: {
            status: newStatus.value
          }
        })

    showViewModal.value = false
    await fetchInvoices()
  } catch (err) {
    formError.value = err.response?.data?.message || 'Update failed'
  } finally {
    saving.value = false
  }
}

onMounted(fetchInvoices)
</script>


<style scoped>

.ams-page{
  display:flex;
  flex-direction:column;
  gap:20px;
}

.card{
  border-radius:12px;
}

.ams-table th,
.ams-table td{
  vertical-align:middle;
  font-size:14px;
}

code{
  background:#f1f5f9;
  padding:3px 8px;
  border-radius:6px;
  color:#334155;
}

.btn-ams{
  background:#6366f1;
  color:#fff;
  border:none;
}

.btn-ams:hover{
  background:#4f46e5;
  color:#fff;
}

.form-control,
.form-select{
  border-radius:8px;
  font-size:14px;
}

.form-control:focus,
.form-select:focus{
  border-color:#6366f1;
  box-shadow:0 0 0 0.15rem rgba(99,102,241,.15);
}

</style>