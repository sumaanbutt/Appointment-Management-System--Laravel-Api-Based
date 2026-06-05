<!--<template>-->
<!--  <div class="ams-page">-->

<!--    <div class="d-flex align-items-center justify-content-between">-->
<!--      <div>-->
<!--        <h2 class="mb-0">Schedules</h2>-->
<!--        <p class="text-muted small mb-0">Manage staff schedules</p>-->
<!--      </div>-->
<!--      <button class="btn btn-ams" @click="openCreateModal()">+ New Schedule</button>-->
<!--    </div>-->

<!--    <div v-if="isAdmin" class="d-flex gap-2">-->
<!--      <select v-model="bizFilter" @change="fetchSchedules" class="form-select" style="max-width:240px">-->
<!--        <option value="">All Businesses</option>-->
<!--        <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>-->
<!--      </select>-->
<!--    </div>-->

<!--    <div class="card shadow-sm border-0">-->
<!--      <div class="card-body p-0">-->
<!--        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>-->
<!--        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>-->
<!--        <table v-else class="table table-hover ams-table mb-0">-->
<!--          <thead class="table-light">-->
<!--          <tr>-->
<!--            <th class="ps-3">ID</th>-->
<!--            <th>Staff Name</th>-->
<!--            <th>Day</th>-->
<!--            <th>Start Time</th>-->
<!--            <th>End Time</th>-->
<!--            <th>Status</th>-->
<!--            <th class="pe-3" style="width:120px">Actions</th>-->
<!--          </tr>-->
<!--          </thead>-->
<!--          <tbody>-->
<!--          <tr v-for="schedule in schedules" :key="schedule.code">-->
<!--            <td class="ps-3">{{ schedule.code }}</td>-->
<!--            <td>{{ schedule.user?.name || schedule.user_code || '—' }}</td>-->
<!--            <td class="text-capitalize">{{ schedule.working_day }}</td>-->
<!--            <td>{{ schedule.status === 'INACTIVE' ? '—' : formatTime(schedule.shift_start_time) }}</td>-->
<!--            <td>{{ schedule.status === 'INACTIVE' ? '—' : formatTime(schedule.shift_end_time) }}</td>-->
<!--            <td><span :class="['badge', schedule.status === 'ACTIVE' ? 'bg-success' : 'bg-secondary']">{{ schedule.status === 'ACTIVE' ? 'Working' : 'Off Day' }}</span></td>-->
<!--            <td class="pe-3">-->

<!--              <div class="dropdown">-->

<!--                <button-->
<!--                    class="btn btn-sm btn-outline-secondary"-->
<!--                    type="button"-->
<!--                    data-bs-toggle="dropdown">-->

<!--                  <i class="bi bi-three-dots-vertical"></i>-->

<!--                </button>-->

<!--                <ul class="dropdown-menu dropdown-menu-end">-->

<!--                  <li>-->
<!--                    <button-->
<!--                        class="dropdown-item"-->
<!--                        @click="openEditModal(schedule)">-->

<!--                      <i class="bi bi-pencil me-2"></i>-->
<!--                      Edit-->

<!--                    </button>-->
<!--                  </li>-->

<!--                  <li>-->
<!--                    <button-->
<!--                        class="dropdown-item text-danger"-->
<!--                        @click="deleteSchedule(schedule.code)">-->

<!--                      <i class="bi bi-trash me-2"></i>-->
<!--                      Delete-->

<!--                    </button>-->
<!--                  </li>-->

<!--                </ul>-->

<!--              </div>-->

<!--            </td>-->
<!--          </tr>-->
<!--          <tr v-if="schedules.length === 0">-->
<!--            <td colspan="7" class="text-center text-muted py-4">No schedules found</td>-->
<!--          </tr>-->
<!--          </tbody>-->
<!--        </table>-->
<!--      </div>-->
<!--    </div>-->

<!--    <nav class="mt-3">-->
<!--      <ul class="pagination justify-content-end">-->

<!--        <li class="page-item" :class="{ disabled: currentPage === 1 }">-->
<!--          <button class="page-link" @click="changePage(currentPage - 1)">-->
<!--            Previous-->
<!--          </button>-->
<!--        </li>-->

<!--        <li-->
<!--            v-for="page in lastPage"-->
<!--            :key="page"-->
<!--            class="page-item"-->
<!--            :class="{ active: currentPage === page }">-->

<!--          <button-->
<!--              class="page-link"-->
<!--              @click="changePage(page)">-->

<!--            {{ page }}-->

<!--          </button>-->

<!--        </li>-->

<!--        <li class="page-item" :class="{ disabled: currentPage === lastPage }">-->
<!--          <button class="page-link" @click="changePage(currentPage + 1)">-->
<!--            Next-->
<!--          </button>-->
<!--        </li>-->

<!--      </ul>-->
<!--    </nav>-->

