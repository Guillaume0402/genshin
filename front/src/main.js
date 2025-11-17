/**
 * Main Application Entry Point
 *
 * Initialise Vue 3, Pinia, et Vue Router
 */

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import { useAuthStore } from './stores/auth'
import './style.css'

// Création de l'application Vue
const app = createApp(App)

// Création du store Pinia
const pinia = createPinia()

// Installation des plugins
app.use(pinia)
app.use(router)

// Initialisation de l'authentification depuis localStorage
const authStore = useAuthStore()
authStore.initializeAuth()

// Montage de l'application
app.mount('#app')
