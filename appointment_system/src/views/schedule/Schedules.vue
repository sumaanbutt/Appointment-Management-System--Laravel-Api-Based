<template>
  <div class="ams-page">

    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="mb-0">Schedules</h2>
        <p class="text-muted small mb-0">Manage staff schedules</p>
      </div>
      <button class="btn btn-ams" @click="openCreateModal()">+ New Schedule</button>
    </div>

    <div v-if="isAdmin" class="d-flex gap-2">
      <select v-model="bizFilter" @change="fetchSchedules" class="form-select" style="max-width:240px">
        <option value="">All Businesses</option>
        <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>
      </select>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
          <tr>
            <th class="ps-3">ID</th>
            <th>Staff Name</th>
            <th>Day</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th class="pe-3" style="width:120px">Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="schedule in schedules" :key="schedule.code">
            <td class="ps-3">{{ schedule.code }}</td>
            <td>{{ schedule.user?.name || schedule.user_code || '—' }}</td>
            <td class="text-capitalize">{{ schedule.working_day }}</td>
            <td>{{ schedule.status === 'INACTIVE' ? '—' : formatTime(schedule.shift_start_time) }}</td>
            <td>{{ schedule.status === 'INACTIVE' ? '—' : formatTime(schedule.shift_end_time) }}</td>
            <td><span :class="['badge', schedule.status === 'ACTIVE' ? 'bg-success' : 'bg-secondary']">{{ schedule.status === 'ACTIVE' ? 'Working' : 'Off Day' }}</span></td>
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
                        @click="openEditModal(schedule)">

                      <i class="bi bi-pencil me-2"></i>
                      Edit

                    </button>
                  </li>

                  <li>
                    <button
                        class="dropdown-item text-danger"
                        @click="deleteSchedule(schedule.code)">

                      <i class="bi bi-trash me-2"></i>
                      Delete

                    </button>
                  </li>

                </ul>

              </div>

            </td>
          </tr>
          <tr v-if="schedules.length === 0">
            <td colspan="7" class="text-center text-muted py-4">No schedules found</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Schedule</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="submitSingleUpdateSchedule">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Working Day *</label>
                <select v-model="editForm.working_day" class="form-select" required>
                  <option v-for="d in DAY_KEYS" :key="d" :value="d">{{ DAY_LABELS[d] }}</option>
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-semibold">Location</label>
                <select v-model="editForm.location_code" class="form-select">
                  <option value="">No specific location</option>
                  <option v-for="loc in locationsList" :key="loc.code" :value="loc.code">
                    {{ loc.address + " " + loc.street + " " + loc.city }}
                  </option>
                </select>
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">Start Time *</label>
                  <input type="time" v-model="editForm.shift_start_time" class="form-control" :required="editForm.status === 'ACTIVE'" :disabled="editForm.status === 'INACTIVE'" />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">End Time *</label>
                  <input type="time" v-model="editForm.shift_end_time" class="form-control" :required="editForm.status === 'ACTIVE'" :disabled="editForm.status === 'INACTIVE'" />
                </div>
              </div>
              <div class="mt-3">
                <label class="form-label fw-semibold">Day Status</label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="editForm.status" value="ACTIVE" id="statusActive" />
                    <label class="form-check-label" for="statusActive">Working Day</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="editForm.status" value="INACTIVE" id="statusInactive" />
                    <label class="form-check-label" for="statusInactive">Off Day</label>
                  </div>
                </div>
              </div>
              <p v-if="editError" class="text-danger small mt-2 mb-0">{{ editError }}</p>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
              <button type="button" class="btn btn-outline-danger btn-sm" @click="markAsOffDay" :disabled="saving">Mark as Off Day</button>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
                <button type="submit" class="btn btn-ams" :disabled="submitting">{{ submitting ? 'Saving...' : 'Save Changes' }}</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="showDeleteModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Schedule</h5>
            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>
          </div>
          <div class="modal-body text-center">
            <p class="mb-0">Delete this schedule?</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button class="btn btn-secondary btn-sm" @click="showDeleteModal = false">Cancel</button>
            <button class="btn btn-danger btn-sm" @click="deleteSchedule" :disabled="saving">{{ saving ? '...' : 'Delete' }}</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showCreateModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-height:90vh">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New Weekly Schedule</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="submitWeeklyBulkSchedule">
            <div class="modal-body" style="overflow-y:auto; max-height:calc(90vh - 120px)">

