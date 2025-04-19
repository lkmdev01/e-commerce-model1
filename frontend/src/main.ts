import './assets/main.css'
import '@fortawesome/fontawesome-free/css/all.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/auth'
import { useProductStore } from './stores/productStore'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

app.mount('#app')

// Inicializar stores principais
const authStore = useAuthStore()
const productStore = useProductStore()

// Carregar dados iniciais
authStore.checkAuth()
productStore.fetchProducts()
