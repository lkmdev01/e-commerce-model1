<template>
  <section class="py-8 md:py-16 bg-white">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-8">
        <div v-for="product in products" :key="product.id" class="group relative">
          <!-- Link para a página de detalhes -->
          <router-link :to="{ name: 'produto-detalhe', params: { id: product.id }}">
            <div class="relative overflow-hidden mb-4">
              <img 
                :src="product.image" 
                :alt="product.name"
                class="w-full h-64 sm:h-72 md:h-96 object-cover transform group-hover:scale-105 transition-transform duration-300"
              >
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
            </div>
            <div class="text-center px-2">
              <h3 class="text-base sm:text-lg font-semibold text-gray-800">{{ product.name }}</h3>
              <p class="text-sm sm:text-base text-gray-600">{{ product.category }}</p>
              <p class="text-base sm:text-lg font-bold text-gray-900 mt-2">R$ {{ product.price }}</p>
            </div>
          </router-link>
          
          <!-- Botão Adicionar ao Carrinho -->
          <button 
            @click.prevent="adicionarAoCarrinho(product)"
            class="absolute top-2 right-2 sm:top-4 sm:right-4 bg-white p-2 sm:p-3 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 hover:bg-[#9810FA] hover:text-white"
            title="Adicionar ao Carrinho"
          >
            <i class="fas fa-shopping-cart text-sm sm:text-base"></i>
          </button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore'

interface Product {
  id: number;
  name: string;
  category: string;
  price: string;
  image: string;
}

const cartStore = useCartStore()

const products: Product[] = [
  {
    id: 1,
    name: 'Sabonete V1',
    category: 'Sabonete',
    price: '50,00',
    image: '/images/product1.jpg'
  },
  {
    id: 2,
    name: 'Sabonete V2',
    category: 'Sabonete',
    price: '55,00',
    image: '/images/product2.jpg'
  },
  {
    id: 3,
    name: 'Sabonete V3',
    category: 'Sabonete',
    price: '50,00',
    image: '/images/product3.jpg'
  }
]

const adicionarAoCarrinho = (product: Product) => {
  cartStore.addItem(product)
}
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
}

@media (max-width: 640px) {
  .container {
    padding: 0 1rem;
  }
}
</style> 