<!--    <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">-->
<!--      <div class="modal-dialog modal-dialog-centered">-->
<!--        <div class="modal-content">-->
<!--          <div class="modal-header">-->
<!--            <h5 class="modal-title">Edit Schedule</h5>-->
<!--            <button type="button" class="btn-close" @click="showEditModal = false"></button>-->
<!--          </div>-->
<!--          <form @submit.prevent="submitSingleUpdateSchedule">-->
<!--            <div class="modal-body">-->
<!--              <div class="mb-3">-->
<!--                <label class="form-label fw-semibold">Working Day *</label>-->
<!--                <select v-model="editForm.working_day" class="form-select" required>-->
<!--                  <option v-for="d in DAY_KEYS" :key="d" :value="d">{{ DAY_LABELS[d] }}</option>-->
<!--                </select>-->
<!--              </div>-->

<!--              <div class="mb-3">-->
<!--                <label class="form-label fw-semibold">Location</label>-->
<!--                <select v-model="editForm.location_code" class="form-select">-->
<!--                  <option value="">No specific location</option>-->
<!--                  <option v-for="loc in locationsList" :key="loc.code" :value="loc.code">-->
<!--                    {{ loc.address + " " + loc.street + " " + loc.city }}-->
<!--                  </option>-->
<!--                </select>-->
<!--              </div>-->
<!--              <div class="row g-3">-->
<!--                <div class="col-6">-->
<!--                  <label class="form-label fw-semibold">Start Time *</label>-->
<!--                  <input type="time" v-model="editForm.shift_start_time" class="form-control" :required="editForm.status === 'ACTIVE'" :disabled="editForm.status === 'INACTIVE'" />-->
<!--                </div>-->
<!--                <div class="col-6">-->
<!--                  <label class="form-label fw-semibold">End Time *</label>-->
<!--                  <input type="time" v-model="editForm.shift_end_time" class="form-control" :required="editForm.status === 'ACTIVE'" :disabled="editForm.status === 'INACTIVE'" />-->
<!--                </div>-->
<!--              </div>-->
<!--              <div class="mt-3">-->
<!--                <label class="form-label fw-semibold">Day Status</label>-->
<!--                <div class="d-flex gap-3">-->
<!--                  <div class="form-check">-->
<!--                    <input class="form-check-input" type="radio" v-model="editForm.status" value="ACTIVE" id="statusActive" />-->
<!--                    <label class="form-check-label" for="statusActive">Working Day</label>-->
<!--                  </div>-->
<!--                  <div class="form-check">-->
<!--                    <input class="form-check-input" type="radio" v-model="editForm.status" value="INACTIVE" id="statusInactive" />-->
<!--                    <label class="form-check-label" for="statusInactive">Off Day</label>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--              <p v-if="editError" class="text-danger small mt-2 mb-0">{{ editError }}</p>-->
<!--            </div>-->
<!--            <div class="modal-footer d-flex justify-content-between align-items-center">-->
<!--              <button type="button" class="btn btn-outline-danger btn-sm" @click="markAsOffDay" :disabled="saving">Mark as Off Day</button>-->
<!--              <div class="d-flex gap-2">-->
<!--                <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>-->
<!--                <button type="submit" class="btn btn-ams" :disabled="submitting">{{ submitting ? 'Saving...' : 'Save Changes' }}</button>-->
<!--              </div>-->
<!--            </div>-->
<!--          </form>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

<!--    <div v-if="showDeleteModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">-->
<!--      <div class="modal-dialog modal-sm modal-dialog-centered">-->
<!--        <div class="modal-content">-->
<!--          <div class="modal-header">-->
<!--            <h5 class="modal-title">Delete Schedule</h5>-->
<!--            <button type="button" class="btn-close" @click="showDeleteModal = false"></button>-->
<!--          </div>-->
<!--          <div class="modal-body text-center">-->
<!--            <p class="mb-0">Delete this schedule?</p>-->
<!--          </div>-->
<!--          <div class="modal-footer justify-content-center">-->
<!--            <button class="btn btn-secondary btn-sm" @click="showDeleteModal = false">Cancel</button>-->
<!--            <button class="btn btn-danger btn-sm" @click="deleteSchedule" :disabled="saving">{{ saving ? '...' : 'Delete' }}</button>-->
<!--          </div>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

<!--    <div v-if="showCreateModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">-->
<!--      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-height:90vh">-->
<!--        <div class="modal-content">-->
<!--          <div class="modal-header">-->
<!--            <h5 class="modal-title">New Weekly Schedule</h5>-->
<!--            <button type="button" class="btn-close" @click="showCreateModal = false"></button>-->
<!--          </div>-->
<!--          <form @submit.prevent="submitWeeklyBulkSchedule">-->
<!--            <div class="modal-body" style="overflow-y:auto; max-height:calc(90vh - 120px)">-->

<!--&lt;!&ndash;              &ndash;&gt;-->
<!--              <div v-if="isAdmin" class="mb-3">-->
<!--                <label class="form-label fw-semibold">Business *</label>-->
<!--                <select v-model="createForm.business_code" class="form-select" required>-->
<!--                  <option value="">Select business</option>-->
<!--                  <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>-->
<!--                </select>-->
<!--              </div>-->

