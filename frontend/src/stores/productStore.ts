import { defineStore } from 'pinia';
import { productService, type Product } from '@/services/api';

interface ProductState {
  products: Product[];
  currentProduct: Product | null;
  loading: boolean;
  error: string | null;
}

export const useProductStore = defineStore('product', {
  state: (): ProductState => ({
    products: [],
    currentProduct: null,
    loading: false,
    error: null,
  }),

  actions: {
    async fetchProducts() {
      this.loading = true;
      this.error = null;
      try {
        this.products = await productService.getAll();
      } catch (error) {
        this.error = 'Erro ao carregar produtos';
        console.error('Erro ao carregar produtos:', error);
      } finally {
        this.loading = false;
      }
    },

    async fetchProductBySlug(slug: string) {
      this.loading = true;
      this.error = null;
      try {
        this.currentProduct = await productService.getBySlug(slug);
      } catch (error) {
        this.error = 'Erro ao carregar produto';
        console.error('Erro ao carregar produto:', error);
      } finally {
        this.loading = false;
      }
    },

    async createProduct(productData: FormData) {
      this.loading = true;
      this.error = null;
      try {
        const newProduct = await productService.create(productData);
        this.products.push(newProduct);
        return newProduct;
      } catch (error) {
        this.error = 'Erro ao criar produto';
        console.error('Erro ao criar produto:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateProduct(id: number, productData: FormData) {
      this.loading = true;
      this.error = null;
      try {
        const updatedProduct = await productService.update(id, productData);
        const index = this.products.findIndex(p => p.id === id);
        if (index !== -1) {
          this.products[index] = updatedProduct;
        }
        if (this.currentProduct?.id === id) {
          this.currentProduct = updatedProduct;
        }
        return updatedProduct;
      } catch (error) {
        this.error = 'Erro ao atualizar produto';
        console.error('Erro ao atualizar produto:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteProduct(id: number) {
      this.loading = true;
      this.error = null;
      try {
        await productService.delete(id);
        this.products = this.products.filter(p => p.id !== id);
        if (this.currentProduct?.id === id) {
          this.currentProduct = null;
        }
      } catch (error) {
        this.error = 'Erro ao deletar produto';
        console.error('Erro ao deletar produto:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
  },
}); 