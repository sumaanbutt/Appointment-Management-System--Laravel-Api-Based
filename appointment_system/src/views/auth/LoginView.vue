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

<!--            <a href="#" class="forgot-link">-->
<!--              Forgot Password?-->
<!--            </a>-->
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
  </template>

<style scoped>


.login-page {

  min-height: 100vh;

  background: #f4f6f9;

  display: flex;
  justify-content: center;
  align-items: center;

  padding: 20px;
}

.login-card {

  width: 100%;
  max-width: 500px;

  background: white;

  padding: 50px;

  border-radius: 18px;

  box-shadow: 0 10px 35px rgba(0,0,0,0.08);
}

.login-header {
  text-align: center;
  margin-bottom: 35px;
}

.login-header h2 {

  font-size: 30px;
  font-weight: 700;

  color: #111827;
}

.login-header p {

  margin-top: 10px;

  color: #6b7280;
}

.input-wrapper {
  position: relative;
}

.input-icon {

  position: absolute;

  left: 16px;
  top: 50%;

  transform: translateY(-50%);

  color: #6b7280;

  z-index: 10;
}

.custom-input {

  height: 55px;

  padding-left: 45px;
  padding-right: 45px;

  border-radius: 12px;
}

.custom-input:focus {
  box-shadow: none;
}

.password-toggle {

  position: absolute;

  right: 16px;
  top: 50%;

  transform: translateY(-50%);

  cursor: pointer;

  color: #6b7280;

  z-index: 10;
}

.login-btn {

  height: 55px;

  border-radius: 12px;

  font-weight: 600;

  font-size: 16px;
}

.forgot-link {

  text-decoration: none;

  font-size: 14px;
}

.forgot-link:hover {
  text-decoration: underline;
}

/* DARK MODE */

:global(body.dark-mode .login-page){

  background:#111827;
}

:global(body.dark-mode .login-card){

  background:#1f2937;

  border-color:#374151;
}

:global(body.dark-mode.login-header h2){

}

:global(body.dark-mode input){

  background:#111827;

  color:white;

  border-color:#374151;
}

:global(body.dark-mode h1),

:global(body.dark-mode .login-header h2),

:global(body.dark-mode .login-header p),

:global(body.dark-mode label){

  color:white;
}

</style>>