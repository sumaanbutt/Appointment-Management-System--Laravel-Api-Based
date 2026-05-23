<script setup lang="ts">

import { ref } from 'vue'
import {useAuthStore} from '@/stores/auth.store'

const authStore = useAuthStore()

const email = ref('')
const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

async function resetPassword(){
  loading.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try{
    const response =
        await authStore.forgotPassword(email.value)

    successMessage.value = response.message || 'Password reset link sent.'
  }

  catch(error:any){

    if(error.response ?.data ?.errors ?.email){

      errorMessage.value = error.response.data.errors.email[0]
    }

    else{
      errorMessage.value = error.response ?.data ?.message || 'Failed to send reset link.'
    }
  }

  finally{
    loading.value = false
  }

}
</script>

<template>

  <div class="forgot-page">

    <!-- LEFT -->

    <div class="forgot-left">

      <div class="forgot-card">

        <div class="forgot-header">

          <h2>Forgot Password</h2>

          <p>
            No worries, we'll send you reset instructions
          </p>

        </div>

        <form @submit.prevent="resetPassword">

          <!-- EMAIL -->

          <div class="input-wrapper mb-4">

            <i class="bi bi-envelope input-icon"></i>

            <input
                type="email"
                class="form-control custom-input"
                placeholder="Enter your email"
                v-model="email"
            >

            <p v-if="successMessage" class="text-success small mt-2">
              {{ successMessage }}
            </p>

            <p v-if="errorMessage" class="text-danger small mt-2">
              {{ errorMessage }}
            </p>

          </div>

          <!-- BUTTON -->

          <button type="submit" class="btn btn-primary forgotpass-btn w-100" :disabled="loading">
            {{ loading ? 'Sending...' : 'Reset Password' }}
          </button>

        </form>

        <!-- RETURN -->

        <div class="return-login">

          <RouterLink
              to="/login"
              class="login-link"
          >
            <i class="bi bi-arrow-left"></i>

            Return to Login
          </RouterLink>

        </div>

      </div>

    </div>

    <!-- RIGHT -->

    <div class="forgot-right">

      <div class="overlay">

        <div class="right-content">

          <h1>AMS Portal</h1>

          <p>
            Manage appointments, organizations,
            services and users efficiently.
          </p>

        </div>

      </div>

    </div>

  </div>

</template>

<style scoped>

.forgot-page {

  min-height: 100vh;

  display: flex;

  background: #f4f6f9;
}

/* LEFT SIDE */

.forgot-left {

  width: 50%;

  display: flex;
  justify-content: center;
  align-items: center;

  padding: 40px;
}

.forgot-card {

  width: 100%;
  max-width: 500px;

  background: white;

  padding: 50px;

  border-radius: 20px;

  box-shadow: 0 10px 35px rgba(0,0,0,0.08);
}

.forgot-header {

  margin-bottom: 35px;
}

.forgot-header h2 {

  font-size: 32px;
  font-weight: 700;

  color: #111827;
}

.forgot-header p {

  margin-top: 10px;

  color: #6b7280;
}

/* INPUT */

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

  border-radius: 12px;
}

.custom-input:focus {
  box-shadow: none;
}

/* BUTTON */

.forgot-btn {

  height: 55px;

  border-radius: 12px;

  font-size: 16px;
  font-weight: 600;
}

/* RETURN */

.return-login {

  margin-top: 25px;

  text-align: center;
}

.login-link {

  text-decoration: none;

  color: #0d6efd;

  font-weight: 500;
}

.login-link:hover {
  text-decoration: underline;
}

/* RIGHT SIDE */

.forgot-right {

  width: 50%;

  background-image:
      url('https://images.unsplash.com/photo-1521791136064-7986c2920216?q=80&w=1600');

  background-size: cover;
  background-position: center;

  position: relative;
}

.overlay {

  width: 100%;
  height: 100%;

  background: rgba(13, 110, 253, 0.75);

  display: flex;
  justify-content: center;
  align-items: center;

  padding: 40px;
}

.right-content {

  color: white;

  text-align: center;
}

.right-content h1 {

  font-size: 48px;
  font-weight: 700;
}

.right-content p {

  margin-top: 20px;

  font-size: 18px;

  line-height: 1.7;
}

/* RESPONSIVE */

@media (max-width: 992px) {

  .forgot-right {
    display: none;
  }

  .forgot-left {
    width: 100%;
  }

}

/* DARK MODE */

:global(body.dark-mode .forgot-page){

  background:#111827;
}

:global(body.dark-mode .forgot-card){

  background:#1f2937;

  border-color:#374151;
}

:global(body.dark-mode input){

  background:#111827;

  border-color:#374151;

  color:white;
}

:global(body.dark-mode .forgot-header h1),

:global(body.dark-mode .forgot-header h2),

:global(body.dark-mode .forgot-header p),

:global(body.dark-mode .forgot-header label){

  color:white;
}

</style>