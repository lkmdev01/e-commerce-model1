<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Cabeçalho -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Acompanhamento de Pedidos</h1>
        <router-link to="/dashboard/profile" class="text-[#9810FA] hover:text-[#8609d8]">
          <i class="fas fa-arrow-left mr-2"></i> Voltar ao Perfil
        </router-link>
      </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="flex flex-wrap gap-4 justify-between items-center">
        <div class="flex space-x-4">
          <button 
            @click="activeFilter = 'all'" 
            :class="[
              'px-4 py-2 rounded-md',
              activeFilter === 'all' 
                ? 'bg-[#9810FA] text-white' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Todos
          </button>
          <button 
            @click="activeFilter = 'pending'" 
            :class="[
              'px-4 py-2 rounded-md',
              activeFilter === 'pending' 
                ? 'bg-[#9810FA] text-white' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Pendentes
          </button>
          <button 
            @click="activeFilter = 'processing'" 
            :class="[
              'px-4 py-2 rounded-md',
              activeFilter === 'processing' 
                ? 'bg-[#9810FA] text-white' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Em Processamento
          </button>
          <button 
            @click="activeFilter = 'completed'" 
            :class="[
              'px-4 py-2 rounded-md',
              activeFilter === 'completed' 
                ? 'bg-[#9810FA] text-white' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            Concluídos
          </button>
        </div>
        
        <div class="relative">
          <input 
            type="text" 
            v-model="searchQuery"
            placeholder="Buscar pedido..." 
            class="pl-10 pr-4 py-2 border border-gray-300 rounded-md"
          />
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-lg shadow-md p-6 text-center">
      <div class="flex justify-center mb-4">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-[#9810FA]"></div>
      </div>
      <p class="text-gray-600">Carregando seus pedidos...</p>
    </div>

    <!-- Lista de Pedidos -->
    <div v-else-if="filteredOrders.length > 0" class="space-y-6">
      <div v-for="order in filteredOrders" :key="order.id" class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Cabeçalho do Pedido -->
        <div class="bg-gray-50 p-4 border-b border-gray-300">
          <div class="flex flex-wrap justify-between items-center">
            <div>
              <span class="text-lg font-semibold">Pedido #{{ order.id }}</span>
              <span class="ml-4 text-sm text-gray-500">{{ new Date(order.created_at).toLocaleDateString() }}</span>
            </div>
            <div class="flex items-center space-x-4">
              <span :class="getStatusClass(order.status)" class="px-3 py-1 rounded-full text-xs font-medium">
                {{ getStatusText(order.status) }}
              </span>
              <span class="font-semibold">R$ {{ order.total.toFixed(2) }}</span>
            </div>
          </div>
        </div>
        
        <!-- Detalhe do Pedido -->
        <div class="p-4">
          <!-- Produtos do Pedido -->
          <div class="divide-y divide-gray-300">
            <div class="py-4 flex flex-wrap items-center gap-4">
              <div class="w-20 h-20 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                <img src="/images/product1.jpg" alt="Produto" class="w-full h-full object-cover" />
              </div>
              <div class="flex-grow">
                <h3 class="font-semibold">Produto de Exemplo 1</h3>
                <p class="text-sm text-gray-500">Categoria: Eletrônicos</p>
                <div class="flex items-center mt-1">
                  <span class="text-sm text-gray-700">Quantidade: 1</span>
                  <span class="mx-2 text-gray-400">|</span>
                  <span class="text-sm text-gray-700">R$ 199.90</span>
                </div>
              </div>
              <div class="flex-shrink-0">
                <button class="text-[#9810FA] hover:text-[#8609d8] text-sm">Ver Produto</button>
              </div>
            </div>
            
            <div v-if="order.id === 1001" class="py-4 flex flex-wrap items-center gap-4">
              <div class="w-20 h-20 bg-gray-200 rounded-md overflow-hidden flex-shrink-0">
                <img src="/images/product2.jpg" alt="Produto" class="w-full h-full object-cover" />
              </div>
              <div class="flex-grow">
                <h3 class="font-semibold">Produto de Exemplo 2</h3>
                <p class="text-sm text-gray-500">Categoria: Moda</p>
                <div class="flex items-center mt-1">
                  <span class="text-sm text-gray-700">Quantidade: 1</span>
                  <span class="mx-2 text-gray-400">|</span>
                  <span class="text-sm text-gray-700">R$ 89.90</span>
                </div>
              </div>
              <div class="flex-shrink-0">
                <button class="text-[#9810FA] hover:text-[#8609d8] text-sm">Ver Produto</button>
              </div>
            </div>
          </div>
          
          <!-- Barra de Progresso do Pedido -->
          <div class="mt-6">
            <div class="relative">
              <div class="flex justify-between mb-2">
                <div class="text-center w-1/4">
                  <div :class="[
                    'w-6 h-6 rounded-full mx-auto mb-1 flex items-center justify-center',
                    isStepComplete(order.status, 'pending') ? 'bg-[#9810FA] text-white' : 'bg-gray-200'
                  ]">
                    <i class="fas fa-check text-xs" v-if="isStepComplete(order.status, 'pending')"></i>
                    <span v-else class="text-gray-500 text-xs">1</span>
                  </div>
                  <span class="text-xs">Pedido Recebido</span>
                </div>
                <div class="text-center w-1/4">
                  <div :class="[
                    'w-6 h-6 rounded-full mx-auto mb-1 flex items-center justify-center',
                    isStepComplete(order.status, 'processing') ? 'bg-[#9810FA] text-white' : 'bg-gray-200'
                  ]">
                    <i class="fas fa-check text-xs" v-if="isStepComplete(order.status, 'processing')"></i>
                    <span v-else class="text-gray-500 text-xs">2</span>
                  </div>
                  <span class="text-xs">Em Processamento</span>
                </div>
                <div class="text-center w-1/4">
                  <div :class="[
                    'w-6 h-6 rounded-full mx-auto mb-1 flex items-center justify-center',
                    isStepComplete(order.status, 'shipping') ? 'bg-[#9810FA] text-white' : 'bg-gray-200'
                  ]">
                    <i class="fas fa-check text-xs" v-if="isStepComplete(order.status, 'shipping')"></i>
                    <span v-else class="text-gray-500 text-xs">3</span>
                  </div>
                  <span class="text-xs">Enviado</span>
                </div>
                <div class="text-center w-1/4">
                  <div :class="[
                    'w-6 h-6 rounded-full mx-auto mb-1 flex items-center justify-center',
                    isStepComplete(order.status, 'completed') ? 'bg-[#9810FA] text-white' : 'bg-gray-200'
                  ]">
                    <i class="fas fa-check text-xs" v-if="isStepComplete(order.status, 'completed')"></i>
                    <span v-else class="text-gray-500 text-xs">4</span>
                  </div>
                  <span class="text-xs">Entregue</span>
                </div>
              </div>
              
              <!-- Barra de Progresso -->
              <div class="h-1 bg-gray-200 rounded-full">
                <div 
                  class="h-1 bg-[#9810FA] rounded-full" 
                  :style="{ width: getProgressWidth(order.status) }"
                ></div>
              </div>
            </div>
          </div>
          
          <!-- Footer do Pedido -->
          <div class="mt-6 flex flex-wrap justify-between items-center">
            <div>
              <button class="text-[#9810FA] hover:underline">
                <i class="fas fa-envelope mr-1"></i> Ajuda com este pedido
              </button>
            </div>
            <div>
              <button 
                v-if="order.status === 'pending'" 
                class="px-4 py-2 border border-red-500 text-red-500 rounded-md hover:bg-red-50"
              >
                Cancelar Pedido
              </button>
              <button 
                v-if="order.status === 'completed'" 
                class="px-4 py-2 bg-[#9810FA] text-white rounded-md hover:bg-[#8609d8]"
              >
                Comprar Novamente
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Estado Vazio -->
    <div v-else class="bg-white rounded-lg shadow-md p-8 text-center">
      <div class="mb-4 text-gray-400">
        <i class="fas fa-shopping-bag text-5xl"></i>
      </div>
      <h3 class="text-xl font-semibold mb-2">Nenhum pedido encontrado</h3>
      <p class="text-gray-500 mb-6">Parece que você ainda não fez nenhum pedido ou nenhum pedido corresponde aos filtros selecionados.</p>
      <router-link to="/comprar" class="px-6 py-3 bg-[#9810FA] text-white rounded-md hover:bg-[#8609d8] inline-block">
        Ir às Compras
      </router-link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useApi } from '@/composables/useApi'

