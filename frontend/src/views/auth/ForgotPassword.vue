<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Recuperação de Senha
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Informe seu email para receber instruções de recuperação
        </p>
      </div>
      
      <div v-if="successMessage" class="mt-4 bg-green-50 p-4 rounded-md">
        <div class="flex">
          <div class="flex-shrink-0">
            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="ml-3">
            <p class="text-sm font-medium text-green-800">
              {{ successMessage }}
            </p>
            <div class="mt-4">
              <div class="flex">
                <router-link to="/login" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                  Voltar para o login
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <form v-if="!successMessage" class="mt-8 space-y-6" @submit.prevent="handleForgotPassword">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <div class="mt-1">
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              placeholder="seu@email.com"
            />
          </div>
        </div>

        <div v-if="error" class="text-red-600 text-sm">
          {{ error }}
        </div>

        <div class="flex items-center justify-between">
          <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-700">
            Voltar para o login
          </router-link>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
          >
            {{ loading ? 'Enviando...' : 'Enviar instruções' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useAuthStore } from '../../stores/auth';

const authStore = useAuthStore();

const loading = ref(false);
const error = ref('');
const successMessage = ref('');
const form = ref({
  email: ''
});

const handleForgotPassword = async () => {
  try {
    error.value = '';
    loading.value = true;
    
    const response = await authStore.forgotPassword(form.value);
    successMessage.value = response.message;
    
    // Para ambientes de desenvolvimento, podemos mostrar o link e token
    if (import.meta.env.MODE !== 'production' && response.reset_url) {
      console.log('Link para redefinição:', response.reset_url);
      console.log('Token:', response.token);
    }
  } catch (e: any) {
    console.error('Erro ao solicitar recuperação de senha:', e);
    if (e.response && e.response.data && e.response.data.message) {
      error.value = e.response.data.message;
    } else if (e.response && e.response.data && e.response.data.errors) {
      const errorMessages = Object.values(e.response.data.errors).flat();
      error.value = errorMessages.join(' ');
    } else {
      error.value = 'Ocorreu um erro ao processar sua solicitação. Tente novamente.';
    }
  } finally {
    loading.value = false;
  }
};
</script> 