<!--&lt;!&ndash;               Staff dropdown&ndash;&gt;-->
<!--              <div class="mb-3">-->
<!--                <label class="form-label fw-semibold">Staff Member *</label>-->
<!--                <select v-model="createForm.user_code" class="form-select" required :disabled="staffList.length === 0">-->
<!--                  <option value="">{{ staffList.length === 0 ? 'No staff available' : 'Select staff' }}</option>-->
<!--                  <option v-for="u in staffList" :key="u.code" :value="u.code">{{ u.name }} ({{ u.user_type }})</option>-->
<!--                </select>-->
<!--              </div>-->


<!--              <div class="mb-4">-->
<!--                <label class="form-label fw-semibold">Location</label>-->
<!--                <select v-model="createForm.location_code" class="form-select">-->
<!--                  <option value="">No specific location</option>-->
<!--                  <option v-for="loc in locationsList" :key="loc.code" :value="loc.code">-->
<!--                    {{ loc.address + " " + loc.street + " " + loc.city }}-->
<!--                  </option>-->
<!--                </select>-->
<!--              </div>-->


<!--              <div class="week-grid-wrap">-->
<!--                <div class="week-grid">-->
<!--                  <div class="week-grid-header">-->
<!--                    <span>Day</span>-->
<!--                    <span>Start Time</span>-->
<!--                    <span>End Time</span>-->
<!--                    <span class="text-center">Off Day</span>-->
<!--                  </div>-->
<!--                  <div v-for="day in weekDays" :key="day.key" class="week-grid-row" :class="{ 'row-disabled': day.is_off }">-->
<!--                    <span class="day-label">{{ day.label }}</span>-->
<!--                    <input-->
<!--                        type="time"-->
<!--                        v-model="day.shift_start_time"-->
<!--                        class="form-control form-control-sm"-->
<!--                        :disabled="day.is_off"-->
<!--                        :required="!day.is_off"-->
<!--                    />-->
<!--                    <input-->
<!--                        type="time"-->
<!--                        v-model="day.shift_end_time"-->
<!--                        class="form-control form-control-sm"-->
<!--                        :disabled="day.is_off"-->
<!--                        :required="!day.is_off"-->
<!--                    />-->
<!--                    <div class="text-center">-->
<!--                      <input type="checkbox" v-model="day.is_off" class="form-check-input" />-->
<!--                    </div>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->

<!--              <p v-if="createError" class="text-danger small mt-3 mb-0">{{ createError }}</p>-->
<!--            </div>-->
<!--            <div class="modal-footer">-->
<!--              <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>-->
<!--              <button type="submit" class="btn btn-ams" :disabled="submitting">{{ submitting ? 'Creating...' : 'Create Schedule' }}</button>-->
<!--            </div>-->
<!--          </form>-->
<!--        </div>-->
<!--      </div>-->
<!--    </div>-->

<!--  </div>-->
<!--</template>-->

<!--<script setup>-->
<!--import { ref, onMounted } from 'vue'-->
<!--import { useAuthStore } from '@/stores/auth.store'-->
<!--import api from '@/services/api'-->

<!--// Central State Declarations-->
<!--const authStore = useAuthStore()-->
<!--const schedules = ref([])-->
<!--const currentPage = ref(1)-->
<!--const lastPage = ref(1)-->
<!--const staffList = ref([])-->
<!--const locationsList = ref([])-->

<!--const showCreateModal = ref(false)-->
<!--const showEditModal = ref(false)-->
<!--const submitting = ref(false)-->

<!--const targetEditingCode = ref(null)-->
<!--const editForm = ref({ working_day: '', location_code: '', shift_start_time: '', shift_end_time: '', status: 'ACTIVE' })-->
<!--const createForm = ref({ user_code: '', location_code: '' })-->

<!--// Ensure your array coordinates match your backend FormRequest validation cases perfectly-->
<!--const DAY_KEYS = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY']-->
<!--const DAY_LABELS = { MONDAY: 'MONDAY', TUESDAY: 'Tuesday', WEDNESDAY: 'Wednesday', THURSDAY: 'Thursday', FRIDAY: 'Friday', SATURDAY: 'Saturday', SUNDAY: 'Sunday' }-->

<!--function getFreshWeekGrid() {-->
<!--  return DAY_KEYS.map(dayKey => ({-->
<!--    key: dayKey,-->
<!--    label: DAY_LABELS[dayKey],-->
<!--    shift_start_time: '09:00',-->
<!--    shift_end_time: '17:00',-->
<!--    is_off: false-->
<!--  }))-->
<!--}-->


<!--const weekDays = ref(getFreshWeekGrid())-->

<!--// Lifecycle Hooks & Context Initialization-->
<!--onMounted(async () => {-->
<!--  const bizCode = authStore.user?.business_code || ''-->
<!--  await Promise.all([-->
<!--    fetchSchedulesData(),-->
<!--    fetchStaffListing(bizCode),-->
<!--    fetchLocationsListing(bizCode)-->
<!--  ])-->
<!--})-->

