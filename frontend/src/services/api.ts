import axios from 'axios';
import { useRouter } from 'vue-router';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
});

// Lista de endpoints que não requerem autenticação
const publicEndpoints = [
  '/products/id',
  '/products/slug',
  '/products$', // Apenas GET /products é público
  '/categories$', // Apenas GET /categories é público
  '/categories/slug',
  '/auth/login',
  '/contact',
  '/sanctum/csrf-cookie',
  // Endpoints de frete - estes precisam ser acessíveis sem autenticação
  '/shipping/shop-cep',
  '/shipping/cep',
  '/shipping/calculate'
];

// Interceptor para adicionar o token de autenticação
api.interceptors.request.use(async (config) => {
  try {
    // Obter CSRF token se necessário
    if (!document.cookie.includes('XSRF-TOKEN')) {
      console.log('Obtendo CSRF token...');
      try {
        await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
          withCredentials: true
        });
        console.log('CSRF token obtido com sucesso!');
      } catch (err) {
        console.error('Erro ao obter CSRF token:', err);
      }
    }

    // Se estamos enviando FormData, não devemos definir o Content-Type
    if (config.data instanceof FormData) {
      delete config.headers['Content-Type'];
    }

    // Adicionar token para todas as requisições exceto as públicas
    const isPublicEndpoint = publicEndpoints.some(endpoint => {
      const regex = new RegExp(endpoint);
      return regex.test(config.url || '');
    });

    // Adicionar token apenas se não for um endpoint público
    if (!isPublicEndpoint) {
      let token = localStorage.getItem('token');

      if (!token) {
        console.warn('Tentativa de acessar rota protegida sem token:', config.url);
      } else {
        // Garantir que o token esteja no formato correto
        if (!token.startsWith('Bearer ') && !token.startsWith('bearer ')) {
          token = `Bearer ${token}`;
          localStorage.setItem('token', token);
        }

        config.headers.Authorization = token;

        // Log para diagnóstico
        console.log(`Enviando requisição para ${config.url} com token: ${token.substring(0, 15)}...`);
      }
    }

    return config;
  } catch (error) {
    console.error('Erro no interceptor de requisições:', error);
    return config;
  }
});

// Interceptor para tratar erros
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    console.error('API Error:', error);

    // Se recebermos um erro 401 (Não autorizado)
    if (error.response && error.response.status === 401) {
      // Limpar o token e redirecionar para o login
      localStorage.removeItem('token');

      // Se não estamos já na página de login, redirecionar
      if (window.location.pathname !== '/login') {
        console.warn('Token expirado ou inválido. Redirecionando para login...');
        window.location.href = '/login';
      }
    }

    return Promise.reject(error);
  }
);

export interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  stock: number;
  image_url: string | null;
  category: string | Category;
  category_id?: number;
  active: boolean;
  featured: boolean;
  slug: string;
  created_at: string;
  updated_at: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  links: {
    first: string;
    last: string;
    next: string | null;
    prev: string | null;
  };
  meta: {
    current_page: number;
    from: number;
    last_page: number;
    path: string;
    per_page: number;
    to: number;
    total: number;
  };
}

export const productService = {
  async getAll(): Promise<Product[]> {
    const response = await api.get<PaginatedResponse<Product> | Product[]>('/products');
    // Handle both paginated and non-paginated responses
    if ('data' in response.data && Array.isArray(response.data.data)) {
      return response.data.data;
    }
    return response.data as Product[];
  },

  async getById(id: number): Promise<Product> {
    const response = await api.get<Product>(`/products/id/${id}`);
    return response.data;
  },

  async getBySlug(slug: string): Promise<Product> {
    const response = await api.get<Product>(`/products/slug/${slug}`);
    return response.data;
  },

  async create(productData: FormData): Promise<Product> {
    // Uso direto do axios em vez do api com interceptores
    const token = localStorage.getItem('token');

    // Log extensivo para diagnóstico
    console.log('Token para criar produto:', token ? token.substring(0, 15) + '...' : 'nenhum');
    console.log('URL da requisição:', 'http://localhost:8000/api/products');
    console.log('Dados enviados:', Object.fromEntries(productData.entries()));

    try {
      const response = await axios({
        method: 'post',
        url: 'http://localhost:8000/api/products',
        data: productData,
        headers: {
          'Accept': 'application/json',
          'Authorization': token || '',
          'X-Requested-With': 'XMLHttpRequest'
        },
        withCredentials: true
      });

      console.log('Resposta da criação do produto:', response.data);
      return response.data;
    } catch (error: any) {
      console.error('Erro detalhado na criação do produto:', {
        status: error.response?.status,
        statusText: error.response?.statusText,
        headers: error.response?.headers,
        data: error.response?.data
      });
      throw error;
    }
  },

  async update(id: number, productData: FormData): Promise<Product> {
    // Add _method=PUT to support Laravel's form method spoofing
    productData.append('_method', 'PUT');
    const response = await api.post<Product>(`/products/${id}`, productData);
    return response.data;
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/products/${id}`);
  },
};

export interface Category {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  image_url: string | null;
  active: boolean;
  isVirtual?: boolean; // Indica se é uma categoria virtual (não salva no banco)
  created_at: string;
  updated_at: string;
}

export const categoryService = {
  async getAll(): Promise<Category[]> {
    const response = await api.get<PaginatedResponse<Category> | Category[]>('/categories');
    // Handle both paginated and non-paginated responses
    if ('data' in response.data && Array.isArray(response.data.data)) {
      return response.data.data;
    }
    return response.data as Category[];
  },

  async getById(id: number): Promise<Category> {
    const response = await api.get<Category>(`/categories/${id}`);
    return response.data;
  },

  async create(category: FormData): Promise<Category> {
    const response = await api.post<Category>('/categories', category);
    return response.data;
  },

  async update(id: number, category: FormData): Promise<Category> {
    // Add _method=PUT to support Laravel's form method spoofing
    category.append('_method', 'PUT');
    const response = await api.post<Category>(`/categories/${id}`, category);
    return response.data;
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/categories/${id}`);
  },
};

export interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  status: boolean;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
  password?: string; // Opcional, usado apenas para criação/atualização
}

export const userService = {
  async getAll(): Promise<User[]> {
    const response = await api.get<PaginatedResponse<User> | User[]>('/users');
    // Handle both paginated and non-paginated responses
    if ('data' in response.data && Array.isArray(response.data.data)) {
      return response.data.data;
    }
    return response.data as User[];
  },

  async getById(id: number): Promise<User> {
    const response = await api.get<User>(`/users/${id}`);
    return response.data;
  },

  async create(userData: Partial<User>): Promise<User> {
    // Explicitamente criar um objeto com apenas os campos que queremos
    const payload = {
      name: userData.name,
      email: userData.email,
      password: userData.password,
      role: userData.role || 'user',
      status: userData.status
    };

    console.log('API - Enviando payload:', payload);
    const response = await api.post<User>('/users', payload);
    return response.data;
  },

  async update(id: number, userData: Partial<User>): Promise<User> {
    // Explicitamente criar um objeto com apenas os campos que queremos
    const payload: Record<string, any> = {};

    if (userData.name !== undefined) payload.name = userData.name;
    if (userData.email !== undefined) payload.email = userData.email;
    if (userData.password !== undefined) payload.password = userData.password;
    if (userData.role !== undefined) payload.role = userData.role;
    if (userData.status !== undefined) payload.status = userData.status;

    console.log('API - Enviando payload de atualização:', payload);
    const response = await api.put<User>(`/users/${id}`, payload);
    return response.data;
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/users/${id}`);
  },
};

export const testService = {
  async test(): Promise<{ message: string; timestamp: string }> {
    const response = await api.get('/test');
    return response.data;
  }
};

export interface ContactForm {
  name: string;
  email: string;
  subject: string;
  message: string;
}

export const contactService = {
  async send(data: ContactForm): Promise<{ message: string }> {
    const response = await api.post<{ message: string }>('/contact', data);
    return response.data;
  }
};

export interface Address {
  id?: number;
  name: string;
  street: string;
  number: string;
  complement?: string;
  district: string;
  city: string;
  state: string;
  zipcode: string;
  is_default?: boolean;
  created_at?: string;
  updated_at?: string;
}

export interface UserSettings {
  name: string;
  email: string;
  newsletter: boolean;
  preferences?: any;
  last_login_at?: string;
}

export interface PasswordChange {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}

export const addressService = {
  async getAll(): Promise<Address[]> {
    const response = await api.get<Address[]>('/addresses');
    return response.data;
  },

  async create(address: Address): Promise<Address> {
    const response = await api.post<Address>('/addresses', address);
    return response.data;
  },

  async update(id: number, address: Partial<Address>): Promise<Address> {
    const response = await api.put<Address>(`/addresses/${id}`, address);
    return response.data;
  },

  async delete(id: number): Promise<void> {
    await api.delete(`/addresses/${id}`);
  }
};

export const userSettingsService = {
  async get(): Promise<UserSettings> {
    const response = await api.get<UserSettings>('/user/settings');
    return response.data;
  },

  async update(settings: Partial<UserSettings>): Promise<UserSettings> {
    const response = await api.put<UserSettings>('/user/settings', settings);
    return response.data;
  },

  async changePassword(passwordData: PasswordChange): Promise<{ message: string }> {
    const response = await api.put<{ message: string }>('/user/settings', passwordData);
    return response.data;
  }
};

export default api; 