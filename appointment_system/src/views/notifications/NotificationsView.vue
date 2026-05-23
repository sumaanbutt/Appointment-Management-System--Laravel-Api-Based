<script setup lang="ts">

import { ref, computed, watch } from 'vue'

const startDate = ref('')
const endDate = ref('')
const dateError = ref('')

const notifications = ref([

{
id:1,
title:'New Appointment',
message:'Appointment created successfully.',
read:false,
time:'2 min ago'
},

{
id:2,
title:'Organization Updated',
message:'Organization details modified.',
read:true,
time:'1 hour ago'
}

])

watch([startDate,endDate],()=>{

dateError.value=''

if(startDate.value && endDate.value){

const start=new Date(startDate.value)
const end=new Date(endDate.value)

const diff =
(end-start)/(1000*60*60*24)

if(diff > 31){

dateError.value =
'Maximum period allowed is 1 month.'

}

}

})

const newNotifications = computed(()=>
notifications.value.filter(n=>!n.read)
)

const olderNotifications = computed(()=>
notifications.value.filter(n=>n.read)
)

function markAllRead(){

notifications.value.forEach(
n=>n.read=true
)

//API later

}

function deleteAll(){

if(confirm('Delete all notifications?')){

notifications.value=[]

// API later

}

}

function deleteOne(id){

notifications.value =
notifications.value.filter(
n=>n.id!==id
)

// API later

}

function approve(id){

const item =
notifications.value.find(
n=>n.id===id
)

if(item){

item.read=true

}

// API later

}

function reject(id){

  const item =
      notifications.value.find(
          n=>n.id===id
      )

  if(item){

    item.read = true

  }

  // API later
  // reject notification endpoint

}

function viewNotification(id){

  console.log('View notification',id)

  // router push / modal / details page
  // according to your project flow

}

</script>

<template>

  <div class="notifications-page">

    <div class="page-header">

      <div>

        <h2>Notifications</h2>

        <p>
          View all appointment, organization and system updates.
        </p>

      </div>

      <div class="header-actions">

        <button
            class="action-btn secondary"
            @click="markAllRead"
        >
          Mark All Read
        </button>

        <button
            class="action-btn danger"
            @click="deleteAll"
        >
          Delete All
        </button>

      </div>

    </div>

    <div class="filters-card">

      <div class="date-group">

        <div>

          <label>Start Date</label>

          <input
              type="date"
              v-model="startDate"
          >

        </div>

        <div>

          <label>End Date</label>

          <input
              type="date"
              v-model="endDate"
          >

        </div>

      </div>

      <small
          v-if="dateError"
          class="error-text"
      >
        {{ dateError }}
      </small>

    </div>

    <div class="notification-container">

      <h3 class="section-title">
        New Notifications
      </h3>

      <div
          v-for="item in newNotifications"
          :key="item.id"
          class="notification-card unread"
      >

        <div class="card-content">

          <div>

            <h4>{{ item.title }}</h4>

            <p>{{ item.message }}</p>

            <small>{{ item.time }}</small>

          </div>

          <span class="badge unread-badge">
            Unread
          </span>

        </div>

        <div class="card-actions">

          <button
              class="approve-btn"
              @click="approve(item.id)"
          >
            Approve
          </button>

          <button
              class="reject-btn"
              @click="reject(item.id)"
          >
            Reject
          </button>

          <button
              class="view-btn"
              @click="viewNotification(item.id)"
          >
            View
          </button>

        </div>

      </div>

      <h3 class="section-title older-title">
        Older Notifications
      </h3>

      <div
          v-for="item in olderNotifications"
          :key="item.id"
          class="notification-card"
      >

        <div class="card-content">

          <div>

            <h4>{{ item.title }}</h4>

            <p>{{ item.message }}</p>

            <small>{{ item.time }}</small>

          </div>

          <span class="badge read-badge">
            Read
          </span>

        </div>

        <div class="card-actions">

          <button
              class="delete-btn"
              @click="deleteOne(item.id)"
          >
            Delete
          </button>

        </div>

      </div>

    </div>

  </div>

</template>


<style scoped>

.notifications-page{
  padding:30px;
  background:#f4f6f9;
  min-height:100vh;
}

.page-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:25px;
}

.page-header h2{
  margin:0;
}

.header-actions{
  display:flex;
  gap:12px;
}

.filters-card{
  background:white;
  padding:22px;
  border-radius:18px;
  margin-bottom:24px;
  box-shadow:0 10px 35px rgba(0,0,0,.05);
}

.date-group{
  display:flex;
  gap:18px;
}

label{
  display:block;
  margin-bottom:8px;
  font-weight:600;
}

input{
  padding:10px 14px;
  border:1px solid #dfe3e8;
  border-radius:10px;
}

.notification-container{
  background:white;
  border-radius:18px;
  padding:25px;
  box-shadow:0 10px 35px rgba(0,0,0,.05);
}

.section-title{
  margin-bottom:18px;
}

.older-title{
  margin-top:40px;
}

.notification-card{
  border:1px solid #e8edf2;
  border-radius:16px;
  padding:20px;
  margin-bottom:18px;
}

.unread{
  border-left:5px solid #dc3545;
  background:#fff7f7;
}

.card-content{
  display:flex;
  justify-content:space-between;
}

.card-actions{
  display:flex;
  gap:10px;
  margin-top:18px;
}

.badge{
  padding:8px 14px;
  border-radius:30px;
  font-size:13px;
}

.unread-badge{
  background:#ffe5e5;
  color:#dc3545;
}

.read-badge{
  background:#e8f6ee;
  color:#198754;
}

.action-btn,
.approve-btn,
.delete-btn,
.view-btn{
  border:none;
  cursor:pointer;
  padding:10px 18px;
  border-radius:10px;
  font-weight:600;
}

.secondary{
  background:#0d6efd;
  color:white;
}

.danger{
  background:#dc3545;
  color:white;
}

.approve-btn{
  background:#198754;
  color:white;
}

.delete-btn{
  background:#dc3545;
  color:white;
}

.view-btn{
  background:#eef2f7;
}

.error-text{
  color:#dc3545;
  display:block;
  margin-top:12px;
}

.reject-btn{
  background:#ffc107;
  color:#212529;
  border:none;
  cursor:pointer;
  padding:10px 18px;
  border-radius:10px;
  font-weight:600;
}

/* DARK MODE — NOTIFICATION PAGE */

:global(body.dark-mode .notifications-page){

  background:#111827;
}

:global(body.dark-mode .filters-card),

:global(body.dark-mode .notification-container){

  background:#1f2937;

  border:1px solid #374151;
}

:global(body.dark-mode .notification-card){

  background:#243041;

  border-color:#374151;
}

:global(body.dark-mode .unread){

  background:#31242a;
}

:global(body.dark-mode h1),

:global(body.dark-mode h2),

:global(body.dark-mode h3),

:global(body.dark-mode h4),

:global(body.dark-mode p),

:global(body.dark-mode small),

:global(body.dark-mode span),

:global(body.dark-mode label){

  color:white;
}

:global(body.dark-mode input){

  background:#111827;

  border-color:#374151;

  color:white;
}

:global(body.dark-mode .view-btn){

  background:#374151;

  color:white;
}

</style>