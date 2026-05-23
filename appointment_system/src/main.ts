import { createApp } from 'vue'
import App from './App.vue'
import {createPinia} from'pinia'

import router from './router'
import './assets/styles/theme.css'

import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'
import 'bootstrap-icons/font/bootstrap-icons.css'

const pinia = createPinia()

createApp(App)
    .use(pinia)
    .use(router)
    .mount('#app')