<!--              -->
              <div v-if="isAdmin" class="mb-3">
                <label class="form-label fw-semibold">Business *</label>
                <select v-model="createForm.business_code" class="form-select" required>
                  <option value="">Select business</option>
                  <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>
                </select>
              </div>

<!--               Staff dropdown-->
              <div class="mb-3">
                <label class="form-label fw-semibold">Staff Member *</label>
                <select v-model="createForm.user_code" class="form-select" required :disabled="staffList.length === 0">
                  <option value="">{{ staffList.length === 0 ? 'No staff available' : 'Select staff' }}</option>
                  <option v-for="u in staffList" :key="u.code" :value="u.code">{{ u.name }} ({{ u.user_type }})</option>
                </select>
              </div>


              <div class="mb-4">
                <label class="form-label fw-semibold">Location</label>
                <select v-model="createForm.location_code" class="form-select">
                  <option value="">No specific location</option>
                  <option v-for="loc in locationsList" :key="loc.code" :value="loc.code">
                    {{ loc.address + " " + loc.street + " " + loc.city }}
                  </option>
                </select>
              </div>


              <div class="week-grid-wrap">
                <div class="week-grid">
                  <div class="week-grid-header">
                    <span>Day</span>
                    <span>Start Time</span>
                    <span>End Time</span>
                    <span class="text-center">Off Day</span>
                  </div>
                  <div v-for="day in weekDays" :key="day.key" class="week-grid-row" :class="{ 'row-disabled': day.is_off }">
                    <span class="day-label">{{ day.label }}</span>
                    <input
                        type="time"
                        v-model="day.shift_start_time"
                        class="form-control form-control-sm"
                        :disabled="day.is_off"
                        :required="!day.is_off"
                    />
                    <input
                        type="time"
                        v-model="day.shift_end_time"
                        class="form-control form-control-sm"
                        :disabled="day.is_off"
                        :required="!day.is_off"
                    />
                    <div class="text-center">
                      <input type="checkbox" v-model="day.is_off" class="form-check-input" />
                    </div>
                  </div>
                </div>
              </div>

              <p v-if="createError" class="text-danger small mt-3 mb-0">{{ createError }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
              <button type="submit" class="btn btn-ams" :disabled="submitting">{{ submitting ? 'Creating...' : 'Create Schedule' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

// Central State Declarations
const authStore = useAuthStore()
const schedules = ref([])
const staffList = ref([])
const locationsList = ref([])

const showCreateModal = ref(false)
const showEditModal = ref(false)
const submitting = ref(false)

const targetEditingCode = ref(null)
const editForm = ref({ working_day: '', location_code: '', shift_start_time: '', shift_end_time: '', status: 'ACTIVE' })
const createForm = ref({ user_code: '', location_code: '' })

// Ensure your array coordinates match your backend FormRequest validation cases perfectly
const DAY_KEYS = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY']
const DAY_LABELS = { MONDAY: 'Monday', TUESDAY: 'Tuesday', WEDNESDAY: 'Wednesday', THURSDAY: 'Thursday', FRIDAY: 'Friday', SATURDAY: 'Saturday', SUNDAY: 'Sunday' }

function getFreshWeekGrid() {
  return DAY_KEYS.map(dayKey => ({
    key: dayKey,
    label: DAY_LABELS[dayKey],
    shift_start_time: '09:00',
    shift_end_time: '17:00',
    is_off: false
  }))
}


const weekDays = ref(getFreshWeekGrid())

// Lifecycle Hooks & Context Initialization
onMounted(async () => {
  const bizCode = authStore.user?.business_code || ''
  await Promise.all([
    fetchSchedulesData(),
    fetchStaffListing(bizCode),
    fetchLocationsListing(bizCode)
  ])
})

// Time Parsing Helper Methods
function formatTime(timeString) {
  if (!timeString) return '?'
  const [hours, minutes] = timeString.split(':').map(Number)
  const marker = hours >= 12 ? 'PM' : 'AM'
  const formattedHours = hours % 12 || 12
  return `${formattedHours}:${String(minutes).padStart(2, '0')} ${marker}`
}

function ensureHMinFormat(timeString) {
  if (!timeString) return null
  return timeString.substring(0, 5) // Guarantees 'HH:mm' format matching 'date_format:H:i'
}

// API Communication Actions
async function fetchSchedulesData() {
  try {
    const response = await api.get('/user-shift-schedules')
    schedules.value = response.data.data?.data || response.data.data || []
  } catch (err) {
    console.error('Failed to resolve database operational shift profiles context.', err)
  }
}

async function fetchStaffListing(businessCode) {
  if (!businessCode) return
  try {
    const response = await api.get('/users', { params: { business_code: businessCode } })
    const collectedUsers = response.data.data?.data || response.data.data || []

    staffList.value = collectedUsers.filter(user =>
        ['OPERATION_STAFF', 'SERVICE_STAFF'].includes(user.user_type)
    )
  } catch (err) {
    console.error('Failed to resolve enterprise team listings maps.', err)
  }
}

async function fetchLocationsListing(businessCode) {
  if (!businessCode) return
  try {
    const response = await api.get('/business-locations', { params: { business_code: businessCode } })
    locationsList.value = response.data.data?.data || response.data.data || []
  } catch (err) {
    console.error('Failed to fetch corporate location parameters.', err)
  }
}

// Modal Handlers
function openCreateModal() {
  createForm.value = { user_code: '', location_code: '' }
  weekDays.value = getFreshWeekGrid()
  showCreateModal.value = true
}

function openEditModal(scheduleRow) {
  targetEditingCode.value = scheduleRow.code
  editForm.value = {
    working_day: scheduleRow.working_day ? scheduleRow.working_day.toUpperCase() : 'MONDAY',
    location_code: scheduleRow.location_code || '',
    shift_start_time: scheduleRow.shift_start_time ? scheduleRow.shift_start_time.substring(0, 5) : '09:00',
    shift_end_time: scheduleRow.shift_end_time ? scheduleRow.shift_end_time.substring(0, 5) : '17:00',
    status: scheduleRow.status ? scheduleRow.status.toUpperCase() : 'ACTIVE'
  }
  showEditModal.value = true
}

async function submitWeeklyBulkSchedule() {
  if (!createForm.value.user_code) return
  submitting.value = true

  const parentBusinessCode = authStore.user?.business_code

  const matrixPayload = weekDays.value.map(day => ({
    business_code: parentBusinessCode,
    user_code: createForm.value.user_code,
    working_days: day.key.toUpperCase(), // Sends matching case string 'MONDAY' to FormRequest validation
    location_code: createForm.value.location_code || null,
    shift_start_time: day.is_off ? null : ensureHMinFormat(day.shift_start_time),
    shift_end_time: day.is_off ? null : ensureHMinFormat(day.shift_end_time),
    status: day.is_off ? 'INACTIVE' : 'ACTIVE'
  }))

  try {
    await api.post('/user-shift-schedules', matrixPayload)
    showCreateModal.value = false
    await fetchSchedulesData()
  } catch (err) {

    console.log(
        'FULL BACKEND ERROR:',
        err.response?.data
    )

    alert(
        JSON.stringify(
            err.response?.data,
            null,
            2
        )
    )

  } finally {
    submitting.value = false
  }
}

async function submitSingleUpdateSchedule() {
  try {
    const payload = {
      working_day: editForm.value.working_day.toUpperCase(),
      location_code: editForm.value.location_code || null,
      status: editForm.value.status,
      shift_start_time: editForm.value.status === 'INACTIVE' ? null : ensureHMinFormat(editForm.value.shift_start_time),
      shift_end_time: editForm.value.status === 'INACTIVE' ? null : ensureHMinFormat(editForm.value.shift_end_time)
    }

    await api.put(`/user-shift-schedules/${targetEditingCode.value}`, payload)
    showEditModal.value = false
    await fetchSchedulesData()
  } catch (err) {
    alert(err.response?.data?.message || 'Error committing singular record changes properties.')
  }
}

async function deleteSchedule(scheduleCode) {
  if (!confirm('Are you sure you want to drop this individual shift line?')) return
  try {
    await api.delete(`/user-shift-schedules/${scheduleCode}`)
    await fetchSchedulesData()
  } catch (err) {
    alert('Error clearing selected index shift structural rows references.')
  }
}
</script>

<style scoped>
.week-grid-wrap{
  overflow-x:auto;
}

.week-grid{
  display:flex;
  flex-direction:column;
  gap:8px;
}

.week-grid-header,
.week-grid-row{
  display:grid;
  grid-template-columns:1fr 1fr 1fr 100px;
  gap:12px;
  align-items:center;
}

.week-grid-header{
  font-weight:600;
  color:#6b7280;
  padding-bottom:10px;
  border-bottom:1px solid #e5e7eb;
}

.week-grid-row{
  padding:10px 0;
  border-bottom:1px solid #f1f5f9;
}

.day-label{
  font-weight:600;
}

.row-disabled{
  opacity:.6;
}
.fs-7 { font-size: 0.785rem; }
.fw-mono { font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
</style>