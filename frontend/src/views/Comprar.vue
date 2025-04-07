<template>
  <div class="comprar">
    <section class="hero-section relative">
      <div class="absolute inset-0">
        <img 
          src="/images/bg-home.jpg"
          alt="Background"
          class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-white/30"></div>
      </div>
      <div class="relative z-10 container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-[#9810FA] mb-4">Nossa Loja</h1>
        <p class="text-xl text-gray-700">Descubra nossa coleção de produtos naturais</p>
      </div>
    </section>
    
    <section class="products-section">
      <div class="container mx-auto px-4 py-16">
        <div class="filters mb-12">
          <div class="search mb-8">
            <input 
              type="text" 
              placeholder="Buscar produtos..." 
              v-model="searchTerm"
              class="w-full max-w-xl mx-auto block px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>
          <div class="categories flex flex-wrap justify-center gap-4">
            <button 
              v-for="category in categories" 
              :key="category"
              :class="[
                'px-6 py-2 rounded-full transition-colors',
                selectedCategory === category 
                  ? 'bg-[#9810FA] text-white' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
              @click="selectedCategory = category"
            >
              {{ category }}
            </button>
          </div>
        </div>
        
        <ProductGrid />
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import ProductGrid from '../components/ProductGrid.vue'

const searchTerm = ref('')
const selectedCategory = ref('Todos')
const categories = ['Todos', 'Sabonetes', 'Óleos', 'Aromatizantes']
</script>

<style scoped>
.comprar {
  min-height: 100vh;
}

.hero-section {
  height: 50vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.container {
  max-width: 1200px;
}

.products-section {
  background-color: white;
}
</style> 