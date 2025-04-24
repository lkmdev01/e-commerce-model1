<template>
  <div class="carrinho min-h-screen py-16">
    <div class="container mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-800 mt-10 mb-8">Seu Carrinho</h1>
      
      <!-- Banner para login quando não estiver autenticado -->
      <div v-if="cartStore.items.length > 0 && !authStore.isAuthenticated" class="bg-blue-50 p-4 rounded-lg shadow-sm mb-8 border border-blue-100">
        <div class="flex flex-col md:flex-row md:items-center justify-between">
          <div>
            <h3 class="text-lg font-semibold text-blue-800 mb-1">Faça login para salvar seu carrinho</h3>
            <p class="text-blue-600 mb-4 md:mb-0">Seu carrinho será salvo automaticamente e você poderá acessá-lo em qualquer dispositivo.</p>
          </div>
          <div class="flex space-x-4">
            <router-link to="/login" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
              Login
            </router-link>
            <router-link to="/register" class="border border-blue-600 text-blue-600 px-4 py-2 rounded-md hover:bg-blue-50 transition-colors">
              Criar Conta
            </router-link>
          </div>
        </div>
      </div>
      
      <div v-if="cartStore.items.length > 0" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Lista de Produtos -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-sm">
            <div v-for="item in cartStore.items" :key="item.id" class="flex items-center p-4 border-b border-gray-100 last:border-b-0">
              <div class="w-16 h-16">
                <img 
                  :src="item.image" 
                  :alt="item.name" 
                  class="w-full h-full object-cover rounded-md"
                >
              </div>
              
              <div class="flex-1 ml-4">
                <h3 class="text-lg font-semibold text-gray-800">{{ item.name }}</h3>
                <p class="text-[#9810FA] font-bold">R$ {{ formatPrice(item.price) }}</p>
              </div>

              <div class="flex items-center gap-4">
                <div class="flex items-center border border-gray-200 rounded-md">
                  <button 
                    @click="cartStore.updateQuantity(item.id, item.quantity - 1)"
                    class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                  >
                    -
                  </button>
                  <span class="px-3 py-1 border-x border-gray-200">{{ item.quantity }}</span>
                  <button 
                    @click="cartStore.updateQuantity(item.id, item.quantity + 1)"
                    class="px-3 py-1 text-gray-600 hover:bg-gray-100"
                  >
                    +
                  </button>
                </div>

                <button 
                  @click="cartStore.removeItem(item.id)"
                  class="text-red-500 hover:text-red-600"
                  title="Remover item"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Resumo do Pedido -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6">Resumo do Pedido</h2>
            
            <div class="space-y-4">
              <div class="flex justify-between">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-semibold">R$ {{ formatPrice(cartStore.totalAmount) }}</span>
              </div>
              
              <!-- Calculadora de Frete -->
              <div class="mt-4 mb-4">
                <h3 class="text-md font-semibold text-gray-700 mb-2">Calcular Frete</h3>
                <ShippingCalculator 
                  :items="cartItemsForShipping" 
                  @shipping-selected="handleShippingSelected"
                  :useCartStore="true"
                  class="shipping-cart-style"
                />
              </div>
              
              <div class="flex justify-between">
                <span class="text-gray-600">Frete</span>
                <span v-if="!cartStore.selectedShipping" class="font-semibold">Calcule acima</span>
                <span v-else class="font-semibold">R$ {{ formatPrice(cartStore.selectedShipping.price) }}</span>
              </div>
              
              <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between">
                  <span class="text-lg font-bold text-gray-800">Total</span>
                  <span class="text-lg font-bold text-[#9810FA]">
                    R$ {{ formatPrice(cartStore.cartTotal) }}
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Endereço de Entrega -->
            <div v-if="cartStore.shippingAddress" class="mt-6 border-t pt-4">
              <h3 class="text-md font-semibold text-gray-700 mb-2">Endereço de Entrega</h3>
              <div class="bg-gray-50 p-3 rounded-md text-sm">
                <p>{{ cartStore.shippingAddress.logradouro }}</p>
                <p>{{ cartStore.shippingAddress.bairro }}</p>
                <p>{{ cartStore.shippingAddress.localidade }} - {{ cartStore.shippingAddress.uf }}</p>
                <p>CEP: {{ cartStore.shippingAddress.cep }}</p>
              </div>
            </div>

            <button 
              class="w-full bg-[#9810FA] text-white py-3 rounded-md mt-6 hover:bg-[#8609d8] transition-colors" 
              :disabled="!cartStore.selectedShipping"
              @click="finalizarCompra"
            >
              Finalizar Compra
            </button>
            
            <p v-if="!cartStore.selectedShipping" class="text-center text-sm text-gray-600 mt-2">
              Selecione uma opção de frete para continuar
            </p>
          </div>
        </div>
      </div>

      <!-- Carrinho Vazio -->
      <div v-else class="text-center py-16">
        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Seu carrinho está vazio</h2>
        <p class="text-gray-600 mb-8">Adicione alguns produtos para começar suas compras!</p>
        <router-link 
          to="/comprar" 
          class="inline-block bg-[#9810FA] text-white px-6 py-3 rounded-md hover:bg-[#8609d8] transition-colors"
        >
          Continuar Comprando
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useCartStore } from '@/stores/cartStore'
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import ShippingCalculator from '@/components/ShippingCalculator.vue'

