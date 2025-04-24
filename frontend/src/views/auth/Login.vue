<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Bem-vindo ao Dashboard
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Faça login para acessar sua conta
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="email" class="sr-only">Email</label>
            <input
              id="email"
              v-model="form.email"
              name="email"
              type="email"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm"
              placeholder="Email"
            />
          </div>
          <div>
            <label for="password" class="sr-only">Senha</label>
            <input
              id="password"
              v-model="form.password"
              name="password"
              type="password"
              required
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm"
              placeholder="Senha"
            />
          </div>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember-me"
              v-model="form.remember"
              name="remember-me"
              type="checkbox"
              class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
            />
            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
              Lembrar-me
            </label>
          </div>

          <div class="text-sm">
            <router-link to="/forgot-password" class="font-medium text-purple-600 hover:text-purple-700">
              Esqueceu sua senha?
            </router-link>
          </div>
        </div>

        <div v-if="error" class="text-red-600 text-sm">
          {{ error }}
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
          >
            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg
                class="h-5 w-5 text-purple-300 group-hover:text-purple-200"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20"
                fill="currentColor"
                aria-hidden="true"
              >
                <path
                  fill-rule="evenodd"
                  d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                  clip-rule="evenodd"
                />
              </svg>
            </span>
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';

const router = useRouter();
const authStore = useAuthStore();

const loading = ref(false);
const error = ref('');
const form = ref({
  email: '',
  password: '',
  remember: false
});

const handleLogin = async () => {
  try {
    error.value = '';
    loading.value = true;
    
    console.log('Tentando login com:', {
      email: form.value.email,
      remember: form.value.remember
    });
    
    // Login feito com sucesso
    await authStore.login(form.value);
    
    // Verificar se existe um redirecionamento pendente após o login
    const redirectUrl = localStorage.getItem('redirectAfterLogin');
    
    if (redirectUrl) {
      // Limpa o redirecionamento armazenado
      localStorage.removeItem('redirectAfterLogin');
      // Redireciona para a URL salva
      router.push(redirectUrl);
    } else {
      // Comportamento padrão: verificar o papel do usuário
      if (authStore.user?.role === 'admin') {
        // Administrador vai para o dashboard principal
        router.push('/dashboard');
      } else {
        // Usuário normal vai para o perfil
        router.push('/dashboard/profile');
      }
    }
  } catch (e: any) {
    console.error('Erro ao fazer login:', e);
    if (e.response && e.response.data && e.response.data.message) {
      error.value = e.response.data.message;
    } else if (e.response && e.response.data && e.response.data.errors) {
      const errorMessages = Object.values(e.response.data.errors).flat();
      error.value = errorMessages.join(' ');
    } else {
      error.value = 'Não foi possível fazer login. Verifique suas credenciais.';
    }
  } finally {
    loading.value = false;
  }
};
</script> 