interface Order {
  id: number
  created_at: string
  status: 'pending' | 'processing' | 'shipping' | 'completed' | 'cancelled'
  total: number
}

const api = useApi()
const loading = ref(true)
const orders = ref<Order[]>([])
const activeFilter = ref('all')
const searchQuery = ref('')

const getStatusClass = (status: Order['status']) => {
  const classes: Record<string, string> = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'shipping': 'bg-purple-100 text-purple-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status]
}

const getStatusText = (status: Order['status']) => {
  const texts: Record<string, string> = {
    'pending': 'Pendente',
    'processing': 'Em Processamento',
    'shipping': 'Em Transporte',
    'completed': 'Entregue',
    'cancelled': 'Cancelado'
  }
  return texts[status]
}

// Verifica se um passo do pedido está completo
const isStepComplete = (orderStatus: Order['status'], step: string) => {
  const statusOrder = ['pending', 'processing', 'shipping', 'completed']
  const orderIndex = statusOrder.indexOf(orderStatus)
  const stepIndex = statusOrder.indexOf(step)
  
  if (orderStatus === 'cancelled') return false
  if (stepIndex === -1) return false
  
  return orderIndex >= stepIndex
}

// Determina a largura da barra de progresso com base no status
const getProgressWidth = (status: Order['status']) => {
  const progressMap: Record<string, string> = {
    'pending': '25%',
    'processing': '50%',
    'shipping': '75%',
    'completed': '100%',
    'cancelled': '0%'
  }
  return progressMap[status]
}

