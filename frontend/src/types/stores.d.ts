declare module '@/stores/auth' {
  import { Store } from 'pinia';

  interface User {
    id: number;
    name: string;
    email: string;
    role: string;
  }

  interface LoginCredentials {
    email: string;
    password: string;
    remember?: boolean;
  }

  export const useAuthStore: () => Store<'auth', {
    user: User | null;
    token: string | null;
    loading: boolean;
    isAuthenticated: boolean;
    isAdmin: boolean;
    login: (credentials: LoginCredentials) => Promise<any>;
    logout: () => void;
    checkAuth: () => Promise<boolean>;
  }>;
} 