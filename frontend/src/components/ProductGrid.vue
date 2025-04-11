<template>
  <section class="py-8 md:py-16 bg-white">
    <div class="container mx-auto px-4">
      <!-- Loading Indicator -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#9810FA]"></div>
        <p class="ml-2 text-gray-600">Carregando produtos...</p>
      </div>
      
      <!-- Error Message -->
      <div v-else-if="error" class="text-center py-12">
        <p class="text-red-600">{{ error }}</p>
        <button @click="fetchProducts" class="mt-4 px-4 py-2 bg-[#9810FA] text-white rounded-md hover:bg-[#7a0dc8]">
          Tentar novamente
        </button>
      </div>
      
      <!-- Empty State -->
      <div v-else-if="filteredProducts.length === 0" class="text-center py-12">
        <p class="text-gray-600">Nenhum produto encontrado com os filtros atuais.</p>
      </div>
      
      <!-- Products Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 md:gap-8">
        <div v-for="product in filteredProducts" :key="product.id" class="group relative">
          <!-- Link para a página de detalhes -->
          <router-link :to="{ name: 'produto-detalhe', params: { id: product.id }}">
            <div class="relative overflow-hidden mb-4">
              <img 
                :src="product.image_url || '/images/product-placeholder.jpg'" 
                :alt="product.name"
                class="w-full h-64 sm:h-72 md:h-96 object-cover transform group-hover:scale-105 transition-transform duration-300"
              >
              <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors duration-300"></div>
            </div>
            <div class="text-center px-2">
              <h3 class="text-base sm:text-lg font-semibold text-gray-800">{{ product.name }}</h3>
              <p class="text-sm sm:text-base text-gray-600">{{ getCategoryName(product.category) }}</p>
              <p class="text-base sm:text-lg font-bold text-gray-900 mt-2">R$ {{ formatPrice(product.price) }}</p>
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
import { ref, onMounted, computed, watch } from 'vue'
import { useCartStore } from '@/stores/cartStore'
import { productService, type Product } from '@/services/api'

// Props para filtros
const props = defineProps({
  searchTerm: {
    type: String,
    default: ''
  },
  category: {
    type: String,
    default: ''
  }
})

const cartStore = useCartStore()
const products = ref<Product[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

// Função para obter o nome da categoria, seja de um objeto ou string
const getCategoryName = (category: any): string => {
  if (typeof category === 'object' && category !== null) {
    return category.name;
  }
  return category;
};

// Produtos filtrados com base nas props
const filteredProducts = computed(() => {
  let result = [...products.value];
  
  // Filtrar por termo de busca
  if (props.searchTerm) {
    const searchTerm = props.searchTerm.toLowerCase();
    result = result.filter(product => 
      product.name.toLowerCase().includes(searchTerm) ||
      (product.description && product.description.toLowerCase().includes(searchTerm))
    );
  }
  
  // Filtrar por categoria
  if (props.category) {
    result = result.filter(product => {
      const categoryName = getCategoryName(product.category);
      return categoryName.toLowerCase() === props.category.toLowerCase();
    });
  }
  
  return result;
});

// Buscar produtos da API
const fetchProducts = async () => {
  try {
    loading.value = true
    error.value = null
    // Buscar apenas produtos ativos
    const data = await productService.getAll()
    products.value = data.filter(product => product.active)
  } catch (err) {
    console.error('Erro ao buscar produtos:', err)
    error.value = 'Não foi possível carregar os produtos. Tente novamente.'
  } finally {
    loading.value = false
  }
}

// Formatar preço para exibição
const formatPrice = (price: number): string => {
  return price.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  })
}

const adicionarAoCarrinho = (product: Product) => {
  cartStore.addItem({
    id: product.id,
    name: product.name,
    price: product.price,
    image: product.image_url || '/images/product-placeholder.jpg'
  })
}

// Observar mudanças nos filtros para atualizar produtos
watch(() => [props.searchTerm, props.category], () => {
  // Se precisar fazer alguma ação quando os filtros mudarem
  // Por exemplo, resetar paginação, etc.
}, { deep: true })

onMounted(fetchProducts)
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