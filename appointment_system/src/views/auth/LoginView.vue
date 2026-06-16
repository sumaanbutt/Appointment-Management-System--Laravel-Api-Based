<script setup lang="ts">

import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('superadmin@gmail.com')
const password = ref('super@dmin')
const rememberMe = ref(false)
const showPassword = ref(false)

const errorMessage = ref('')

function togglePassword() {

  showPassword.value =
      !showPassword.value

}

async function login() {
  errorMessage.value = ''
  try{
    await authStore.login(email.value, password.value)

    router.push(authStore.homeRoute)
  }
  catch(error:any){
    errorMessage.value = error.response?.data?.message || 'Invalid email or password'
  }
}

</script>

  <template>
    <div class="login-page">

      <!-- LEFT -->

      <div class="login-left">

      <div class="login-card">

        <div class="login-header">

          <h2>Appointment Management System</h2>

          <p>
            Please login to your account
          </p>

        </div>

        <form @submit.prevent="login">

          <!-- EMAIL -->

          <div class="input-wrapper mb-4">

            <i class="bi bi-envelope input-icon"></i>

            <input
                type="email"
                class="form-control custom-input"
                placeholder="Enter email"
                v-model="email"
            >

          </div>

          <!-- PASSWORD -->

          <div class="input-wrapper mb-4">

            <i class="bi bi-lock input-icon"></i>

            <input
                :type="showPassword ? 'text' : 'password'"
                class="form-control custom-input"
                placeholder="Enter password"
                v-model="password"
            >

            <i
                :class="showPassword ? 'bi bi-eye ' : 'bi bi-eye-slash'"
                class="password-toggle"
                @click="togglePassword"
            ></i>

          </div>

          <!-- REMEMBER -->

          <div
              class="d-flex justify-content-between align-items-center mb-4"
          >

            <div class="form-check">

              <input
                  class="form-check-input"
                  type="checkbox"
                  id="remember"
                  v-model="rememberMe"
              >

              <label
                  class="form-check-label"
                  for="remember"
              >
                Remember Me
              </label>

            </div>
            <div>
              <RouterLink
                  to="/reset-password"
                  class="forgot-link"
              >

                Forgot Password?

              </RouterLink>
            </div>

          </div>

          <!-- ERROR MESSAGE -->

          <div
              v-if="errorMessage"
              class="alert alert-danger mb-3"
          >
            {{ errorMessage }}
          </div>

          <!-- BUTTON -->

          <button
              type="submit"
              class="btn btn-primary login-btn w-100"
          >
            Login
          </button>

        </form>

      </div>

      </div>
    </div>

  </template>

<style scoped>

/* FULL PAGE */

.login-page {

  min-height: 100vh;

  display: flex;
  justify-content: center;
  align-items: center;

  background-image:
      url('https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=1600');

  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;

  position: relative;

  overflow: hidden;
}

/* BLUE OVERLAY */

.login-page::before {

  content: '';

  position: absolute;

  inset: 0;

  background:
      rgba(13,110,253,.75);

  z-index: 1;
}

.login-card {

  width: 100%;
  max-width: 500px;

  background: rgba(255,255,255,.95);

  backdrop-filter: blur(12px);

  padding: 50px;

  border-radius: 20px;

  box-shadow:
      0 20px 60px rgba(0,0,0,.25);

  position: relative;

  z-index: 3;
}

/* CENTER LOGIN */

.login-left {

  width: 100%;

  display: flex;
  justify-content: center;
  align-items: center;

  position: relative;

  z-index: 2;

  padding: 20px;
}

/* HIDE RIGHT PANEL */

.login-right {

  display: none;
}

/* LOGIN CARD */

.login-card {

  width: 100%;
  max-width: 500px;

  background: rgba(255,255,255,.96);

  backdrop-filter: blur(12px);

  padding: 50px;

  border-radius: 20px;

  box-shadow:
      0 20px 60px rgba(0,0,0,.25);

  position: relative;

  z-index: 3;
}

/* HEADER */

.login-header {

  text-align: center;

  margin-bottom: 35px;
}

.login-header h2 {

  font-size: 30px;

  font-weight: 700;

  color: #111827;

  margin-bottom: 10px;
}

.login-header p {

  color: #6b7280;

  margin: 0;
}

/* INPUT WRAPPER */

.input-wrapper {

  position: relative;
}

/* INPUT ICON */

.input-icon {

  position: absolute;

  left: 16px;
  top: 50%;

  transform: translateY(-50%);

  color: #6b7280;

  z-index: 10;
}

/* INPUTS */

.custom-input {

  height: 55px;

  padding-left: 45px;
  padding-right: 45px;

  border-radius: 12px;

  border: 1px solid #d1d5db;
}

.custom-input:focus {

  box-shadow: none;

  border-color: #0d6efd;
}

/* PASSWORD TOGGLE */

.password-toggle {

  position: absolute;

  right: 16px;
  top: 50%;

  transform: translateY(-50%);

  cursor: pointer;

  color: #6b7280;

  z-index: 10;
}

/* BUTTON */

.login-btn {

  height: 55px;

  border-radius: 12px;

  font-size: 16px;

  font-weight: 600;
}

/* FORGOT PASSWORD */

.forgot-link {

  text-decoration: none;

  font-size: 14px;

  color: #0d6efd;
}

.forgot-link:hover {

  text-decoration: underline;
}

/* DARK MODE */

:global(body.dark-mode .login-card){

  background: rgba(31,41,55,.95);

  border: 1px solid #374151;
}

:global(body.dark-mode .custom-input){

  background:#111827;

  color:white;

  border-color:#374151;
}

:global(body.dark-mode .custom-input::placeholder){

  color:#9ca3af;
}

:global(body.dark-mode .login-header h2),

:global(body.dark-mode .login-header p),

:global(body.dark-mode label),

:global(body.dark-mode .form-check-label){

  color:white;
}

:global(body.dark-mode .input-icon),

:global(body.dark-mode .password-toggle){

  color:#9ca3af;
}

/* MOBILE */

@media (max-width: 768px) {

  .login-card {

    padding: 30px;
  }

  .login-header h2 {

    font-size: 24px;
  }

}

</style>