<!--// Time Parsing Helper Methods-->
<!--function formatTime(timeString) {-->
<!--  if (!timeString) return '?'-->
<!--  const [hours, minutes] = timeString.split(':').map(Number)-->
<!--  const marker = hours >= 12 ? 'PM' : 'AM'-->
<!--  const formattedHours = hours % 12 || 12-->
<!--  return `${formattedHours}:${String(minutes).padStart(2, '0')} ${marker}`-->
<!--}-->

<!--function ensureHMinFormat(timeString) {-->
<!--  if (!timeString) return null-->
<!--  return timeString.substring(0, 5) // Guarantees 'HH:mm' format matching 'date_format:H:i'-->
<!--}-->

<!--// API Communication Actions-->
<!--async function fetchSchedulesData() {-->
<!--  try {-->
<!--    const response = await api.get('/user-shift-schedules',{-->
<!--      params: {-->
<!--        page: currentPage.value-->
<!--      }-->
<!--    })-->
<!--    schedules.value = response.data.data?.data || response.data.data || []-->
<!--    currentPage.value = response.data.data.current_page-->
<!--    lastPage.value = response.data.data.last_page-->
<!--  } catch (err) {-->
<!--    console.error('Failed to resolve database operational shift profiles context.', err)-->
<!--  }-->
<!--}-->

<!--async function changePage(page) {-->
<!--  if (page < 1 || page > lastPage.value) return-->

<!--  currentPage.value = page-->
<!--  await fetchSchedulesData()-->
<!--}-->

<!--async function fetchStaffListing(businessCode) {-->
<!--  if (!businessCode) return-->
<!--  try {-->
<!--    const response = await api.get('/users', { params: { business_code: businessCode } })-->
<!--    const collectedUsers = response.data.data?.data || response.data.data || []-->

<!--    staffList.value = collectedUsers.filter(user =>-->
<!--        ['OPERATION_STAFF', 'SERVICE_STAFF'].includes(user.user_type)-->
<!--    )-->
<!--  } catch (err) {-->
<!--    console.error('Failed to resolve enterprise team listings maps.', err)-->
<!--  }-->
<!--}-->

<!--async function fetchLocationsListing(businessCode) {-->
<!--  if (!businessCode) return-->
<!--  try {-->
<!--    const response = await api.get('/business-locations', { params: { business_code: businessCode } })-->
<!--    locationsList.value = response.data.data?.data || response.data.data || []-->
<!--  } catch (err) {-->
<!--    console.error('Failed to fetch corporate location parameters.', err)-->
<!--  }-->
<!--}-->

<!--// Modal Handlers-->
<!--function openCreateModal() {-->
<!--  createForm.value = { user_code: '', location_code: '' }-->
<!--  weekDays.value = getFreshWeekGrid()-->
<!--  showCreateModal.value = true-->
<!--}-->

<!--function openEditModal(scheduleRow) {-->
<!--  targetEditingCode.value = scheduleRow.code-->
<!--  editForm.value = {-->
<!--    working_day: scheduleRow.working_day ? scheduleRow.working_day.toUpperCase() : 'MONDAY',-->
<!--    location_code: scheduleRow.location_code || '',-->
<!--    shift_start_time: scheduleRow.shift_start_time ? scheduleRow.shift_start_time.substring(0, 5) : '09:00',-->
<!--    shift_end_time: scheduleRow.shift_end_time ? scheduleRow.shift_end_time.substring(0, 5) : '17:00',-->
<!--    status: scheduleRow.status ? scheduleRow.status.toUpperCase() : 'ACTIVE'-->
<!--  }-->
<!--  showEditModal.value = true-->
<!--}-->

<!--async function submitWeeklyBulkSchedule() {-->
<!--  if (!createForm.value.user_code) return-->
<!--  submitting.value = true-->

<!--  const parentBusinessCode = authStore.user?.business_code-->

<!--  const matrixPayload = weekDays.value.map(day => ({-->
<!--    business_code: parentBusinessCode,-->
<!--    user_code: createForm.value.user_code,-->
<!--    working_days: day.key.toUpperCase(), // Sends matching case string 'MONDAY' to FormRequest validation-->
<!--    location_code: createForm.value.location_code || null,-->
<!--    shift_start_time: day.is_off ? null : ensureHMinFormat(day.shift_start_time),-->
<!--    shift_end_time: day.is_off ? null : ensureHMinFormat(day.shift_end_time),-->
<!--    status: day.is_off ? 'INACTIVE' : 'ACTIVE'-->
<!--  }))-->

<!--  try {-->
<!--    await api.post('/user-shift-schedules', matrixPayload)-->
<!--    showCreateModal.value = false-->
<!--    await fetchSchedulesData()-->
<!--  } catch (err) {-->

<!--    console.log(-->
<!--        'FULL BACKEND ERROR:',-->
<!--        err.response?.data-->
<!--    )-->

<!--    alert(-->
<!--        JSON.stringify(-->
<!--            err.response?.data,-->
<!--            null,-->
<!--            2-->
<!--        )-->
<!--    )-->

<!--  } finally {-->
<!--    submitting.value = false-->
<!--  }-->
<!--}-->

