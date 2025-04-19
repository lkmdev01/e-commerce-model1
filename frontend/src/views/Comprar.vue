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
import { ref, onMounted } from 'vue'
import ProductGrid from '../components/ProductGrid.vue'
import { productService, type Product } from '@/services/api'
import { useProductStore } from '@/stores/productStore'

const searchTerm = ref('')
const selectedCategory = ref('Todos')
const categories = ref<string[]>(['Todos'])
const loading = ref(false)
const allProducts = ref<Product[]>([])
const categoryCounts = ref<Record<string, number>>({})
const productStore = useProductStore()

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
    // Usar produtos do productStore se já estiverem carregados
    const products = productStore.products.length > 0 
                    ? productStore.products 
                    : await productService.getAll();
    
    // Se usamos a API diretamente, atualize também o store
    if (productStore.products.length === 0) {
      productStore.products = products;
    }
    
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
    
    if (allProducts.value.length === 0) {
      // Se não tiver produtos, não exibe categorias
      categories.value = ['Todos'];
    }
  } catch (err) {
    console.error('Erro ao buscar produtos:', err);
    // Em caso de erro, mostrar apenas "Todos"
    categories.value = ['Todos'];
    allProducts.value = [];
  }
};

// Carregar categorias do backend
const fetchCategories = async () => {
  try {
    loading.value = true;
    
    // Verificar se o productStore já tem produtos
    if (productStore.products.length === 0) {
      // Carregar produtos via store primeiro
      await productStore.fetchProducts();
    }
    
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