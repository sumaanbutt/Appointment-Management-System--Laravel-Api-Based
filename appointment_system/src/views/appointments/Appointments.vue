<template>
  <div class="ams-page">

    <div class="d-flex align-items-center justify-content-between">
      <div>
        <h2 class="mb-0">Appointments</h2>
        <p class="text-muted small mb-0">Manage all appointment requests</p>
      </div>
      <router-link to="/admin/appointments/create" class="btn btn-ams">+ New Appointment</router-link>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <select v-model="statusFilter" class="form-select" style="max-width:180px">
        <option value="">All Status</option>
        <option value="PENDING">Pending</option>
        <option value="APPROVED">Approved</option>
        <option value="REJECTED">Rejected</option>
        <option value="RESCHEDULED">Rescheduled</option>
        <option value="COMPLETED">Completed</option>
        <option value="CANCELLED">Cancelled</option>
        <option value="IN_PROGRESS">In Progress</option>
      </select>
      <input v-model="search" class="form-control" style="max-width:260px" placeholder="Search by code or client..." />
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0 overflow-auto">
        <div v-if="loading" class="text-center text-muted py-4">Loading...</div>
        <div v-else-if="error" class="alert alert-danger m-3 py-2">{{ error }}</div>
        <table v-else class="table table-hover ams-table mb-0">
          <thead class="table-light">
          <tr>
            <th>Business Name</th>
            <th>Service Name</th>
            <th>Location</th>
            <th>Notes</th>
            <th>Created By</th>
            <th>Approved By</th>
            <th>Start Date</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Status</th>
            <th class="pe-3" style="width:280px">Actions</th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="appt in filteredAppointments" :key="appt.code">
            <td>{{ appt.business?.name || '?' }}</td>
            <td>{{ appt.service?.service_name || '?' }}</td>
            <td>{{ [appt.location.apartment, appt.location.street, appt.location.address, appt.location.city ].filter(Boolean).join(', ') || '?' }}</td>
            <td>{{ appt.notes || '-' }}</td>
            <td>{{ appt.created_by?.name || '-' }}</td>
            <td>{{ appt.approved_by?.name || '-' }}</td>
            <td>{{ formatDate(appt.appointment_start_date || '-') }}</td>
            <td>{{ formatTime(appt.start_time || '-') }}</td>
            <td>{{ formatTime(appt.end_time || '-') }}</td>
            <td>
                <span :class="['ams-badge', appt.status]">
                  {{ appt.status.replaceAll('_', ' ').toLowerCase() }}
                </span>
            </td>
            <td class="pe-3">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <button class="dropdown-item" @click="openDetails(appt)">
                      <i class="bi bi-eye me-2"></i> View
                    </button>
                  </li>
                  <li v-if="appt.status === 'PENDING'">
                    <button class="dropdown-item text-success" @click="openApprovalDialog(appt)">
                      <i class="bi bi-check-circle me-2"></i> Approve
                    </button>
                  </li>
                  <li v-if="appt.status === 'APPROVED'">
                    <button class="dropdown-item text-info" @click="changeStatus(appt,'IN_PROGRESS')">
                      <i class="bi bi-play-circle me-2"></i> Start
                    </button>
                  </li>
                  <li v-if="appt.status === 'IN_PROGRESS'">
                    <button class="dropdown-item text-success" @click="changeStatus(appt,'COMPLETED')">
                      <i class="bi bi-check2-all me-2"></i> Complete
                    </button>
                  </li>
                  <li v-if="['PENDING','APPROVED'].includes(appt.status)">
                    <button class="dropdown-item text-primary" @click="openReschedule(appt)">
                      <i class="bi bi-calendar-event me-2"></i> Reschedule
                    </button>
                  </li>
                  <li v-if="['PENDING','APPROVED'].includes(appt.status)">
                    <button class="dropdown-item text-danger" @click="changeStatus(appt,'REJECTED')">
                      <i class="bi bi-x-circle me-2"></i> Reject
                    </button>
                  </li>
                  <li v-if="['PENDING','APPROVED'].includes(appt.status)">
                    <button class="dropdown-item text-secondary" @click="changeStatus(appt,'CANCELLED')">
                      <i class="bi bi-slash-circle me-2"></i> Cancel
                    </button>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
          <tr v-if="filteredAppointments.length === 0">
            <td colspan="11" class="text-center text-muted py-4">No appointments found</td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>

    <nav class="mt-3">
      <ul class="pagination justify-content-end">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="changePage(currentPage - 1)">Previous</button>
        </li>
        <li v-for="page in lastPage" :key="page" class="page-item" :class="{ active: currentPage === page }">
          <button class="page-link" @click="changePage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === lastPage }">
          <button class="page-link" @click="changePage(currentPage + 1)">Next</button>
        </li>
      </ul>
    </nav>

    <div v-if="showDetails" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Appointment Details</h5>
            <button type="button" class="btn-close" @click="showDetails = false"></button>
          </div>
          <div class="modal-body" v-if="selected">
            <dl class="row mb-3">
              <dt class="col-5 text-muted">Code</dt>
              <dd class="col-7"><code>{{ selected.appointment_code }}</code></dd>
              <dt class="col-5 text-muted">Client</dt>
              <dd class="col-7">{{ selected.client_name || selected.client_code }}</dd>
              <dt class="col-5 text-muted">Service</dt>
              <dd class="col-7">{{ selected.service_name || selected.service_code }}</dd>
              <dt class="col-5 text-muted">Date</dt>
              <dd class="col-7">{{ selected.appointment_start_date }}</dd>
              <dt class="col-5 text-muted">Start</dt>
              <dd class="col-7">{{ selected.start_time }}</dd>
              <dt class="col-5 text-muted">End</dt>
              <dd class="col-7">{{ selected.end_time }}</dd>
              <dt class="col-5 text-muted">Status</dt>
              <dd class="col-7"><span :class="['ams-badge', selected.status]">{{ selected.status }}</span></dd>
              <template v-if="selected.notes">
                <dt class="col-5 text-muted">Notes</dt>
                <dd class="col-7">{{ selected.notes }}</dd>
              </template>
            </dl>
            <hr class="my-2" />
            <div class="fw-semibold mb-2" style="font-size:13px">History</div>
            <div v-if="historyLoading" class="text-muted small text-center py-2">Loading...</div>
            <ul v-else-if="appointmentHistory.length" class="list-unstyled mb-0">
              <li v-for="h in appointmentHistory" :key="h.id" class="d-flex gap-2 align-items-center mb-1 flex-wrap">
                <span class="text-muted" style="font-size:11px;min-width:80px">{{ h.created_at?.split('T')[0] }}</span>
                <span :class="['ams-badge', h.action]">{{ h.action }}</span>
                <span class="text-muted small">by {{ h.changed_by || '?' }}</span>
              </li>
            </ul>
            <p v-else class="text-muted small mb-0">No history yet</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showDetails = false">Close</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="showReschedule" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Reschedule Appointment</h5>
            <button type="button" class="btn-close" @click="showReschedule = false"></button>
          </div>
          <form @submit.prevent="submitReschedule">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">New Start Date *</label>
                  <input type="date" v-model="rescheduleForm.appointment_start_date" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">New End Date *</label>
                  <input type="date" v-model="rescheduleForm.appointment_end_date" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Start Time *</label>
                  <input type="time" v-model="rescheduleForm.start_time" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">End Time *</label>
                  <input type="time" v-model="rescheduleForm.end_time" class="form-control" required />
                </div>
              </div>
              <div class="mt-3">
                <label class="form-label fw-semibold">Reason</label>
                <textarea v-model="rescheduleForm.reason" class="form-control" placeholder="Reason for reschedule" rows="3"></textarea>
              </div>
              <p v-if="rescheduleError" class="text-danger small mt-2 mb-0">{{ rescheduleError }}</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="showReschedule = false">Cancel</button>
              <button type="submit" class="btn btn-ams" :disabled="saving">{{ saving ? 'Sending...' : 'Submit Reschedule' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div v-if="showApproval" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5);z-index:1050">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Approve Appointment &mdash; Staff Availability</h5>
            <button type="button" class="btn-close" @click="closeApprovalDialog"></button>
          </div>
          <div class="modal-body">

            <div class="bg-light rounded p-3 mb-3 d-flex flex-wrap gap-3" v-if="selected">
              <div><span class="text-muted small">Service Name</span><div>{{ selected.service?.service_name }}</div></div>
              <div><span class="text-muted small">Start Date</span><div>{{ formatDate(selected.appointment_start_date) }}</div></div>
              <div><span class="text-muted small">End Date</span><div>{{ formatDate(selected.appointment_end_date) }}</div></div>
              <div><span class="text-muted small">Start Time</span><div>{{ formatTime(selected.start_time) }}</div></div>
              <div><span class="text-muted small">End Time</span><div>{{ formatTime(selected.end_time) }}</div></div>
              <div><span class="text-muted small">Location</span><div>{{ selected.location?.city || '—' }} &bull; {{ selected.location?.address || '—' }}</div></div>
            </div>

            <div v-if="availabilityLoading" class="text-center text-muted py-4">
              <div class="spinner-border spinner-border-sm me-2"></div> Checking staff availability...
            </div>

            <div v-else-if="availabilityError" class="alert alert-warning py-2 mb-3">{{ availabilityError }}</div>

            <template v-else-if="!showRescheduleInApproval">
              <div v-if="slotAlreadyBooked" class="alert alert-danger py-2 mb-3">
                This slot is already booked.
                <span v-if="conflictingAppointments.length"> Conflicting appointments: {{ conflictingAppointments.join(', ') }}</span>
              </div>

              <div v-if="availableStaff.length > 0" class="mb-4">
                <p class="fw-semibold mb-2">Available staff for this slot:</p>
                <div class="list-group mb-3">
                  <label
                      v-for="s in availableStaff"
                      :key="s.user_code"
                      class="list-group-item list-group-item-action d-flex align-items-center gap-3 cursor-pointer"
                      :class="{ 'bg-success-subtle text-success-emphasis border-success': approvalSelectedStaff === s.user_code }"
                      @click="approvalSelectedStaff = s.user_code"
                  >
                    <input type="radio" :value="s.user_code" v-model="approvalSelectedStaff" class="form-check-input mt-0" />
                    <div>
                      <div class="fw-semibold">{{ s.staff_name || s.user_name || s.user_code }}</div>
                      <small class="text-muted">{{ s.working_day }} &bull; {{ formatTime(s.shift_start_time) }} - {{ formatTime(s.shift_end_time) }}</small>
                    </div>
                  </label>
                </div>
              </div>

              <div v-if="engagedStaff.length > 0" class="mb-4">
                <p class="fw-semibold mb-2 text-danger">Engaged staff during this slot (Busy):</p>
                <div class="list-group">
                  <label
                      v-for="s in engagedStaff"
                      :key="`engaged-${s.user_code}`"
                      class="list-group-item list-group-item-action d-flex align-items-start gap-3 cursor-pointer border-danger p-3 mb-2"
                      :class="{ 'bg-danger-subtle text-danger-emphasis': approvalSelectedStaff === s.user_code }"
                      @click="approvalSelectedStaff = s.user_code"
                  >
                    <input type="radio" :value="s.user_code" v-model="approvalSelectedStaff" class="form-check-input mt-1 border-danger" />
                    <div class="flex-grow-1">
                      <div class="fw-semibold d-flex align-items-center gap-2 mb-1">
                        <span>{{ s.staff_name || s.user_name || s.user_code }}</span>
                        <span class="badge bg-danger">Busy</span>
                      </div>

                      <div v-for="a in s.appointments" :key="a.appointment_code" class="small text-danger-emphasis mt-1 bg-white p-2 rounded-2 border border-danger-subtle" style="font-size:12px;">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Conflict Appt: <code>{{ a.appointment_code }}</code>
                        &bull; Time: {{ formatTime(a.start_time) }} - {{ formatTime(a.end_time) }}
                      </div>
                    </div>
                  </label>
                </div>
              </div>

              <div v-if="approvalSelectedStaff && engagedStaff.some(s => s.user_code === approvalSelectedStaff)" class="alert alert-danger py-2 mt-2">
                Warning: selected staff already has another appointment during this time slot. The button below will force assign this staff.
              </div>

              <!--Charges-->

              <div v-if="autoCharges.length || optionalCharges.length" class="card border-0 bg-light mb-3">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">Appointment Charges</h6>

                  <div v-if="autoCharges.length" class="mb-3">
                    <div class="fw-semibold text-success mb-2">Automatically Applied Charges</div>
<!--                    <div v-for="charge in autoCharges" :key="charge.charge_code" class="form-check mb-2">-->
<!--                      <input checked disabled type="checkbox" class="form-check-input">-->
<!--                      <label class="form-check-label">-->
<!--                        {{ charge.name }}-->
<!--                        <span class="text-muted">( {{ charge.charge_uom === 'percentage' ? `${charge.charge_value}%` : charge.charge_value }} )</span>-->
<!--                        <span class="badge text-bg-success ms-2">Auto</span>-->
<!--                      </label>-->
<!--                    </div>-->

                    <div v-for="charge in autoCharges"
                         :key="charge.code"
                         class="form-check mb-2">

                      <input
                          :id="`auto-charge-${charge.code}`"
                          v-model="selectedChargeCodes"
                          :value="charge.code"
                          type="checkbox"
                          class="form-check-input"
                      >

                      <label
                          class="form-check-label"
                          :for="`auto-charge-${charge.code}`">
                              {{ charge.name }}

                        <span class="text-muted">
                        ({{ charge.charge_uom === 'PERCENTAGE'
                            ? `${charge.charge_value}%`
                            : charge.charge_value }})
                        </span>

                        <span class="badge text-bg-success ms-2">Auto</span>
                      </label>
                    </div>
                  </div>

                  <div v-if="optionalCharges.length">
                    <div class="fw-semibold mb-2">Optional Charges</div>
                    <div v-for="charge in optionalCharges" :key="charge.charge_code" class="form-check mb-2">
                      <input :id="`charge-${charge.code}`" v-model="selectedChargeCodes" :value="charge.code" type="checkbox" class="form-check-input" />
                      <label class="form-check-label" :for="`charge-${charge.charge_code}`">
                        {{ charge.name }}
                        <span class="text-muted">( {{ charge.charge_uom === 'percentage' ? `${charge.charge_value}%` : charge.charge_value }} )</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="availableStaff.length === 0" class="alert alert-warning py-2">
                No free staff available for this slot. You may reschedule the appointment and propose one of the alternatives below.
              </div>

              <div v-if="availableStaff.length === 0" class="card mb-3">
                <div class="card-body">
                  <h6 class="fw-semibold mb-3">Choose Alternative Option</h6>
                  <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-outline-primary" :class="{ active: selectedAlternativeType === 'same_location' }" @click="selectedAlternativeType = 'same_location'">
                      Different Time Same Location
                    </button>
                    <button class="btn btn-outline-primary" :class="{ active: selectedAlternativeType === 'other_location' }" @click="selectedAlternativeType = 'other_location'">
                      Different Location Same Time
                    </button>
                    <button class="btn btn-outline-primary" :class="{ active: selectedAlternativeType === 'service_location' }" @click="selectedAlternativeType = 'service_location'">
                      Service Available At Other Locations
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="availableStaff.length === 0 && selectedAlternativeType === 'same_location' && alternativeTimeSameLocation.length" class="mb-3">
                <div class="fw-semibold mb-2">Other service staff at different time (same location)</div>
                <div class="list-group">
                  <div v-for="s in alternativeTimeSameLocation" :key="`same-${s.user_code}-${s.start_time}-${s.end_time}`" class="list-group-item">
                    <div class="fw-semibold d-flex align-items-center gap-2">
                      <span>{{ s.staff_name || s.user_code }}</span>
                      <span v-if="isRecommendedAlternative(selected?.location_code, s.start_time, s.end_time, s.user_code)" class="badge text-bg-success">Recommended</span>
                    </div>
                    <div class="small text-muted">{{ s.user_code }} &bull; {{ formatTime(s.start_time) }} - {{ formatTime(s.end_time) }}</div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" @click="selectedAlternative = { location_code: selected?.location_code, start_time: s.start_time, end_time: s.end_time }">
                      Select
                    </button>
                  </div>
                </div>
              </div>

              <div v-if="availableStaff.length === 0 && selectedAlternativeType === 'other_location' && alternativeLocationSameTime.length" class="mb-3">
                <div class="fw-semibold mb-2">Other locations at the same time slot</div>
                <div class="border rounded p-2 mb-2" v-for="loc in alternativeLocationSameTime" :key="`loc-${loc.location_code}`">
                  <div class="small mb-2">Location: {{ selected.location?.address || '-' }} &bull; {{ selected.location?.city || '-' }}</div>
                  <button type="button" class="btn btn-sm btn-outline-primary mb-2" @click="selectedAlternative = { location_code: loc.location_code, start_time: selected?.start_time, end_time: selected?.end_time }">
                    Select this Location
                  </button>
                  <div class="list-group">
                    <div v-for="s in loc.staff" :key="`loc-staff-${loc.location_code}-${s.user_code}-${s.start_time}-${s.end_time}`" class="list-group-item">
                      <div class="fw-semibold d-flex align-items-center gap-2">
                        <span>{{ s.staff_name || s.user_code }}</span>
                      </div>
                      <div class="small text-muted">{{ formatTime(s.start_time) }} - {{ formatTime(s.end_time) }}</div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="availableStaff.length === 0 && selectedAlternativeType === 'service_location' && serviceLocationsAlternatives.length" class="mb-3">
                <div class="fw-semibold mb-2">Selected service available at other locations</div>
                <div class="border rounded p-2 mb-2" v-for="loc in serviceLocationsAlternatives" :key="`svc-${loc.location_code}`">
                  <div class="small fw-semibold">Location: {{ loc.location_code }}</div>
                  <div class="small text-muted mb-2" v-if="loc.location?.city || loc.location?.address">
                    {{ loc.location?.city || '?' }} &bull; {{ loc.location?.address || '?' }}
                  </div>
                  <div class="small text-muted mb-2">Services: {{ (loc.matched_service_codes || []).join(', ') || '?' }}</div>

                  <div v-if="loc.available_staff_same_slot?.length" class="mb-2">
                    <div class="small fw-semibold">Available at same slot</div>
                    <div class="list-group">
                      <div v-for="s in loc.available_staff_same_slot" :key="`svc-same-${loc.location_code}-${s.user_code}-${s.start_time}-${s.end_time}`" class="list-group-item">
                        <div class="fw-semibold d-flex align-items-center gap-2">
                          <span>{{ s.staff_name || s.user_code }}</span>
                          <span v-if="isRecommendedAlternative(loc.location_code, s.start_time, s.end_time, s.user_code)" class="badge text-bg-success">Recommended</span>
                        </div>
                        <div class="small text-muted">{{ s.user_code }} &bull; {{ formatTime(s.start_time) }} - {{ formatTime(s.end_time) }}</div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" @click="prefillApprovalReschedule({ locationCode: loc.location_code, startTime: selected?.start_time, endTime: selected?.end_time })">
                          Select Location & Current Time
                        </button>
                      </div>
                    </div>
                  </div>

                  <div v-if="loc.alternative_staff_time_slots?.length">
                    <div class="small fw-semibold">Alternative time slots</div>
                    <div class="list-group">
                      <div v-for="s in loc.alternative_staff_time_slots" :key="`svc-alt-${loc.location_code}-${s.user_code}-${s.start_time}-${s.end_time}`" class="list-group-item">
                        <div class="fw-semibold d-flex align-items-center gap-2">
                          <span>{{ s.staff_name || s.user_code }}</span>
                          <span v-if="isRecommendedAlternative(loc.location_code, s.start_time, s.end_time, s.user_code)" class="badge text-bg-success">Recommended</span>
                        </div>
                        <div class="small text-muted">{{ s.user_code }} &bull; {{ formatTime(s.start_time) }} - {{ formatTime(s.end_time) }}</div>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" @click="prefillApprovalReschedule({ locationCode: loc.location_code, startTime: s.start_time, endTime: s.end_time })">
                          Select this Location & Slot
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="selectedAlternative && availableStaff.length === 0" class="alert alert-success mt-3">
                <div class="fw-semibold">Selected Alternative</div>
                <div>Location: {{ selectedAlternative.location_code }}</div>
                <div>Time: {{ formatTime(selectedAlternative.start_time) }} - {{ formatTime(selectedAlternative.end_time) }}</div>
                <button class="btn btn-primary btn-sm mt-2" @click="prefillApprovalReschedule(selectedAlternative)">
                  Continue With Reschedule
                </button>
              </div>
              <p v-if="approvalError" class="text-danger small mt-2 mb-0">{{ approvalError }}</p>
            </template>

            <template v-if="showRescheduleInApproval">
              <div class="alert alert-info py-2 mb-3">
                Fill in a new date &amp; time to propose to the client.
              </div>
              <div class="row g-3">
                <div class="col-6">
                  <label class="form-label fw-semibold">New Start Date *</label>
                  <input type="date" v-model="approvalRescheduleForm.appointment_start_date" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">New End Date *</label>
                  <input type="date" v-model="approvalRescheduleForm.appointment_end_date" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">Start Time *</label>
                  <input type="time" v-model="approvalRescheduleForm.start_time" class="form-control" required />
                </div>
                <div class="col-6">
                  <label class="form-label fw-semibold">End Time *</label>
                  <input type="time" v-model="approvalRescheduleForm.end_time" class="form-control" required />
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Location Code</label>
                  <input type="text" v-model="approvalRescheduleForm.location_code" class="form-control" />
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Reason / Notes</label>
                  <textarea v-model="approvalRescheduleForm.notes" class="form-control" rows="2" placeholder="Reason for rescheduling..."></textarea>
                </div>
              </div>
              <p v-if="approvalError" class="text-danger small mt-2 mb-0">{{ approvalError }}</p>
            </template>
          </div>

          <div class="modal-footer gap-2">
            <button type="button" class="btn btn-secondary" @click="closeApprovalDialog">Cancel</button>

            <button
                v-if="!showRescheduleInApproval && (availableStaff.length > 0 || engagedStaff.length > 0)"
                class="btn"
                :class="engagedStaff.some(e => e.user_code === approvalSelectedStaff) ? 'btn-danger' : 'btn-success'"
                :disabled="!approvalSelectedStaff || approvalSaving"
                @click="handleMainApprovalAction"
            >
              {{ approvalSaving ? 'Processing...' : (engagedStaff.some(e => e.user_code === approvalSelectedStaff) ? 'Force Approve & Reassign' : 'Approve & Assign Staff') }}
            </button>

            <button
                v-if="!showRescheduleInApproval && availableStaff.length === 0 && engagedStaff.length === 0 && !availabilityLoading && !availabilityError"
                class="btn btn-primary"
                @click="showRescheduleInApproval = true"
            >Send Reschedule Request to Client</button>

            <button
                v-if="!showRescheduleInApproval && availableStaff.length === 0 && engagedStaff.length > 0"
                class="btn btn-outline-primary"
                @click="showRescheduleInApproval = true"
            >Reschedule Instead</button>

            <button
                v-if="showRescheduleInApproval"
                class="btn btn-outline-secondary"
                @click="showRescheduleInApproval = false"
            >&thinsp;&larr; Back</button>

            <button
                v-if="showRescheduleInApproval"
                class="btn btn-primary"
                :disabled="approvalSaving"
                @click="submitApprovalReschedule"
            >{{ approvalSaving ? 'Sending...' : 'Send Reschedule Request' }}</button>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>


<script setup>
import { computed, reactive, ref, onMounted } from 'vue'
import api from '@/services/api'
import formatDate from "@/services/formatDate.ts";
import formatTime from "@/services/formatTime.ts";

const appointments = ref([])
const loading = ref(true)
const saving = ref(false)
const error = ref('')
const rescheduleError = ref('')
const currentPage = ref(1)
const lastPage = ref(1)

const search = ref('')
const statusFilter = ref('')

const showDetails = ref(false)
const showReschedule = ref(false)
const selected = ref(null)
const historyLoading = ref(false)
const appointmentHistory = ref([])

const rescheduleForm = reactive({
  appointment_start_date: '',
  appointment_end_date: '',
  start_time: '',
  end_time: '',
  reason: '',
})

async function fetchAppointments() {
  loading.value = true
  error.value = ''
  try {
    const res = await api.get('/appointments',{
      params: { page: currentPage.value }
    })
    appointments.value = res.data.data.data || []
    currentPage.value = res.data.data.current_page
    lastPage.value = res.data.data.last_page
  } catch(err){
    error.value = err.response?.data?.message || 'Failed loading appointments'
  } finally {
    loading.value = false
  }
}

async function changePage(page) {
  if (page < 1 || page > lastPage.value) return
  currentPage.value = page
  await fetchAppointments()
}

const filteredAppointments = computed(() => {
  return appointments.value.filter(a => {
    const s = search.value.toLowerCase()
    const matchSearch = !s || (a.code || '').toLowerCase().includes(s) || (a.client_code || '').toLowerCase().includes(s)
    const matchStatus = !statusFilter.value || a.status === statusFilter.value
    return matchSearch && matchStatus
  })
})

async function openDetails(appt) {
  selected.value = appt
  appointmentHistory.value = []
  showDetails.value = true
  historyLoading.value = true
  try {
    const res = await api.get(`/appointments/${appt.code}/histories `)
    appointmentHistory.value = res.data.data.data || []
  } catch (_) {
  } finally {
    historyLoading.value = false
  }
}

function openReschedule(appt) {
  selected.value = appt
  rescheduleForm.appointment_start_date = appt.appointment_start_date || ''
  rescheduleForm.appointment_end_date = appt.appointment_end_date || ''
  rescheduleForm.start_time = appt.start_time || ''
  rescheduleForm.end_time = appt.end_time || ''
  rescheduleForm.reason = ''
  rescheduleError.value = ''
  showReschedule.value = true
}

async function changeStatus(appt, status) {
  try {
    await api.patch(`/appointments/${appt.code}/status`, { status })
    appt.status = status
  } catch (err) {
    error.value = err.response?.data?.message || 'Status update failed'
  }
}

async function submitReschedule() {
  saving.value = true
  rescheduleError.value = ''
  try {
    await api.post(`/appointments/${selected.value.code}/reschedule`, rescheduleForm)
    showReschedule.value = false
    await fetchAppointments()
  } catch (err) {
    rescheduleError.value = err.response?.data?.message || 'Reschedule failed'
  } finally {
    saving.value = false
  }
}

// ==================== Approval Flow Pipeline ====================

const showApproval = ref(false)
const availabilityLoading = ref(false)
const availabilityError = ref('')
const availableStaff = ref([])
const engagedStaff = ref([])
const approvalSaving = ref(false)
const approvalError = ref('')
const showRescheduleInApproval = ref(false)
const approvalSelectedStaff = ref('')
const slotAlreadyBooked = ref(false)
const conflictingAppointments = ref([])
const alternativeTimeSameLocation = ref([])
const alternativeLocationSameTime = ref([])
const serviceLocationsAlternatives = ref([])
const recommendedAlternativeKey = ref('')
const autoCharges = ref([])
const optionalCharges = ref([])
const selectedChargeCodes = ref([])
const selectedAlternativeType = ref('')
const selectedAlternative = ref(null)

const approvalRescheduleForm = reactive({
  appointment_start_date: '',
  appointment_end_date: '',
  start_time: '',
  end_time: '',
  location_code: '',
  notes: '',
})

function toDateInput(v) {
  return String(v || '').split('T')[0] || ''
}

function toTimeInput(v) {
  return String(v || '').slice(0, 5) || ''
}

function slotTimeValue(v) {
  const normalized = toTimeInput(v)
  const [h, m] = normalized.split(':').map(Number)
  if (Number.isNaN(h) || Number.isNaN(m)) return Number.MAX_SAFE_INTEGER
  return h * 60 + m
}

function slotKey(locationCode, startTime, endTime, userCode) {
  return [locationCode || '', toTimeInput(startTime), toTimeInput(endTime), userCode || ''].join('|')
}

function isRecommendedAlternative(locationCode, startTime, endTime, userCode) {
  return recommendedAlternativeKey.value !== '' && recommendedAlternativeKey.value === slotKey(locationCode, startTime, endTime, userCode)
}

function sortAlternativeSlots(slots, locationCode) {
  return [...(slots || [])].sort((a, b) => {
    const aPriority = isRecommendedAlternative(locationCode, a.start_time, a.end_time, a.user_code) ? 0 : 1
    const bPriority = isRecommendedAlternative(locationCode, b.start_time, b.end_time, b.user_code) ? 0 : 1
    if (aPriority !== bPriority) return aPriority - bPriority
    return slotTimeValue(a.start_time) - slotTimeValue(b.start_time)
  })
}

function applyRecommendationOrdering() {
  alternativeTimeSameLocation.value = sortAlternativeSlots(alternativeTimeSameLocation.value, selected.value?.location_code)
  alternativeLocationSameTime.value = (alternativeLocationSameTime.value || []).map((loc) => ({
    ...loc,
    staff: sortAlternativeSlots(loc.staff, loc.location_code),
  }))
  serviceLocationsAlternatives.value = (serviceLocationsAlternatives.value || []).map((loc) => ({
    ...loc,
    available_staff_same_slot: sortAlternativeSlots(loc.available_staff_same_slot, loc.location_code),
    alternative_staff_time_slots: sortAlternativeSlots(loc.alternative_staff_time_slots, loc.location_code),
  }))
}

function pickRecommendedAlternative() {
  const candidates = []
  for (const s of alternativeTimeSameLocation.value || []) {
    candidates.push({
      key: slotKey(selected.value?.location_code, s.start_time, s.end_time, s.user_code),
      time: slotTimeValue(s.start_time),
      priority: 1,
    })
  }
  for (const loc of alternativeLocationSameTime.value || []) {
    for (const s of loc.staff || []) {
      candidates.push({
        key: slotKey(loc.location_code, s.start_time, s.end_time, s.user_code),
        time: slotTimeValue(s.start_time),
        priority: 2,
      })
    }
  }
  for (const loc of serviceLocationsAlternatives.value || []) {
    for (const s of loc.available_staff_same_slot || []) {
      candidates.push({
        key: slotKey(loc.location_code, s.start_time, s.end_time, s.user_code),
        time: slotTimeValue(s.start_time),
        priority: 3,
      })
    }
    for (const s of loc.alternative_staff_time_slots || []) {
      candidates.push({
        key: slotKey(loc.location_code, s.start_time, s.end_time, s.user_code),
        time: slotTimeValue(s.start_time),
        priority: 4,
      })
    }
  }
  candidates.sort((a, b) => a.time - b.time || a.priority - b.priority)
  recommendedAlternativeKey.value = candidates[0]?.key || ''
  applyRecommendationOrdering()
}

async function openApprovalDialog(appt) {
  selected.value = appt
  showApproval.value = true
  await loadApprovalAvailability(appt)
}

async function loadApprovalAvailability(appt) {
  selected.value = appt
  showApproval.value = true
  availabilityError.value = ''
  availableStaff.value = []
  engagedStaff.value = []
  autoCharges.value = []
  optionalCharges.value = []
  selectedAlternativeType.value = ''
  selectedAlternative.value = null
  selectedChargeCodes.value = []
  slotAlreadyBooked.value = false
  approvalSelectedStaff.value = ''
  conflictingAppointments.value = []
  alternativeTimeSameLocation.value = []
  alternativeLocationSameTime.value = []
  serviceLocationsAlternatives.value = []
  recommendedAlternativeKey.value = ''
  approvalError.value = ''
  showRescheduleInApproval.value = false
  approvalRescheduleForm.appointment_start_date = toDateInput(appt.appointment_start_date) || ''
  approvalRescheduleForm.appointment_end_date = toDateInput(appt.appointment_end_date) || ''
  approvalRescheduleForm.start_time = toTimeInput(appt.start_time) || ''
  approvalRescheduleForm.end_time = toTimeInput(appt.end_time) || ''
  approvalRescheduleForm.location_code = appt.location_code || ''
  approvalRescheduleForm.notes = ''

  availabilityLoading.value = true
  try {
    const res = await api.get(`/appointments/${appt.code}/availability`)
    console.log("RAW CHARGES OBJECT FROM API:", res.data.data?.charges)
    console.log("AUTO ARRAY:", res.data.data?.charges?.auto_apply)
    console.log("OPTIONAL ARRAY:", res.data.data?.charges?.optional)
    availableStaff.value = res.data.data?.available_staff || []
    engagedStaff.value = res.data.data?.engaged_staff || []
    autoCharges.value = res.data.data?.charges?.auto_apply || []
    selectedChargeCodes.value = autoCharges.value.map(c => c.code)
    optionalCharges.value = res.data.data?.charges?.optional || []

    console.log('OPTIONAL CHARGES', optionalCharges.value)

    const alternatives = res.data.data?.alternatives || {}
    alternativeTimeSameLocation.value = alternatives.different_time_same_location || []
    alternativeLocationSameTime.value = Object.entries(alternatives.different_location_same_time || {}).map(([location_code, staff]) => ({
      location_code,
      staff
    }))

    serviceLocationsAlternatives.value = alternatives.selected_service_other_locations || []
    pickRecommendedAlternative()
    slotAlreadyBooked.value = res.data.data?.slot_already_booked || false
    conflictingAppointments.value = res.data.data?.conflicting_appointments || []
  } catch (err) {
    availabilityError.value = err.response?.data?.message || 'Could not check availability'
  } finally {
    availabilityLoading.value = false
  }
}

function closeApprovalDialog() {
  showApproval.value = false
  showRescheduleInApproval.value = false
  selectedAlternativeType.value = ''
  selectedAlternative.value = null
  approvalError.value = ''
}

// Router to switch between standard validation payload vs bypass constraint force reassign paths
async function handleMainApprovalAction() {
  if (!approvalSelectedStaff.value) return
  const isEngaged = engagedStaff.value.some(s => s.user_code === approvalSelectedStaff.value);

  if (isEngaged) {
    await forceAssignStaff(approvalSelectedStaff.value);
  } else {
    await submitApproveWithStaff();
  }
}

async function submitApproveWithStaff() {
  if (!approvalSelectedStaff.value) return
  approvalSaving.value = true
  approvalError.value = ''
  try {

    console.log(
        'Selected Charges:',
        selectedChargeCodes.value
    )

    await api.post(`/appointments/${selected.value.code}/approve`, {
      staff_code: approvalSelectedStaff.value,
      selected_charge_codes: selectedChargeCodes.value
    })
    showApproval.value = false
    await fetchAppointments()
  } catch (err) {
    approvalError.value = err.response?.data?.message || 'Approval failed'
    if (selected.value) await loadApprovalAvailability(selected.value)
  } finally {
    approvalSaving.value = false
  }
}

async function forceAssignStaff(staffCode) {
  approvalSaving.value = true
  approvalError.value = ''
  const payload = {
    staff_code: staffCode,
    force_reassign: true,
    selected_charge_codes: selectedChargeCodes.value
  }
  try {
    await api.post(`/appointments/${selected.value.code}/approve`, payload)
    showApproval.value = false
    await fetchAppointments()
  } catch (err) {
    approvalError.value = err.response?.data?.message || 'Force assignment failed'
  } finally {
    approvalSaving.value = false
  }
}

async function submitApprovalReschedule() {
  if (!approvalRescheduleForm.appointment_start_date || !approvalRescheduleForm.start_time || !approvalRescheduleForm.end_time) {
    approvalError.value = 'Please fill in the new date and times'
    return
  }
  approvalSaving.value = true
  approvalError.value = ''
  try {
    await api.post(`/appointments/${selected.value.code}/reschedule`, approvalRescheduleForm)
    showApproval.value = false
    await fetchAppointments()
  } catch (err) {
    approvalError.value = err.response?.data?.message || 'Reschedule request failed'
  } finally {
    approvalSaving.value = false
  }
}

function prefillApprovalReschedule({ locationCode, startTime, endTime }) {
  approvalRescheduleForm.appointment_start_date = toDateInput(selected.value?.appointment_start_date)
  approvalRescheduleForm.appointment_end_date = toDateInput(selected.value?.appointment_end_date || selected.value?.appointment_start_date)
  approvalRescheduleForm.start_time = toTimeInput(startTime)
  approvalRescheduleForm.end_time = toTimeInput(endTime)
  approvalRescheduleForm.location_code = locationCode || selected.value?.location_code || ''
  showRescheduleInApproval.value = true
  approvalError.value = ''
}

onMounted(fetchAppointments)
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
.ams-badge{
  display:inline-block;
  padding:4px 10px;
  border-radius:999px;
  font-size:11px;
  font-weight:600;
  text-transform:capitalize;
}
.ams-badge.PENDING{ background:#fef3c7; color:#92400e; }
.ams-badge.APPROVED{ background:#dcfce7; color:#166534; }
.ams-badge.REJECTED{ background:#fee2e2; color:#991b1b; }
.ams-badge.COMPLETED{ background:#dbeafe; color:#1e40af; }
.ams-badge.RESCHEDULED{ background:#ede9fe; color:#6d28d9; }
.ams-badge.CANCELLED{ background:#f1f5f9; color:#475569; }
.ams-badge.IN_PROGRESS{ background:#cffafe; color:#155e75; }

.modal-content{
  border:none;
  border-radius:16px;
  overflow:hidden;
  box-shadow:0 15px 45px rgba(0,0,0,.18);
}
.modal-header{ background:#f8fafc; }
.modal-title{ font-weight:700; }
.modal-footer{ background:#fafafa; }

.list-group-item{
  border-radius:10px !important;
  border:1px solid #e2e8f0;
}
.cursor-pointer {
  cursor: pointer;
}
.btn-success{ background:#22c55e; border-color:#22c55e; }
.btn-success:hover{ background:#16a34a; border-color:#16a34a; }
.btn-danger{ background:#ef4444; border-color:#ef4444; }
.btn-danger:hover{ background:#dc2626; border-color:#dc2626; }
.btn-outline-primary{ color:#6366f1; border-color:#6366f1; }
.btn-outline-primary:hover{ background:#6366f1; color:white; }
dl dt{ font-size:13px; }
dl dd{ font-size:14px; }

@media(max-width:768px){
  .ams-table{ min-width:1000px; }
  .d-flex.gap-2.flex-wrap{ flex-direction:column; }
  .form-control, .form-select{ max-width:100% !important; }
}
</style>