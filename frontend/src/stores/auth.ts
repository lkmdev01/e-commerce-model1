import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../services/api';

interface LoginCredentials {
  email: string;
  password: string;
  remember?: boolean;
}

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(null);
  const loading = ref(false);

  const setUser = (userData: User | null) => {
    user.value = userData;
  };

  const setToken = (tokenValue: string | null) => {
    token.value = tokenValue;
    if (tokenValue) {
      localStorage.setItem('token', tokenValue);
      api.defaults.headers.common['Authorization'] = `Bearer ${tokenValue}`;
    } else {
      localStorage.removeItem('token');
      delete api.defaults.headers.common['Authorization'];
    }
  };

  const login = async (credentials: LoginCredentials) => {
    try {
      loading.value = true;
      const response = await api.post('/auth/login', credentials);
      const { user: userData, token: tokenValue } = response.data;

      setUser(userData);
      setToken(tokenValue);

      return response.data;
    } finally {
      loading.value = false;
    }
  };

  const logout = () => {
    setUser(null);
    setToken(null);
  };

  const checkAuth = async () => {
    try {
      const tokenValue = localStorage.getItem('token');
      if (!tokenValue) return false;

      setToken(tokenValue);
      const response = await api.get('/auth/user');
      setUser(response.data);
      return true;
    } catch (error) {
      logout();
      return false;
    }
  };

  return {
    user,
    token,
    loading,
    login,
    logout,
    checkAuth
  };
}); 