import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { useApi } from '@/composables/useApi';
import axios from 'axios';

interface LoginCredentials {
  email: string;
  password: string;
  remember?: boolean;
}

interface User {
  id: number;
  name: string;
  email: string;
  role: 'admin' | 'user';
}

export const useAuthStore = defineStore('auth', () => {
  const api = useApi();
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const loading = ref(false);

  const isAuthenticated = computed(() => !!user.value);
  const isAdmin = computed(() => user.value?.role === 'admin');

  const setUser = (userData: User) => {
    user.value = userData;
  };

  const setToken = (tokenValue: string) => {
    // Garantir que o token tenha o prefixo Bearer
    if (!tokenValue.startsWith('Bearer ') && !tokenValue.startsWith('bearer ')) {
      tokenValue = `Bearer ${tokenValue}`;
    }

    token.value = tokenValue;
    localStorage.setItem('token', tokenValue);
    console.log('Token salvo com sucesso:', tokenValue.substring(0, 15) + '...');
  };

  const login = async (credentials: LoginCredentials) => {
    try {
      loading.value = true;

      // Obter CSRF token antes do login
      await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
        withCredentials: true
      });

      console.log('Iniciando processo de login...');
      const response = await api.post('/auth/login', credentials);
      console.log('Resposta do login:', response.data);

      const { user: userData, token: tokenValue } = response.data;

      if (!userData || !tokenValue) {
        console.error('Dados de login inválidos:', response.data);
        throw new Error('Dados de autenticação inválidos');
      }

      console.log('Dados do usuário:', userData);
      console.log('Token recebido:', tokenValue.substring(0, 15) + '...');

      setUser(userData);
      setToken(tokenValue);

      console.log('Login concluído com sucesso!');

      return response.data;
    } catch (error) {
      console.error('Erro no login:', error);
      throw error;
    } finally {
      loading.value = false;
    }
  };

  const logout = async () => {
    try {
      await api.post('/logout');
    } catch (error) {
      console.error('Erro ao fazer logout:', error);
    } finally {
      user.value = null;
      token.value = null;
      localStorage.removeItem('token');
    }
  };

  const checkAuth = async () => {
    const storedToken = localStorage.getItem('token');
    if (storedToken) {
      token.value = storedToken;
      try {
        const response = await api.get('/user');
        const userData = response.data;

        if (!userData) {
          throw new Error('Dados de usuário inválidos');
        }

        setUser(userData);
        return true;
      } catch (error) {
        console.error('Erro ao verificar autenticação:', error);
        logout();
        return false;
      }
    }
    return false;
  };

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    login,
    logout,
    checkAuth,
  };
}); 