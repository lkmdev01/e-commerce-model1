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
        
        <!-- Produtos em Destaque (caso tenha) -->
        <div v-if="featuredProducts.length > 0" class="mb-16">
          <h2 class="text-2xl font-bold text-center mb-8">Produtos em Destaque</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-8">
            <div v-for="product in featuredProducts" :key="product.id" class="group relative">
              <router-link :to="{ name: 'produto-detalhe', params: { id: product.id }}">
                <div class="relative overflow-hidden mb-4">
                  <img 
                    :src="product.image_url || '/images/product-placeholder.jpg'" 
                    :alt="product.name"
                    class="w-full h-64 object-cover transform group-hover:scale-105 transition-transform duration-300"
                  >
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
                  <div class="absolute top-2 right-2 bg-[#9810FA] text-white px-2 py-1 text-xs font-medium rounded">
                    Destaque
                  </div>
                </div>
                <div class="text-center px-2">
                  <h3 class="text-base font-semibold text-gray-800">{{ product.name }}</h3>
                  <p class="text-sm text-gray-600">{{ getCategoryName(product.category) }}</p>
                  <p class="text-base font-bold text-gray-900 mt-2">
                    R$ {{ product.price.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
                  </p>
                </div>
              </router-link>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-bold text-center mb-8">Todos os Produtos</h2>
        
        <div class="filters mb-12">
          <div class="search mb-8">
            <input 
              type="text" 
              placeholder="Buscar produtos..." 
              v-model="searchTerm"
              class="w-full max-w-xl mx-auto block px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>
          
          <!-- Loading de categorias -->
          <div v-if="loading" class="text-center mb-4">
            <div class="inline-block animate-spin rounded-full h-5 w-5 border-b-2 border-[#9810FA]"></div>
            <span class="ml-2 text-gray-600">Carregando categorias...</span>
          </div>
          
          <!-- Categorias -->
          <div class="categories flex flex-wrap justify-center gap-4">
            <button 
              v-for="category in categories" 
              :key="category"
              :class="[
                'px-6 py-2 rounded-full transition-colors flex items-center',
                selectedCategory === category 
                  ? 'bg-[#9810FA] text-white' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
              @click="selectedCategory = category"
            >
              {{ category }}
              <span 
                v-if="category !== 'Todos' && categoryCounts[category]" 
                :class="[
                  'ml-2 text-xs font-medium rounded-full px-2 py-0.5',
                  selectedCategory === category 
                    ? 'bg-white text-[#9810FA]' 
                    : 'bg-gray-200 text-gray-600'
                ]"
              >
                {{ categoryCounts[category] }}
              </span>
            </button>
          </div>
        </div>
        
        <ProductGrid 
          :searchTerm="searchTerm" 
          :category="selectedCategory !== 'Todos' ? selectedCategory : ''" 
        />
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import ProductGrid from '../components/ProductGrid.vue'
import { categoryService, productService, type Product } from '@/services/api'

const searchTerm = ref('')
const selectedCategory = ref('Todos')
const categories = ref<string[]>(['Todos'])
const loading = ref(false)
const allProducts = ref<Product[]>([])
const categoryCounts = ref<Record<string, number>>({})

// Produtos em destaque
const featuredProducts = computed(() => {
  return allProducts.value
    .filter(product => product.featured && product.active)
    .slice(0, 3); // Exibir no máximo 3 produtos em destaque
});

// Função para obter o nome da categoria, seja de um objeto ou string
const getCategoryName = (category: any): string => {
  if (typeof category === 'object' && category !== null) {
    return category.name;
  }
  return category;
};

// Carregar produtos e calcular contagem por categoria
const fetchProducts = async () => {
  try {
    const products = await productService.getAll();
    allProducts.value = products.filter(p => p.active);
    
    // Calcular contagem por categoria
    const counts: Record<string, number> = {};
    const uniqueCategories: string[] = [];
    
    // Extrair categorias dos produtos
    for (const product of allProducts.value) {
      let categoryName = getCategoryName(product.category);
      
      if (categoryName) {
        // Adicionar à contagem
        counts[categoryName] = (counts[categoryName] || 0) + 1;
        
        // Adicionar à lista de categorias se ainda não estiver lá
        if (!uniqueCategories.includes(categoryName)) {
          uniqueCategories.push(categoryName);
        }
      }
    }
    
    // Atualizar contagem e categorias
    categoryCounts.value = counts;
    
    // Atualizar lista de categorias (ordenada alfabeticamente)
    uniqueCategories.sort((a, b) => a.localeCompare(b));
    categories.value = ['Todos', ...uniqueCategories];
    
    // Gerar produtos em destaque simulados se não houver produtos reais
    if (allProducts.value.length === 0) {
      // Se não tiver produtos, não exibe categorias
      categories.value = ['Todos'];
      allProducts.value = [];
    }
  } catch (err) {
    console.error('Erro ao buscar produtos:', err);
    // Em caso de erro, mostrar apenas "Todos"
    categories.value = ['Todos'];
    allProducts.value = [];
  }
};

// Carregar categorias do backend (não usado mais, agora extraímos direto dos produtos)
const fetchCategories = async () => {
  try {
    loading.value = true;
    
    // Carregar produtos para obter categorias
    await fetchProducts();
  } catch (err) {
    console.error('Erro ao carregar dados:', err);
    categories.value = ['Todos'];
  } finally {
    loading.value = false;
  }
}

onMounted(fetchCategories)
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