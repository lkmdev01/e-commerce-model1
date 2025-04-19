<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <Hero />

    <!-- Produtos em Destaque -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <ProductGrid 
        :search-term="searchTerm"
        :category="selectedCategory"
        :show-featured-only="true"
        @product-selected="handleProductSelect"
      />
    </section>

    <Testimonials />
    <CallToAction />
    <Features />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import Hero from '@/components/Hero.vue'
import ProductGrid from '@/components/ProductGrid.vue'
import Testimonials from '@/components/Testimonials.vue'
import CallToAction from '@/components/CallToAction.vue'
import Features from '@/components/Features.vue'
import { useProductStore } from '@/stores/productStore';
import type { Product } from '@/services/api';

const productStore = useProductStore();
const searchTerm = ref('');
const selectedCategory = ref('');

const handleProductSelect = (product: Product) => {
  // Implementar lógica de seleção de produto
  console.log('Produto selecionado:', product);
};

// Buscar produtos quando o componente é montado
onMounted(async () => {
  await productStore.fetchProducts();
});
</script>

<style scoped>
.home {
  min-height: 100vh;
}
</style> 