<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth.store.ts'
import {ref, onMounted, onBeforeUnmount} from 'vue'
import NotificationDropdown from '@/components/notifications/NotificationDropdown.vue'
import ProfileDropdown from '@/components/layout/ProfileDropdown.vue'

const darkMode = ref(false)
const unreadNotifications = ref(true)
const showNotifications = ref(false)
const showProfileDropdown = ref(false)
const dropdownRef = ref()
const router = useRouter()
const authStore = useAuthStore()

const user = ref({
  get name(){
    return (authStore.user?.name || authStore.user?.email || 'User')
  },
  get role(){
    return (authStore.user?.user_type || 'Guest')
  },
  profileImage:''
})

function toggleNotifications(){
  showNotifications.value =
      !showNotifications.value
  showProfileDropdown.value = false
}

function toggleProfile(){
  showProfileDropdown.value =
      !showProfileDropdown.value
  showNotifications.value = false
}

function goToSettings(){
  router.push('/settings')
}

function handleOutsideClick(
    event:MouseEvent
){

  if(
      dropdownRef.value &&
      !dropdownRef.value.contains(
          event.target
      )
  ){

    const sidebar = document.querySelector('.sidebar')
    if(sidebar && sidebar.contains(event.target as Node)
    ){
      return
    }
    showNotifications.value = false
    showProfileDropdown.value = false
  }
}

function toggleDarkMode(){
  darkMode.value = !darkMode.value
  document.body.classList.toggle('dark-mode')

  localStorage.setItem(
      'darkMode',
      darkMode.value.toString()
  )
}

onMounted(()=>{
  document.addEventListener(
      'click',
      handleOutsideClick
  )

  const savedMode =
      localStorage.getItem(
          'darkMode'
      )

  if(savedMode === 'true'){
    darkMode.value = true
    document.body.classList.add(
        'dark-mode'
    )
  }
})

onBeforeUnmount(()=>{
  document.removeEventListener(
      'click',
      handleOutsideClick
  )
})

</script>

<template>

  <nav class="custom-navbar">

    <div class="navbar-left">

      <div class="search-wrapper">

        <i
            class="bi bi-search search-icon"
        ></i>

        <input
            type="text"
            class="search-input"
            placeholder="Search..."
        >

      </div>

    </div>

    <div
        ref="dropdownRef"
        class="navbar-right"
    >

      <button class="nav-icon-btn">

        <i class="bi bi-calendar-plus"></i>

      </button>

      <button
          class="nav-icon-btn"
          @click="goToSettings"
      >

        <i class="bi bi-gear"></i>

      </button>

      <button
          class="nav-icon-btn"
          @click="toggleDarkMode"
      >

        <i
            :class="
             darkMode
             ? 'bi bi-sun'
             : 'bi bi-moon'
          "
        ></i>

      </button>

      <div class="notification-wrapper">

        <button
            class="nav-icon-btn notification-btn"
            @click.stop="
            toggleNotifications()
            "
        >

          <i class="bi bi-bell"></i>

          <span
              v-if="
              unreadNotifications
              "
              class="notification-dot"
          ></span>

        </button>

        <NotificationDropdown
            v-if="
            showNotifications
            "
            class="notification-popup"
        />

      </div>

      <div class="profile-wrapper">

        <button
            class="profile-btn"
            @click.stop="toggleProfile"
        >

          <img
              v-if="
              user.profileImage
              "
              :src="
              user.profileImage
              "
              class="profile-avatar"
              alt="profile"
          >

          <i
              v-else
              class="
              bi bi-person-circle
              "
          ></i>

        </button>

        <ProfileDropdown
            v-if="
            showProfileDropdown
            "
            :user="user"
        />

      </div>

    </div>

  </nav>

</template>

<style scoped>

.custom-navbar{

  height:75px;

  background:white;

  display:flex;

  justify-content:space-between;

  align-items:center;

  padding:0 30px;

  border-bottom:
      1px solid #e5e7eb;
}

.navbar-left{

  display:flex;

  align-items:center;
}

.search-wrapper{

  position:relative;

  width:320px;
}

.search-input{

  width:100%;

  height:45px;

  border:
      1px solid #d1d5db;

  border-radius:12px;

  padding-left:45px;

  padding-right:20px;

  outline:none;

  transition:.3s;
}

.search-input:focus{

  border-color:#0d6efd;

  box-shadow:
      0 0 0 3px
      rgba(13,110,253,.10);
}

.search-icon{

  position:absolute;

  left:16px;

  top:50%;

  transform:
      translateY(-50%);

  color:#6b7280;
}

.navbar-right{

  display:flex;

  align-items:center;

  gap:14px;
}

.nav-icon-btn{

  width:42px;

  height:42px;

  border:none;

  border-radius:12px;

  background:#f3f4f6;

  display:flex;

  justify-content:center;

  align-items:center;

  cursor:pointer;

  transition:.3s;

  position:relative;
}

.nav-icon-btn:hover{

  background:#e5e7eb;
}

.nav-icon-btn i{

  font-size:18px;

  color:#374151;
}

.notification-wrapper{

  position:relative;
}

.notification-popup{

  position:absolute;

  top:60px;

  right:0;

  z-index:99999;
}

.notification-btn i{

  animation:
      bellShake 2s infinite;
}

.notification-dot{

  width:10px;

  height:10px;

  background:red;

  border-radius:50%;

  position:absolute;

  top:10px;

  right:10px;
}

.profile-wrapper{

  position:relative;
}

.profile-btn{

  width:45px;

  height:45px;

  border:none;

  border-radius:50%;

  background:#0d6efd;

  color:white;

  display:flex;

  justify-content:center;

  align-items:center;

  cursor:pointer;
}

.profile-btn i{

  font-size:24px;
}

.profile-avatar{

  width:100%;

  height:100%;

  object-fit:cover;

  border-radius:50%;
}

@keyframes bellShake{

  0%{transform:rotate(0)}

  10%{transform:rotate(12deg)}

  20%{transform:rotate(-12deg)}

  30%{transform:rotate(10deg)}

  40%{transform:rotate(-10deg)}

  50%{transform:rotate(0)}

  100%{transform:rotate(0)}
}
</style>