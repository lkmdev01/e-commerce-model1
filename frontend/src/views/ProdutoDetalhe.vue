<template>
  <div class="produto-detalhe">
    <section class="product-content bg-white py-32 pb-16">
      <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Imagem do Produto -->
            <div class="relative">
              <img 
                :src="produto?.image" 
                :alt="produto?.name"
                class="w-full h-[500px] object-cover rounded-lg shadow-lg"
              >
            </div>

            <!-- Informações do Produto -->
            <div class="space-y-6">
              <div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ produto?.name }}</h2>
                <p class="text-2xl font-bold text-[#9810FA]">R$ {{ produto?.price }}</p>
              </div>

              <div class="border-t border-b border-gray-200 py-6">
                <p class="text-gray-600 leading-relaxed">{{ produto?.description || 'Inspirational posters are a great way to be inspired and encouraged to take on new challenges and adventures. Hang up a poster at home or in the office to be reminded how much beauty awaits in the world, luring you out of your comfort zone and into a world where possibility resides.' }}</p>
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
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cartStore'

interface Produto {
  id: number | string;
  name: string;
  category: string;
  price: string;
  image: string;
  description?: string;
}

const route = useRoute()
const quantidade = ref(1)
const produto = ref<Produto | null>(null)
const cartStore = useCartStore()

onMounted(() => {
  // Aqui você faria uma chamada à API para buscar os detalhes do produto
  // usando o ID da rota (route.params.id)
  // Por enquanto vamos simular com dados estáticos
  produto.value = {
    id: String(route.params.id),
    name: 'Poster V1',
    category: 'Posters',
    price: '23.99',
    image: '/images/product1.jpg',
    description: 'Inspirational posters are a great way to be inspired and encouraged to take on new challenges and adventures.'
  }
})

const adicionarAoCarrinho = () => {
  if (!produto.value) return
  cartStore.addItem(produto.value, quantidade.value)
}
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