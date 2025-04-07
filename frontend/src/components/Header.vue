<template>
  <header class="w-full absolute bg-transparent top-0 z-50">
    <nav class="container px-4 py-4 gap-4 flex justify-between items-center">
      <router-link to="/" class="text-2xl font-bold text-gray-800">
        YASMIM SOL
      </router-link>
      
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

      <button class="md:hidden text-gray-800 hover:text-purple-600">
        <i class="fas fa-bars text-xl"></i>
      </button>
    </nav>
  </header>
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore'

const cartStore = useCartStore()
const menuItems = [
  { name: 'LAR', path: '/' },
  { name: 'SOBRE', path: '/sobre' },
  { name: 'COMPRAR', path: '/comprar' },
  { name: 'CONTATO', path: '/contato' }
]
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
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