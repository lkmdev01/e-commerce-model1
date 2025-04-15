import axios from 'axios';

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  withCredentials: true,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest'
  },
});

// Interceptor para adicionar o token de autenticação
api.interceptors.request.use(async (config) => {
  // Obter CSRF token se necessário
  if (!document.cookie.includes('XSRF-TOKEN')) {
    await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
      withCredentials: true
    });
  }

  // Se estamos enviando FormData, não devemos definir o Content-Type
  // O axios vai automaticamente definir como 'multipart/form-data' e incluir o boundary
  if (config.data instanceof FormData) {
    delete config.headers['Content-Type'];
  }

  const token = localStorage.getItem('token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Interceptor para tratar erros
api.interceptors.response.use(
  (response) => response,
  async (error) => {
    console.error('API Error:', error);
    if (error.response?.status === 401) {
      // Redirecionar para login
      window.location.href = '/login';
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
    const response = await api.post<Product>('/products', productData);
    return response.data;
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

export default api; 