<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Redefinir sua senha
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Crie uma nova senha segura
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
                  Ir para o login
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <form v-if="!successMessage" class="mt-8 space-y-6" @submit.prevent="handleResetPassword">
        <input type="hidden" v-model="form.token" name="token">
        <input type="hidden" v-model="form.email" name="email">

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
              readonly
              disabled
            />
          </div>
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Nova Senha</label>
          <div class="mt-1">
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              placeholder="Nova senha"
            />
          </div>
        </div>

        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nova Senha</label>
          <div class="mt-1">
            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              name="password_confirmation"
              type="password"
              required
              class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm"
              placeholder="Confirme a nova senha"
            />
          </div>
        </div>

        <div v-if="error" class="text-red-600 text-sm">
          {{ error }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading || !isFormValid"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 disabled:opacity-50"
          >
            {{ loading ? 'Redefinindo senha...' : 'Redefinir senha' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import { useRouter, useRoute } from 'vue-router';

const authStore = useAuthStore();
const router = useRouter();
const route = useRoute();

const loading = ref(false);
const error = ref('');
const successMessage = ref('');
const form = ref({
  email: '',
  token: '',
  password: '',
  password_confirmation: ''
});

const isFormValid = computed(() => {
  return (
    form.value.email &&
    form.value.token &&
    form.value.password &&
    form.value.password === form.value.password_confirmation &&
    form.value.password.length >= 8
  );
});

onMounted(() => {
  // Pegar email e token da URL
  const token = route.query.token as string;
  const email = route.query.email as string;
  
  if (!token || !email) {
    error.value = 'Link inválido ou expirado. Por favor, solicite uma nova redefinição de senha.';
    return;
  }
  
  form.value.token = token;
  form.value.email = email;
});

const handleResetPassword = async () => {
  try {
    error.value = '';
    
    if (!isFormValid.value) {
      if (form.value.password !== form.value.password_confirmation) {
        error.value = 'As senhas não conferem';
      } else if (form.value.password.length < 8) {
        error.value = 'A senha deve ter pelo menos 8 caracteres';
      } else {
        error.value = 'Por favor, preencha todos os campos';
      }
      return;
    }
    
    loading.value = true;
    
    const response = await authStore.resetPassword(form.value);
    successMessage.value = response.message || 'Sua senha foi redefinida com sucesso!';
    
    // Redirecionar para login após alguns segundos
    setTimeout(() => {
      router.push('/login');
    }, 3000);
    
  } catch (e: any) {
    console.error('Erro ao redefinir senha:', e);
    if (e.response && e.response.data && e.response.data.message) {
      error.value = e.response.data.message;
    } else if (e.response && e.response.data && e.response.data.errors) {
      const errorMessages = Object.values(e.response.data.errors).flat();
      error.value = errorMessages.join(' ');
    } else {
      error.value = 'Ocorreu um erro ao redefinir sua senha. Tente novamente ou solicite um novo link.';
    }
  } finally {
    loading.value = false;
  }
};
</script> 