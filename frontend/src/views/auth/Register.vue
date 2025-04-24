<template>
  <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
        Criar uma conta
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Ou
        <router-link to="/login" class="font-medium text-[#9810FA] hover:text-[#7a0dc8]">
          faça login se já tiver uma conta
        </router-link>
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <div v-if="success" class="mb-4 p-4 rounded-md bg-green-50 text-green-700 border border-green-200">
          <p>Conta criada com sucesso! Você será redirecionado para a página de login.</p>
        </div>

        <div v-if="generalError" class="mb-4 p-4 rounded-md bg-red-50 text-red-700 border border-red-200">
          <p>{{ generalError }}</p>
        </div>

        <form class="space-y-6" @submit.prevent="handleSubmit">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nome
            </label>
            <div class="mt-1">
              <input
                id="name"
                v-model="form.name"
                type="text"
                required
                :class="[
                  'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#9810FA] focus:border-[#9810FA] sm:text-sm',
                  errors.name ? 'border-red-300' : 'border-gray-300'
                ]"
              />
              <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">
              Email
            </label>
            <div class="mt-1">
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                :class="[
                  'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#9810FA] focus:border-[#9810FA] sm:text-sm',
                  errors.email ? 'border-red-300' : 'border-gray-300'
                ]"
              />
              <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">
              Senha
            </label>
            <div class="mt-1">
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                :class="[
                  'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#9810FA] focus:border-[#9810FA] sm:text-sm',
                  errors.password ? 'border-red-300' : 'border-gray-300'
                ]"
              />
              <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password[0] }}</p>
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
              Confirmar Senha
            </label>
            <div class="mt-1">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                required
                :class="[
                  'appearance-none block w-full px-3 py-2 border rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-[#9810FA] focus:border-[#9810FA] sm:text-sm',
                  errors.password_confirmation ? 'border-red-300' : 'border-gray-300'
                ]"
              />
              <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation[0] }}</p>
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading || success"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#9810FA] hover:bg-[#7a0dc8] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9810FA] disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="loading">Registrando...</span>
              <span v-else>Registrar</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { useApi } from '@/composables/useApi';

const router = useRouter();
const authStore = useAuthStore();
const api = useApi();

const loading = ref(false);
const success = ref(false);
const generalError = ref('');
const errors = ref<Record<string, string[]>>({});

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const handleSubmit = async () => {
  try {
    loading.value = true;
    errors.value = {};
    generalError.value = '';
    
    const response = await api.post('/auth/register', form.value);
    
    success.value = true;
    
    // Redirecionar para a página de login após 2 segundos
    setTimeout(() => {
      router.push('/login');
    }, 2000);
    
  } catch (error: any) {
    console.error('Erro ao registrar:', error);
    
    if (error.response) {
      // Erros de validação
      if (error.response.status === 422 && error.response.data.errors) {
        errors.value = error.response.data.errors;
      } 
      // Mensagem geral de erro
      else if (error.response.data.message) {
        generalError.value = error.response.data.message;
      } 
      // Erro genérico
      else {
        generalError.value = 'Ocorreu um erro ao processar seu registro. Tente novamente.';
      }
    } else {
      generalError.value = 'Não foi possível conectar ao servidor. Verifique sua conexão.';
    }
  } finally {
    loading.value = false;
  }
};
</script> 