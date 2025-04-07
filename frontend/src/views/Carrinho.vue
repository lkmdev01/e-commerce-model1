<template>
  <div class="carrinho min-h-screen py-16">
    <div class="container mx-auto px-4">
      <h1 class="text-3xl font-bold text-gray-800 mb-8">Seu Carrinho</h1>
      
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
              
              <div class="flex justify-between">
                <span class="text-gray-600">Frete</span>
                <span class="font-semibold">R$ {{ formatPrice(frete) }}</span>
              </div>
              
              <div class="border-t border-gray-200 pt-4">
                <div class="flex justify-between">
                  <span class="text-lg font-bold text-gray-800">Total</span>
                  <span class="text-lg font-bold text-[#9810FA]">
                    R$ {{ formatPrice(cartStore.totalAmount + frete) }}
                  </span>
                </div>
              </div>
            </div>

            <button class="w-full bg-[#9810FA] text-white py-3 rounded-md mt-6 hover:bg-[#8609d8] transition-colors">
              Finalizar Compra
            </button>
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

const cartStore = useCartStore()
const frete = 10.00

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

.cart-content {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 2rem;
}

.cart-items {
  background: white;
  border-radius: 8px;
  padding: 1rem;
}

.cart-item {
  display: flex;
  align-items: center;
  padding: 1rem;
  border-bottom: 1px solid #eee;
}

.item-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 4px;
}

.item-info {
  flex: 1;
  padding: 0 1rem;
}

.item-price {
  color: #8B008B;
  font-weight: bold;
}

.item-quantity {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.item-quantity button {
  width: 30px;
  height: 30px;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  cursor: pointer;
}

.remove-btn {
  background: none;
  border: none;
  color: #ff4444;
  cursor: pointer;
  padding: 0.5rem;
}

.cart-summary {
  background: white;
  border-radius: 8px;
  padding: 1.5rem;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin: 1rem 0;
}

.total {
  font-weight: bold;
  font-size: 1.2rem;
  border-top: 1px solid #eee;
  padding-top: 1rem;
}

.checkout-btn {
  width: 100%;
  padding: 1rem;
  background: #8B008B;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.checkout-btn:hover {
  background: #6a006a;
}

.empty-cart {
  text-align: center;
  padding: 4rem 0;
}

.empty-cart i {
  font-size: 4rem;
  color: #ddd;
  margin-bottom: 1rem;
}

.continue-shopping {
  display: inline-block;
  margin-top: 1rem;
  padding: 0.8rem 1.5rem;
  background: #8B008B;
  color: white;
  text-decoration: none;
  border-radius: 4px;
  transition: background-color 0.3s ease;
}

.continue-shopping:hover {
  background: #6a006a;
}

@media (max-width: 768px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
}
</style> 