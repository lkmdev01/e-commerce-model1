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
          <!-- Menu para Administradores -->
          <template v-if="authStore.isAdmin">
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
          </template>

          <!-- Menu para Usuários Normais -->
          <template v-else>
            <li>
              <router-link to="/dashboard/profile" class="flex items-center px-4 py-2 rounded-lg hover:bg-white/10 transition-colors">
                <i class="fas fa-user w-6"></i>
                <span>Meu Perfil</span>
              </router-link>
            </li>
          </template>
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
            
            <!-- Menu do Usuário -->
            <div class="relative">
              <button 
                @click="toggleUserMenu" 
                class="flex items-center space-x-2 text-gray-600 hover:text-gray-900"
              >
                <img src="/images/person1.jpeg" alt="Avatar" class="w-8 h-8 rounded-full">
                <span class="hidden md:inline">{{ authStore.user?.name || 'Usuário' }}</span>
                <i class="fas fa-chevron-down text-xs"></i>
              </button>

              <!-- Dropdown Menu -->
              <div 
                v-if="isUserMenuOpen" 
                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50"
              >
                <button 
                  @click="handleLogout" 
                  class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                >
                  <i class="fas fa-sign-out-alt mr-2"></i>
                  Sair
                </button>
              </div>
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
          &copy; 2024 Loja Virtual. Todos os direitos reservados.
        </p>
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useApi } from '@/composables/useApi';

const router = useRouter();
const authStore = useAuthStore();
const api = useApi();
const sidebarOpen = ref(true);
const isUserMenuOpen = ref(false);

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};

const toggleUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
};

const handleLogout = async () => {
  try {
    await api.post('/logout');
    authStore.logout();
    router.push('/login');
  } catch (error) {
    console.error('Erro ao fazer logout:', error);
  }
};

// Fechar o menu quando clicar fora
const closeUserMenu = (event: MouseEvent) => {
  const target = event.target as HTMLElement;
  if (!target.closest('.relative')) {
    isUserMenuOpen.value = false;
  }
};

// Adicionar e remover o event listener
onMounted(() => {
  document.addEventListener('click', closeUserMenu);
});

onUnmounted(() => {
  document.removeEventListener('click', closeUserMenu);
});
</script> 