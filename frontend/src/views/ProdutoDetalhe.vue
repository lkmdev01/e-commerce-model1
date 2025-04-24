<template>
  <div class="produto-detalhe">
    <section class="product-content bg-white py-32 pb-16">
      <div class="container mx-auto px-4">
        <!-- Loading -->
        <div v-if="loading" class="flex justify-center items-center py-12">
          <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#9810FA]"></div>
          <p class="ml-2 text-gray-600">Carregando produto...</p>
        </div>
        
        <!-- Error Message -->
        <div v-else-if="error" class="text-center py-12">
          <p class="text-red-600">{{ error }}</p>
          <button @click="fetchProduto" class="mt-4 px-4 py-2 bg-[#9810FA] text-white rounded-md hover:bg-[#7a0dc8]">
            Tentar novamente
          </button>
          <div class="mt-4">
            <router-link to="/" class="text-[#9810FA] hover:underline">Voltar para a página inicial</router-link>
          </div>
        </div>
        
        <!-- Produto não encontrado -->
        <div v-else-if="!produto" class="text-center py-12">
          <p class="text-gray-600">Produto não encontrado.</p>
          <div class="mt-4">
            <router-link to="/" class="text-[#9810FA] hover:underline">Voltar para a página inicial</router-link>
          </div>
        </div>
        
        <!-- Produto -->
        <div v-else class="max-w-6xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Imagem do Produto -->
            <div class="relative">
              <img 
                :src="produto.image_url || '/images/product-placeholder.jpg'" 
                :alt="produto.name"
                class="w-full h-[500px] object-cover rounded-lg shadow-lg"
              >
            </div>

            <!-- Informações do Produto -->
            <div class="space-y-6">
              <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ produto.name }}</h2>
                <p class="text-2xl font-bold text-[#9810FA]">R$ {{ formatPrice(produto.price) }}</p>
              </div>

              <div class="border-t border-b border-gray-200 py-6">
                <p class="text-gray-600 leading-relaxed">{{ produto.description }}</p>
              </div>

              <div class="flex items-center gap-4">
                <div class="flex items-center border border-gray-300 rounded-md">
                  <button 
                    @click="quantidade > 1 ? quantidade-- : null"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100"
                  >
                    -
                  </button>
                  <span class="px-4 py-2 border-x border-gray-300">{{ quantidade }}</span>
                  <button 
                    @click="quantidade++"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100"
                  >
                    +
                  </button>
                </div>

                <button 
                  @click="adicionarAoCarrinho"
                  class="flex-1 bg-[#9810FA] text-white py-3 px-6 rounded-md hover:bg-[#8609d8] transition-colors duration-300"
                >
                  Adicionar ao Carrinho
                </button>
              </div>

              <div class="space-y-4 pt-6">
                <div class="flex items-center gap-2">
                  <i class="fas fa-truck text-[#9810FA]"></i>
                  <span class="text-gray-600">Entrega em todo Brasil</span>
                </div>
                <div class="flex items-center gap-2">
                  <i class="fas fa-lock text-[#9810FA]"></i>
                  <span class="text-gray-600">Pagamento 100% seguro</span>
                </div>
              </div>

              <!-- Calculadora de Frete -->
              <div class="pt-6 pb-4">
                <ShippingCalculator 
                  :items="[produtoParaCalculo]" 
                  @shipping-selected="freteSelectionado"
                />
                
                <!-- Exibir frete selecionado -->
                <div v-if="freteSelecionado" class="mt-6 p-4 bg-green-50 border border-green-200 rounded-md">
                  <p class="font-semibold text-green-800">
                    Frete selecionado: {{ freteSelecionado.service }} - 
                    R$ {{ formatPrice(freteSelecionado.price) }} - 
                    {{ freteSelecionado.days }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'
import { productService, type Product } from '@/services/api'
import ShippingCalculator from '@/components/ShippingCalculator.vue'

const route = useRoute()
const quantidade = ref(1)
const produto = ref<Product | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)
const cartStore = useCartStore()
const freteSelecionado = ref<any>(null)

// Produto formatado para o componente de frete
const produtoParaCalculo = computed(() => {
  if (!produto.value) return null
  
  return {
    width: 20, // Largura em cm (exemplo)
    height: 15, // Altura em cm (exemplo)
    length: 10, // Comprimento em cm (exemplo)
    weight: 0.5, // Peso em kg (exemplo)
    quantity: quantidade.value, // Quantidade selecionada
  }
})

// Buscar detalhes do produto da API
const fetchProduto = async () => {
  try {
    const productId = Number(route.params.id)
    if (isNaN(productId)) {
      error.value = 'ID do produto inválido'
      return
    }
    
    loading.value = true
    error.value = null
    produto.value = await productService.getById(productId)
    
    // Verificar se o produto está ativo
    if (produto.value && !produto.value.active) {
      error.value = 'Este produto não está disponível no momento.'
      produto.value = null
    }
  } catch (err) {
    console.error('Erro ao buscar produto:', err)
    error.value = 'Não foi possível carregar os detalhes do produto.'
    produto.value = null
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

// Adicionar ao Carrinho
const adicionarAoCarrinho = () => {
  if (produto.value) {
    cartStore.addItem({
      id: produto.value.id,
      name: produto.value.name,
      price: produto.value.price,
      image: produto.value.image_url || '/images/product-placeholder.jpg'
    }, quantidade.value)
  }
}

// Quando um frete é selecionado
const freteSelectionado = (opcao: any) => {
  freteSelecionado.value = opcao
}

onMounted(fetchProduto)
</script>

<style scoped>
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
</style> 