<!--async function submitSingleUpdateSchedule() {-->
<!--  try {-->
<!--    const payload = {-->
<!--      working_day: editForm.value.working_day.toUpperCase(),-->
<!--      location_code: editForm.value.location_code || null,-->
<!--      status: editForm.value.status,-->
<!--      shift_start_time: editForm.value.status === 'INACTIVE' ? null : ensureHMinFormat(editForm.value.shift_start_time),-->
<!--      shift_end_time: editForm.value.status === 'INACTIVE' ? null : ensureHMinFormat(editForm.value.shift_end_time)-->
<!--    }-->

<!--    await api.put(`/user-shift-schedules/${targetEditingCode.value}`, payload)-->
<!--    showEditModal.value = false-->
<!--    await fetchSchedulesData()-->
<!--  } catch (err) {-->
<!--    alert(err.response?.data?.message || 'Error committing singular record changes properties.')-->
<!--  }-->
<!--}-->

<!--async function deleteSchedule(scheduleCode) {-->
<!--  if (!confirm('Are you sure you want to drop this individual shift line?')) return-->
<!--  try {-->
<!--    await api.delete(`/user-shift-schedules/${scheduleCode}`)-->
<!--    await fetchSchedulesData()-->
<!--  } catch (err) {-->
<!--    alert('Error clearing selected index shift structural rows references.')-->
<!--  }-->
<!--}-->
<!--</script>-->

<!--<style scoped>-->
<!--.week-grid-wrap{-->
<!--  overflow-x:auto;-->
<!--}-->

<!--.week-grid{-->
<!--  display:flex;-->
<!--  flex-direction:column;-->
<!--  gap:8px;-->
<!--}-->

<!--.week-grid-header,-->
<!--.week-grid-row{-->
<!--  display:grid;-->
<!--  grid-template-columns:1fr 1fr 1fr 100px;-->
<!--  gap:12px;-->
<!--  align-items:center;-->
<!--}-->

<!--.week-grid-header{-->
<!--  font-weight:600;-->
<!--  color:#6b7280;-->
<!--  padding-bottom:10px;-->
<!--  border-bottom:1px solid #e5e7eb;-->
<!--}-->

<!--.week-grid-row{-->
<!--  padding:10px 0;-->
<!--  border-bottom:1px solid #f1f5f9;-->
<!--}-->

<!--.day-label{-->
<!--  font-weight:600;-->
<!--}-->

<!--.row-disabled{-->
<!--  opacity:.6;-->
<!--}-->
<!--.fs-7 { font-size: 0.785rem; }-->
<!--.fw-mono { font-family: SFMono-Regular, Menlo, Monaco, Consolas, monospace; }-->
<!--</style>-->


