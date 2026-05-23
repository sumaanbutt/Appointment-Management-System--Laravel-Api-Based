<template>
  <div class="page">

    <!-- HEADER -->
    <div class="header">

      <div>
        <h2>Request Appointment</h2>

        <p class="sub">
          Book services from available locations
        </p>
      </div>

      <div>
        <h2> Request Appointment</h2>
        <p class="sub">
          Book services from available locations
        </p>
      </div>

    </div>

    <!-- REQUEST FORM -->
    <div class="card">

      <form
          class="form"
          @submit.prevent="submitAppointment"
      >

        <!-- BUSINESS -->
        <div class="field">
          <label>Business</label>

          <select v-model="form.business_code">
            <option value="">Select Business</option>

            <option
                v-for="business in businesses"
                :key="business.code"
                :value="business.code"
            >
              {{ business.name }}
            </option>

          </select>
        </div>

        <!-- LOCATION -->
        <div class="field">
          <label>Location</label>

          <select v-model="form.location_code">
            <option value="">Select Location</option>

            <option
                v-for="location in filteredLocations"
                :key="location.code"
                :value="location.code"
            >
              {{ location.name }}
            </option>

          </select>
        </div>

        <!-- SERVICE -->
        <div class="field">
          <label>Service</label>

          <select v-model="form.service_code">
            <option value="">Select Service</option>

            <option
                v-for="service in filteredServices"
                :key="service.code"
                :value="service.code"
            >
              {{ service.name }}
              - ${{ service.price }}
            </option>

          </select>
        </div>

        <!-- DATE -->
        <div class="field">
          <label>Date</label>

          <input
              type="date"
              v-model="form.appointment_date"
          />
        </div>

        <!-- TIME -->
        <div class="field">
          <label>Time</label>

          <input
              type="time"
              v-model="form.appointment_time"
          />
        </div>

        <!-- NOTES -->
        <div class="field full">
          <label>Notes</label>

          <textarea
              v-model="form.notes"
              placeholder="Optional notes"
          ></textarea>
        </div>

        <!-- SUBMIT -->
        <div class="full">
          <button
              type="submit"
              class="primary-btn"
          >
            Request Appointment
          </button>
        </div>

      </form>

    </div>

    <!-- HISTORY -->
    <div class="card">

      <div class="card-header">
        <h3>My Appointments</h3>
      </div>

      <table class="table">

        <thead>
        <tr>
          <th>Business</th>
          <th>Service</th>
          <th>Date</th>
          <th>Time</th>
          <th>Status</th>
        </tr>
        </thead>

        <tbody>

        <tr v-for="appointment in appointments" :key="appointment.code">

          <td>{{ appointment.business_code }}</td>
          <td>{{ appointment.service_code }}</td>
          <td>{{ appointment.appointment_date }}</td>
          <td>{{ appointment.appointment_time }}</td>

          <td>
              <span :class="['badge', appointment.status]">
                {{ appointment.status }}
              </span>
          </td>

        </tr>

        </tbody>

      </table>

    </div>

  </div>
</template>

<script setup>
import { computed, reactive, ref, onMounted} from 'vue'
import api from '@/services/api'

const businesses = ref([])
const locations = ref([])
const services = ref([])
const appointments = ref([])

/* FORM */
const form = reactive({
    business_code:'',
    location_code:'',
    service_code:'',
    appointment_date:'',
    appointment_time:'',
    notes:''
  })

onMounted(async()=>{
  try{
    const [bizRes, locRes, srvRes, apptRes] = await Promise.all([
        api.get('/businesses'),
        api.get('/business-locations'),
        api.get('/services'),
        api.get('/appointments')
    ])

    businesses.value = bizRes.data.data.data || []
    locations.value = locRes.data.data.data || []
    services.value = srvRes.data.data.data || []
    appointments.value = apptRes.data.data.data || []
  }

  catch(err){
    console.error(err)
  }
})

/* FILTERED LOCATIONS */
const filteredLocations = computed(() => {
  return locations.value.filter(
      l => l.business_code === form.business_code
  )
})

/* FILTERED SERVICES */
const filteredServices = computed(() => {
  return services.value.filter(
      s => s.business_code === form.business_code
  )
})

/* SUBMIT */
async function submitAppointment(){
  try{
    await api.post('/appointments', form)
    const res = await api.get('/appointments')

    appointments.value = res.data.data.data || []
    form.business_code=''
    form.location_code=''
    form.service_code=''
    form.appointment_date=''
    form.appointment_time=''
    form.notes=''
  }
  catch(err){
    console.error(err)
  }
}
</script>

<style scoped>
.page {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

/* HEADER */
.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.sub {
  color: #64748b;
}

/* CARD */
.card {
  background: white;
  border-radius: 10px;
  padding: 20px;
}

/* FORM */
.form {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
}

.field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.full {
  grid-column: span 2;
}

label {
  font-size: 14px;
  font-weight: 600;
}

input,
select,
textarea {
  padding: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
}

textarea {
  min-height: 100px;
}

/* BUTTON */
.primary-btn {
  background: #6366f1;
  color: white;
  border: none;
  padding: 12px 18px;
  border-radius: 6px;
  cursor: pointer;
}

/* TABLE */
.table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 12px;
  border-bottom: 1px solid #eee;
  text-align: left;
}

/* BADGES */
.badge {
  padding: 5px 8px;
  border-radius: 6px;
  font-size: 12px;
}

.pending {
  background: #fef3c7;
  color: #92400e;
}

.approved {
  background: #dcfce7;
  color: #166534;
}

.rejected {
  background: #fee2e2;
  color: #991b1b;
}

.rescheduled {
  background: #dbeafe;
  color: #1d4ed8;
}

/* RESPONSIVE */
@media (max-width: 768px) {

  .form {
    grid-template-columns: 1fr;
  }

  .full {
    grid-column: span 1;
  }

}
</style>