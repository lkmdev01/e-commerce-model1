<template>
  <div class="p-6 space-y-8">
    <h1 class="text-3xl font-bold">Verificação de Autenticação</h1>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Status da Autenticação</h2>
      
      <div class="space-y-4">
        <div>
          <span class="font-medium">Status:</span> 
          <span :class="isAuthenticated ? 'text-green-600' : 'text-red-600'">
            {{ isAuthenticated ? 'Autenticado' : 'Não Autenticado' }}
          </span>
        </div>
        
        <div v-if="isAuthenticated && user">
          <h3 class="font-medium">Informações do Usuário:</h3>
          <div class="mt-2 p-3 bg-gray-50 rounded">
            <p><span class="font-medium">ID:</span> {{ user.id }}</p>
            <p><span class="font-medium">Nome:</span> {{ user.name }}</p>
            <p><span class="font-medium">Email:</span> {{ user.email }}</p>
            <p><span class="font-medium">Papel:</span> <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded">{{ user.role }}</span></p>
          </div>
        </div>
        
        <div>
          <h3 class="font-medium">Token:</h3>
          <div class="mt-2 p-3 bg-gray-50 rounded break-all">
            <p v-if="token">{{ token }}</p>
            <p v-else class="text-red-600">Nenhum token encontrado</p>
          </div>
        </div>
      </div>
      
      <div class="mt-6 space-y-4">
        <button 
          @click="testAuthentication" 
          class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700"
        >
          Testar Autenticação
        </button>
        
        <div v-if="testResult" class="mt-4">
          <h3 class="font-medium mb-2">Resultado do Teste:</h3>
          <div class="p-3 bg-gray-50 rounded">
            <pre>{{ JSON.stringify(testResult, null, 2) }}</pre>
          </div>
        </div>
        
        <div v-if="testError" class="mt-4">
          <h3 class="font-medium mb-2 text-red-600">Erro no Teste:</h3>
          <div class="p-3 bg-red-50 text-red-800 rounded">
            <pre>{{ JSON.stringify(testError, null, 2) }}</pre>
          </div>
        </div>
      </div>
    </div>
    
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold mb-4">Ações</h2>
      
      <div class="space-y-4">
        <button 
          @click="fixToken" 
          class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700"
        >
          Corrigir Token (Adicionar Bearer)
        </button>
        
        <button 
          @click="logout" 
          class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
        >
          Sair e Limpar Token
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '@/stores/auth';
import axios from 'axios';
import { useRouter } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();

const testResult = ref(null);
const testError = ref<any>(null);
const token = ref(localStorage.getItem('token'));
const user = computed(() => authStore.user);
const isAuthenticated = computed(() => authStore.isAuthenticated);

onMounted(async () => {
  token.value = localStorage.getItem('token');
  await authStore.checkAuth();
});

const testAuthentication = async () => {
  testResult.value = null;
  testError.value = null;
  
  try {
    const response = await axios.get('http://localhost:8000/api/user', {
      headers: {
        'Authorization': token.value || '',
        'Accept': 'application/json'
      },
      withCredentials: true
    });
    
    testResult.value = response.data;
  } catch (error: any) {
    testError.value = {
      message: error.message,
      status: error.response?.status,
      data: error.response?.data
    };
  }
};

const fixToken = () => {
  const currentToken = localStorage.getItem('token');
  if (currentToken && !currentToken.startsWith('Bearer ')) {
    const fixedToken = `Bearer ${currentToken}`;
    localStorage.setItem('token', fixedToken);
    token.value = fixedToken;
  }
};

const logout = async () => {
  await authStore.logout();
  localStorage.removeItem('token');
  token.value = null;
  router.push('/login');
};
</script> 