<template>
  <div class="product-list">
    <h2>Produtos</h2>
    <div class="products-grid">
      <div v-for="product in products" :key="product.id" class="product-card">
        <img :src="product.image_url || 'https://via.placeholder.com/150'" :alt="product.name" class="product-image">
        <div class="product-info">
          <h3>{{ product.name }}</h3>
          <p class="description">{{ product.description }}</p>
          <p class="price">R$ {{ product.price.toFixed(2) }}</p>
          <p class="stock">Estoque: {{ product.stock }}</p>
          <p class="category">{{ product.category }}</p>
          <div class="actions">
            <button @click="editProduct(product)" class="edit-btn">Editar</button>
            <button @click="deleteProduct(product.id)" class="delete-btn">Excluir</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { productService, type Product } from '@/services/api';

const products = ref<Product[]>([]);

const loadProducts = async () => {
  try {
    products.value = await productService.getAll();
  } catch (error) {
    console.error('Erro ao carregar produtos:', error);
  }
};

const deleteProduct = async (id: number) => {
  if (confirm('Tem certeza que deseja excluir este produto?')) {
    try {
      await productService.delete(id);
      products.value = products.value.filter(p => p.id !== id);
    } catch (error) {
      console.error('Erro ao excluir produto:', error);
    }
  }
};

const editProduct = (product: Product) => {
  // TODO: Implementar edição do produto
  console.log('Editar produto:', product);
};

onMounted(loadProducts);
</script>

<style scoped>
.product-list {
  padding: 20px;
}

.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 20px;
  margin-top: 20px;
}

.product-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.2s;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.product-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.product-info {
  padding: 15px;
}

h3 {
  margin: 0 0 10px;
  font-size: 1.2em;
}

.description {
  color: #666;
  margin-bottom: 10px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.price {
  font-size: 1.4em;
  font-weight: bold;
  color: #2c3e50;
  margin: 10px 0;
}

.stock, .category {
  color: #666;
  font-size: 0.9em;
  margin: 5px 0;
}

.actions {
  display: flex;
  gap: 10px;
  margin-top: 15px;
}

button {
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}

.edit-btn {
  background-color: #3498db;
  color: white;
}

.edit-btn:hover {
  background-color: #2980b9;
}

.delete-btn {
  background-color: #e74c3c;
  color: white;
}

.delete-btn:hover {
  background-color: #c0392b;
}
</style> 