const cartStore = useCartStore()
const authStore = useAuthStore()
const router = useRouter()

// Formatar os itens do carrinho para o cálculo de frete
const cartItemsForShipping = computed(() => {
  return cartStore.items.map(item => ({
    width: 20, // Valores padrão ou obter de algum lugar se disponível
    height: 15,
    length: 10,
    weight: 0.5,
    quantity: item.quantity
  }))
})

// Quando um frete é selecionado
const handleShippingSelected = (option: {service: string, price: number, days: string, code: string}) => {
  cartStore.setShipping(option)
}

// Função para finalizar a compra
const finalizarCompra = async () => {
  if (!cartStore.selectedShipping) {
    return
  }
  
  // Verifica se o usuário está autenticado
  if (!authStore.isAuthenticated) {
    // Salva a URL atual para redirecionamento após o login
    localStorage.setItem('redirectAfterLogin', '/checkout')
    
    // Redireciona para a página de login
    router.push('/login')
    return
  }
  
  // Se estiver autenticado, sincroniza o carrinho e prossegue para o checkout
  try {
    await cartStore.saveToBackend()
    router.push('/checkout')
  } catch (error) {
    console.error('Erro ao finalizar compra:', error)
  }
}

const formatPrice = (price: string | number) => {
  const numPrice = typeof price === 'string' ? parseFloat(price.replace(',', '.')) : price
  return numPrice.toFixed(2).replace('.', ',')
}
</script>

<style scoped>
.carrinho {
  min-height: 100vh;
  padding: 4rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

h1 {
  font-size: 2.5rem;
  color: #333;
  margin-bottom: 2rem;
}

.shipping-cart-style {
  padding: 0;
  border: none;
  background: transparent;
}

.shipping-cart-style :deep(.border) {
  border-color: #e5e7eb;
}

.shipping-cart-style :deep(h3) {
  display: none;
}

/* Removendo as cores roxas dos botões no carrinho */
.shipping-cart-style :deep(button) {
  /* Não definimos cor de fundo aqui para usar o padrão do componente */
}

.shipping-cart-style :deep(button:hover) {
  /* Não definimos cor de hover aqui para usar o padrão do componente */
}

/* Adicionando cor roxa apenas para botões específicos */
.shipping-cart-style :deep(button.rounded-r),
.shipping-cart-style :deep(.w-full button),
.shipping-cart-style :deep(button.mt-1),
.shipping-cart-style :deep(button.w-full) {
  background-color: #9810FA !important;
  color: white !important;
}

.shipping-cart-style :deep(button.rounded-r:hover),
.shipping-cart-style :deep(.w-full button:hover),
.shipping-cart-style :deep(button.mt-1:hover),
.shipping-cart-style :deep(button.w-full:hover) {
  background-color: #8609d8 !important;
}
</style> 