// Filtra os pedidos com base no filtro ativo e na busca
const filteredOrders = computed(() => {
  let result = orders.value

  // Aplicar filtro de status
  if (activeFilter.value !== 'all') {
    result = result.filter(order => order.status === activeFilter.value)
  }

  // Aplicar busca por termo
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(order => 
      order.id.toString().includes(query) ||
      getStatusText(order.status).toLowerCase().includes(query)
    )
  }

  return result
})

onMounted(async () => {
  try {
    const response = await api.get('/orders')
    orders.value = response.data
    
    // Adicionar status extra para demonstração
    if (orders.value.length > 0) {
      orders.value.push({
        id: 1004,
        created_at: new Date(Date.now() - 5 * 86400000).toISOString(), // 5 dias atrás
        status: 'shipping',
        total: 349.90
      })
    }
  } catch (error) {
    console.error('Erro ao carregar pedidos:', error)
    // Dados de fallback em caso de erro
    orders.value = [
      {
        id: 1001,
        created_at: new Date().toISOString(),
        status: 'pending',
        total: 199.90
      },
      {
        id: 1002,
        created_at: new Date(Date.now() - 86400000).toISOString(), // Ontem
        status: 'completed',
        total: 149.50
      },
      {
        id: 1003,
        created_at: new Date(Date.now() - 3 * 86400000).toISOString(), // 3 dias atrás
        status: 'processing',
        total: 299.90
      },
      {
        id: 1004,
        created_at: new Date(Date.now() - 5 * 86400000).toISOString(), // 5 dias atrás
        status: 'shipping',
        total: 349.90
      }
    ]
  } finally {
    loading.value = false
  }
})
</script>

<style scoped>
/* Animação de loading */
@keyframes spin {
  to { transform: rotate(360deg); }
}
.animate-spin {
  animation: spin 1s linear infinite;
}
</style> 