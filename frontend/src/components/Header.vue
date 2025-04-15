<template>
  <header class="w-full absolute bg-transparent top-0 z-50">
    <nav class="container px-4 py-4 gap-4 flex justify-between items-center">
      <router-link to="/" class="text-2xl font-bold text-gray-800">
        LOJA VIRTUAL
      </router-link>
      
      <!-- Menu Mobile -->
      <div 
        class="fixed inset-0 bg-white z-50 transform transition-transform duration-300"
        :class="isMenuOpen ? 'translate-x-0' : 'translate-x-full'"
      >
        <div class="p-4">
          <div class="flex justify-between items-center mb-8">
            <router-link to="/" class="text-2xl font-bold text-gray-800">
              YASMIM SOL
            </router-link>
            <button 
              @click="toggleMenu" 
              class="text-gray-800 hover:text-purple-600"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
          
          <div class="flex flex-col space-y-6">
            <router-link 
              v-for="item in menuItems" 
              :key="item.path"
              :to="item.path"
              class="text-gray-800 hover:text-purple-600 font-medium transition-colors text-lg"
              @click="closeMenu"
            >
              {{ item.name }}
            </router-link>
          </div>
        </div>
      </div>
      
      <!-- Menu Desktop -->
      <div class="hidden md:flex items-center space-x-8">
        <router-link 
          v-for="item in menuItems" 
          :key="item.path"
          :to="item.path"
          class="text-gray-800 hover:text-purple-600 font-medium transition-colors"
        >
          {{ item.name }}
        </router-link>
      </div>

      <div class="flex items-center space-x-4">
        <button class="text-gray-800 hover:text-purple-600">
          <i class="fas fa-search text-xl"></i>
        </button>
        <router-link to="/carrinho" class="text-gray-800 hover:text-purple-600 relative">
          <i class="fas fa-shopping-cart text-xl"></i>
          <span 
            v-if="cartStore.totalItems > 0"
            class="absolute -top-2 -right-2 bg-purple-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"
          >
            {{ cartStore.totalItems }}
          </span>
        </router-link>
      </div>

      <button 
        @click="toggleMenu" 
        class="md:hidden text-gray-800 hover:text-purple-600"
      >
        <i class="fas fa-bars text-xl"></i>
      </button>
    </nav>
  </header>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { useCartStore } from '@/stores/cartStore'

const cartStore = useCartStore()
const isMenuOpen = ref(false)

const menuItems = [
  { name: 'LAR', path: '/' },
  { name: 'SOBRE', path: '/sobre' },
  { name: 'COMPRAR', path: '/comprar' },
  { name: 'CONTATO', path: '/contato' }
]

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #333;
  text-decoration: none;
}

.nav a {
  color: #333;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.3s ease;
}

.nav a:hover {
  color: #8B008B;
}

.icons {
  display: flex;
  gap: 1rem;
}

.icons a {
  color: #333;
  font-size: 1.2rem;
  text-decoration: none;
  transition: color 0.3s ease;
}

.icons a:hover {
  color: #8B008B;
}
</style> 