<template>
  <div class="ams-page">

    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="mb-0">Schedules</h2>
        <p class="text-muted small mb-0">Manage staff schedules</p>
      </div>
      <button class="btn btn-ams" @click="showCreateModal = true">+ New Schedule</button>
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
            <th class="pe-3" style="width:220px">Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="schedule in schedules" :key="schedule.code">
            <td class="ps-3">{{ schedule.code }}</td>
            <td>{{ schedule.user?.name || schedule.user_code || '—' }}</td>
            <td>{{ schedule.working_day }}</td>
            <td>{{ schedule.status === 'INACTIVE' ? '—' : formatTime(schedule.shift_start_time) }}</td>
            <td>{{ schedule.status === 'INACTIVE' ? '—' : formatTime(schedule.shift_end_time) }}</td>
            <td><span :class="['badge', schedule.status === 'ACTIVE' ? 'bg-success' : 'bg-secondary']">{{ schedule.status === 'ACTIVE' ? 'Active' : 'Inactive' }}</span></td>
            <td class="pe-3">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <button class="dropdown-item" @click="openEdit(schedule)">
                      <i class="bi bi-pencil me-2"></i>
                      Edit
                    </button>
                  </li>
                  <li>
                    <button class="dropdown-item text-danger" @click="openDelete(schedule)">
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

    <nav class="mt-3">
      <ul class="pagination justify-content-end">

        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)">
            Previous
          </button>
        </li>

        <li
            v-for="page in lastPage"
            :key="page"
            class="page-item"
            :class="{ active: currentPage === page }">

          <button
              class="page-link"
              @click="changePage(page)">

            {{ page }}

          </button>

        </li>

        <li class="page-item" :class="{ disabled: currentPage === lastPage }">
          <button class="page-link" @click="changePage(currentPage + 1)">
            Next
          </button>
        </li>

      </ul>
    </nav>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Schedule</h5>
            <button type="button" class="btn-close" @click="showEditModal = false"></button>
          </div>
          <form @submit.prevent="updateSchedule">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label fw-semibold">Working Day *</label>
                <select v-model="editForm.working_days" class="form-select" required>
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
                  <input type="time" v-model="editForm.start_time" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">End Time *</label>
                  <input type="time" v-model="editForm.end_time" class="form-control" required />
                </div>
              </div>
              <div class="mt-3">
                <label class="form-label fw-semibold">Status *</label>
                <select v-model="editForm.status" class="form-select" required>
                  <option value="ACTIVE">Active</option>
                  <option value="INACTIVE">Inactive</option>
                </select>
              </div>
              <p v-if="editError" class="text-danger small mt-2 mb-0">{{ editError }}</p>
            </div>
            <div class="modal-footer d-flex gap-2 justify-content-end align-items-center">
              <button type="button" class="btn btn-secondary" @click="showEditModal = false">Cancel</button>
              <button type="submit" class="btn btn-ams" :disabled="saving">{{ saving ? 'Saving...' : 'Save Changes' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- DELETE CONFIRM MODAL -->
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

    <!-- if    -->
    <!-- CREATE MODAL -->
    <div v-if="showCreateModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" style="max-height:90vh">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">New Weekly Schedule</h5>
            <button type="button" class="btn-close" @click="showCreateModal = false"></button>
          </div>
          <form @submit.prevent="createSchedule">
            <div class="modal-body" style="overflow-y:auto; max-height:calc(90vh - 120px)">

              <!-- Business (admin only) -->
              <div v-if="isAdmin" class="mb-3">
                <label class="form-label fw-semibold">Business *</label>
                <select v-model="createForm.business_code" class="form-select" required @change="onBusinessChange">
                  <option value="">Select business</option>
                  <option v-for="biz in businesses" :key="biz.code" :value="biz.code">{{ biz.name }}</option>
                </select>
              </div>

              <!-- Staff dropdown -->
              <div class="mb-3">
                <label class="form-label fw-semibold">Staff Member *</label>
                <select v-model="createForm.user_code" class="form-select" required :disabled="staffList.length === 0">
                  <option value="">{{ staffList.length === 0 ? 'No staff available' : 'Select staff' }}</option>
                  <option v-for="u in staffList" :key="u.code" :value="u.code">{{ u.name }} ({{ u.user_type }})</option>
                </select>
              </div>


              <!-- Location -->
              <div class="mb-4">
                <label class="form-label fw-semibold">Location</label>
                <select v-model="createForm.location_code" class="form-select">
                  <option value="">No specific location</option>
                  <option v-for="loc in locationsList" :key="loc.code" :value="loc.code">
                    {{ loc.address + " " + loc.street + " " + loc.city }}
                  </option>
                </select>
              </div>


              <div class="mb-4 border rounded-3 p-3 bg-light-subtle">
                <div class="mb-3">
                  <label class="form-label fw-semibold">Working Days Setup</label>
                  <div class="d-flex gap-4 flex-wrap">
                    <div class="form-check">
                      <input id="modeWholeWeek" v-model="scheduleMode" class="form-check-input" type="radio" value="whole_week" />
                      <label class="form-check-label" for="modeWholeWeek">Whole week is working</label>
                    </div>
                    <div class="form-check">
                      <input id="modeCustomDays" v-model="scheduleMode" class="form-check-input" type="radio" value="custom" />
                      <label class="form-check-label" for="modeCustomDays">Custom working days</label>
                    </div>
                  </div>
                  <small v-if="scheduleMode === 'custom'" class="text-muted">Select only the working days. Other days will be non-working by default.</small>
                </div>

                <div class="form-check form-switch mb-3">
                  <input id="sameTimeAllDays" v-model="sameTimeForAllDays" class="form-check-input" type="checkbox" />
                  <label class="form-check-label fw-semibold" for="sameTimeAllDays">Use same time for all days</label>
                </div>
                <div v-if="sameTimeForAllDays" class="row g-3">
                  <div class="col-6">
                    <label class="form-label fw-semibold">Shared Start Time *</label>
                    <input type="time" v-model="sharedSchedule.start_time" class="form-control" required />
                  </div>
                  <div class="col-6">
                    <label class="form-label fw-semibold">Shared End Time *</label>
                    <input type="time" v-model="sharedSchedule.end_time" class="form-control" required />
                  </div>
                </div>
              </div>

              <!-- Weekly schedule grid -->
              <div class="week-grid-wrap">
                <div class="week-grid">
                  <div class="week-grid-header" :class="{ 'with-working': scheduleMode === 'custom' }">
                    <span>Day</span>
                    <span v-if="scheduleMode === 'custom'" class="text-center">Working</span>
                    <span>Start Time</span>
                    <span>End Time</span>
                  </div>
                  <div v-for="day in weekDays" :key="day.key" class="week-grid-row" :class="{ 'with-working': scheduleMode === 'custom' }">
                    <span class="day-label">{{ day.label }}</span>
                    <div v-if="scheduleMode === 'custom'" class="text-center">
                      <input type="checkbox" v-model="day.is_working" class="form-check-input" />
                    </div>
                    <input
                        type="time"
                        v-model="day.start_time"
                        class="form-control form-control-sm"
                        :disabled="sameTimeForAllDays"
                        required
                    />
                    <input
                        type="time"
                        v-model="day.end_time"
                        class="form-control form-control-sm"
                        :disabled="sameTimeForAllDays"
                        required
                    />
                  </div>
                </div>
              </div>

              <p v-if="createError" class="text-danger small mt-3 mb-0">{{ createError }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showCreateModal = false">Cancel</button>
              <button type="submit" class="btn btn-ams" :disabled="saving">{{ saving ? 'Creating...' : 'Create Schedule' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth.store'
import api from '@/services/api'

const authStore = useAuthStore()
const isAdmin = computed(
    () => authStore.user?.user_type === 'SUPER_ADMIN'
)
const schedules = ref([])
const currentPage = ref(1)
const lastPage = ref(1)
const businesses = ref([])
const staffList = ref([])
const locationsList = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const createError = ref('')
const bizFilter = ref('')

const showDeleteModal = ref(false)
const showEditModal = ref(false)
const showCreateModal = ref(false)
const selected = ref(null)

const editForm = ref({ working_day: '',  location_code: '', shift_start_time: '', shift_end_time: '', status: 'ACTIVE' })
const editError = ref('')

async function openEdit(schedule) {

  selected.value = schedule

  // Load locations for this schedule's business
  await fetchLocations(schedule.business_code)

  editForm.value = {
    working_days: schedule.working_day || '',
    location_code: schedule.location_code || '',
    start_time: (schedule.shift_start_time || '').slice(0, 5),
    end_time: (schedule.shift_end_time || '').slice(0, 5),
    status: schedule.status || 'ACTIVE',
  }

  editError.value = ''
  showEditModal.value = true
}

async function updateSchedule() {
  saving.value = true
  editError.value = ''
  try {
    const payload = { ...editForm.value }
    payload.start_time = (payload.start_time || '').slice(0, 5)
    payload.end_time = (payload.end_time || '').slice(0, 5)
    if (!payload.location_code) delete payload.location_code
    await api.put(`/user-shift-schedules/${selected.value.code}`, {
      working_day: editForm.value.working_days,
      location_code: editForm.value.location_code || null,
      shift_start_time: editForm.value.start_time,
      shift_end_time: editForm.value.end_time,
      status: editForm.value.status
    })
    showEditModal.value = false
    await refreshSchedules()
  } catch (err) {

    console.log(
        'UPDATE ERROR:',
        err.response?.data
    )

    alert(
        JSON.stringify(
            err.response?.data,
            null,
            2
        )
    )

    editError.value =
        err.response?.data?.message ||
        'Update failed'
  } finally {
    saving.value = false
  }
}

async function refreshSchedules() {
  try {
    let params = {}

    if (isAdmin.value) {
      if (bizFilter.value) {
        params.business_code = bizFilter.value
      }
    } else {
      params.business_code =
          authStore.user?.business_code
    }
      const res = await api.get('/user-shift-schedules', { params })
    schedules.value = res.data.data.data || []
  } catch (_) {}
}

const DAY_KEYS = ['MONDAY','TUESDAY','WEDNESDAY','THURSDAY','FRIDAY','SATURDAY','SUNDAY']
const DAY_LABELS = { MONDAY:'Monday', TUESDAY:'Tuesday', WEDNESDAY:'Wednesday', THURSDAY:'Thursday', FRIDAY:'Friday', SATURDAY:'Saturday', SUNDAY:'Sunday' }

function freshWeekDays() {
  return DAY_KEYS.map(k => ({ key: k, label: DAY_LABELS[k], start_time: '09:00', end_time: '17:00', is_working: true }))
}

const createForm = ref({ business_code: '', user_code: '', location_code: '' })
const weekDays = ref(freshWeekDays())
const scheduleMode = ref('whole_week')
const sameTimeForAllDays = ref(false)
const sharedSchedule = ref({ start_time: '09:00', end_time: '17:00' })

function applySharedTimesToAllDays() {
  weekDays.value = weekDays.value.map(day => ({
    ...day,
    start_time: sharedSchedule.value.start_time,
    end_time: sharedSchedule.value.end_time,
  }))
}

function formatTime(time) {
  if (!time) return '—'

  return new Date(`1970-01-01T${time}`)
      .toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
      })
}

watch(sameTimeForAllDays, (enabled) => {
  if (!enabled) return
  applySharedTimesToAllDays()
})

watch(scheduleMode, (mode) => {
  if (mode === 'whole_week') {
    weekDays.value = weekDays.value.map(day => ({ ...day, is_working: true }))
    return
  }
  weekDays.value = weekDays.value.map(day => ({ ...day, is_working: false }))
})

watch(sharedSchedule, () => {
  if (!sameTimeForAllDays.value) return
  applySharedTimesToAllDays()
}, { deep: true })

async function fetchStaff(business_code) {
  staffList.value = []
  if (!business_code) return
  try {
    const res = await api.get('/users', { params: { business_code } })
    const all = res.data.data?.data || []
    staffList.value = all.filter(u => ['OPERATION_STAFF', 'SERVICE_STAFF'].includes(u.user_type))

    console.log(all)

  } catch (_) {}
}

async function fetchLocations(business_code) {
  locationsList.value = []
  if (!business_code) return
  try {
    const res = await api.get('/business-locations', { params: { business_code } })
    locationsList.value = res.data.data.data || []
  } catch (_) {}
}

async function onBusinessChange() {
  createForm.value.user_code = ''
  createForm.value.location_code = ''
  await Promise.all([fetchStaff(createForm.value.business_code), fetchLocations(createForm.value.business_code)])
}

async function fetchSchedules() {
  loading.value = true
  error.value = ''
  try {
    let params = {}

    if (isAdmin.value) {

      if (bizFilter.value) {
        params.business_code = bizFilter.value
      }

    } else {

      params.business_code =
          authStore.user?.business_code
    }

    const [scheduleRes, usersRes] = await Promise.all([
      api.get('/user-shift-schedules', {
        params: {
          ...params,
          page: currentPage.value
        }
      }),
      api.get('/users', { params })
    ])

    console.log('Schedules API:', scheduleRes.data)
    console.log('Users API:', usersRes.data)

    const users = usersRes.data.data.data || []

    const staffNameByCode = new Map(
        users.map((user) => [user.code, user.name.trim()])
    )

    const scheduleData = scheduleRes.data.data

    schedules.value = (scheduleData.data || []).map((schedule) => ({
      ...schedule,
      name: staffNameByCode.get(schedule.user_code) || ''
    }))

    currentPage.value = scheduleData.current_page
    lastPage.value = scheduleData.last_page
  } catch (err) {
    error.value =
        err.response?.data?.message || 'Failed to load schedules'
  } finally {
    loading.value = false
  }
}

async function changePage(page) {
  if (page < 1 || page > lastPage.value) return

  currentPage.value = page
  await fetchSchedules()
}


function openDelete(schedule) {
  selected.value = schedule
  showDeleteModal.value = true
}

async function deleteSchedule() {
  saving.value = true
  try {
    await api.delete(`/user-shift-schedules/${selected.value.code}`)
    showDeleteModal.value = false
    await fetchSchedules()
  } catch (err) {
    error.value = err.response?.data?.message || 'Delete failed'
  } finally {
    saving.value = false
  }
}

async function createSchedule() {
  createError.value = ''
  const business_code = isAdmin.value ? createForm.value.business_code : (authStore.user?.business_code || '')

  if (scheduleMode.value === 'custom' && !weekDays.value.some(d => d.is_working)) {
    createError.value = 'Select at least one working day for custom setup'
    return
  }

  let daysToSave = weekDays.value

  if (scheduleMode.value === 'custom') {
    daysToSave = weekDays.value.filter(
        d => d.is_working
    )
  }

  const entries = daysToSave.map(d => ({
    business_code,
    user_code: createForm.value.user_code,
    working_day: d.key,
    location_code: createForm.value.location_code || null,
    shift_start_time: d.start_time,
    shift_end_time: d.end_time,
    status: 'ACTIVE'
  }))

  saving.value = true
  try {
    console.log(
        'PAYLOAD:',
        JSON.stringify(entries, null, 2)
    )
    await api.post('/user-shift-schedules', entries)
    showCreateModal.value = false
    createForm.value = { business_code: '', user_code: '', employee_type: '', location_code: '' }
    weekDays.value = freshWeekDays()
    scheduleMode.value = 'whole_week'
    sameTimeForAllDays.value = false
    sharedSchedule.value = { start_time: '09:00', end_time: '17:00' }
    staffList.value = []
    locationsList.value = []
    await fetchSchedules()
  } catch (err) {

    console.log(
        'VALIDATION ERROR:',
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
    saving.value = false
  }
}

onMounted(async () => {
  if (!isAdmin.value) {
    const bizCode = authStore.user?.business_code || ''
    createForm.value.business_code = bizCode
    await Promise.all([fetchSchedules(), fetchStaff(bizCode), fetchLocations(bizCode)])
    return
  }
  const [_, bizRes] = await Promise.allSettled([fetchSchedules(), api.get('/businesses')])
  if (bizRes.status === 'fulfilled') businesses.value = bizRes.value.data.data.data || []
})
</script>

<style scoped>
.week-grid-wrap {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
}

.week-grid {
  width: 100%;
}

.week-grid-header,
.week-grid-row {
  display: grid;
  grid-template-columns: 110px 1fr 1fr;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
}

.week-grid-header.with-working,
.week-grid-row.with-working {
  grid-template-columns: 110px 90px 1fr 1fr;
}

.week-grid-row > .form-control {
  min-width: 0;
}

.week-grid-header {
  background: #f8fafc;
  font-size: 12px;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  border-bottom: 1px solid #e2e8f0;
}

.week-grid-row {
  border-bottom: 1px solid #f1f5f9;
  transition: background 0.15s;
}

.week-grid-row:last-child {
  border-bottom: none;
}

.day-label {
  font-size: 14px;
  font-weight: 600;
  color: #1e293b;
}
</style>