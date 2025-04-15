<template>
  <div class="min-h-screen bg-gray-100">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-[#9810FA] text-white transform transition-transform duration-300 ease-in-out z-30" :class="{ '-translate-x-full': !sidebarOpen }">
      <div class="flex items-center justify-between h-16 px-4 border-b border-white/10">
        <router-link to="/dashboard" class="text-xl font-bold">LOJA VIRTUAL</router-link>
        <button @click="toggleSidebar" class="md:hidden">
          <i class="fas fa-times"></i>
        </button>
      </div>
      
      <nav class="mt-6 px-4">
        <ul class="space-y-2">
          <li>
            <router-link to="/dashboard" class="flex items-center px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">
              <i class="fas fa-home w-6"></i>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/products" class="flex items-center px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">
              <i class="fas fa-box w-6"></i>
              <span>Produtos</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/categories" class="flex items-center px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">
              <i class="fas fa-tags w-6"></i>
              <span>Categorias</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/orders" class="flex items-center px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">
              <i class="fas fa-shopping-cart w-6"></i>
              <span>Pedidos</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/users" class="flex items-center px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">
              <i class="fas fa-users w-6"></i>
              <span>Usuários</span>
            </router-link>
          </li>
          <li>
            <router-link to="/dashboard/settings" class="flex items-center px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">
              <i class="fas fa-cog w-6"></i>
              <span>Configurações</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="md:pl-64 flex flex-col min-h-screen">
      <!-- Header -->
      <header class="bg-white shadow-sm z-20">
        <div class="flex items-center justify-between h-16 px-4">
          <button @click="toggleSidebar" class="md:hidden text-gray-600">
            <i class="fas fa-bars"></i>
          </button>
          
          <div class="flex items-center space-x-4">
            <div class="relative">
              <input 
                type="text" 
                placeholder="Buscar..." 
                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
              >
              <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <div class="relative">
              <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-900">
                <img src="/images/person1.jpeg" alt="Avatar" class="w-8 h-8 rounded-full">
                <span class="hidden md:inline">Admin</span>
                <i class="fas fa-chevron-down text-xs"></i>
              </button>
            </div>
          </div>
        </div>
      </header>
      
      <!-- Page Content -->
      <main class="flex-1 p-6">
        <router-view></router-view>
      </main>
      
      <!-- Footer -->
      <footer class="bg-white border-t border-gray-200 py-4 px-6">
        <p class="text-center text-gray-500 text-sm">
          &copy; 2024 Yasmim Sol. Todos os direitos reservados.
        </p>
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

const sidebarOpen = ref(true);

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};
</script> 