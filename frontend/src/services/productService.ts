import api from './api';

export interface Product {
  id: number;
  name: string;
  description: string;
  price: number;
  category: string;
  image: string;
  stock: number;
  active: boolean;
  featured: boolean;
  slug: string;
  created_at: string;
  updated_at: string;
}

export const productService = {
  async getAll() {
    const response = await api.get<Product[]>('/products');
    return response.data;
  },

  async getBySlug(slug: string) {
    const response = await api.get<Product>(`/products/${slug}`);
    return response.data;
  },

  async create(productData: FormData) {
    const response = await api.post<Product>('/products', productData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  async update(id: number, productData: FormData) {
    const response = await api.put<Product>(`/products/${id}`, productData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  },

  async delete(id: number) {
    await api.delete(`/products/${id}`);
  },
}; 