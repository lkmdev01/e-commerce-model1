<template>
  <div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Pedidos</h1>
      <button 
        @click="exportOrders"
        class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8] transition-colors"
      >
        <i class="fas fa-file-export mr-2"></i>
        Exportar Pedidos
      </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-4 rounded-lg shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
          <input 
            type="text" 
            v-model="filters.search"
            placeholder="ID ou Cliente"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select 
            v-model="filters.status"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          >
            <option value="">Todos</option>
            <option value="pending">Pendente</option>
            <option value="processing">Processando</option>
            <option value="shipped">Enviado</option>
            <option value="delivered">Entregue</option>
            <option value="cancelled">Cancelado</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Data Inicial</label>
          <input 
            type="date" 
            v-model="filters.startDate"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          >
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Data Final</label>
          <input 
            type="date" 
            v-model="filters.endDate"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          >
        </div>
      </div>
    </div>

    <!-- Lista de Pedidos -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="order in orders" :key="order.id">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ order.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ order.customer.name }}</div>
              <div class="text-sm text-gray-500">{{ order.customer.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ new Date(order.date).toLocaleDateString() }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              R$ {{ order.total.toFixed(2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span 
                :class="{
                  'px-2 py-1 text-xs font-semibold rounded-full': true,
                  'bg-yellow-100 text-yellow-800': order.status === 'pending',
                  'bg-blue-100 text-blue-800': order.status === 'processing',
                  'bg-green-100 text-green-800': order.status === 'shipped',
                  'bg-purple-100 text-purple-800': order.status === 'delivered',
                  'bg-red-100 text-red-800': order.status === 'cancelled'
                }"
              >
                {{ getStatusText(order.status) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button 
                @click="viewOrder(order)"
                class="text-[#9810FA] hover:text-[#7a0dc8] mr-3"
              >
                <i class="fas fa-eye"></i>
              </button>
              <button 
                @click="updateOrderStatus(order)"
                class="text-[#9810FA] hover:text-[#7a0dc8]"
              >
                <i class="fas fa-edit"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Paginação -->
    <div class="flex justify-between items-center">
      <div class="text-sm text-gray-700">
        Mostrando {{ pagination.start }} a {{ pagination.end }} de {{ pagination.total }} pedidos
      </div>
      <div class="flex space-x-2">
        <button 
          @click="previousPage"
          :disabled="pagination.currentPage === 1"
          class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50"
        >
          Anterior
        </button>
        <button 
          @click="nextPage"
          :disabled="pagination.currentPage === totalPages"
          class="px-3 py-1 border border-gray-300 rounded-lg disabled:opacity-50"
        >
          Próximo
        </button>
      </div>
    </div>

    <!-- Modal de Detalhes do Pedido -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-2xl w-full">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-900">Detalhes do Pedido #{{ selectedOrder.id }}</h2>
          <button @click="selectedOrder = null" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Informações do Cliente</h3>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm text-gray-500">Nome</p>
                <p class="text-sm font-medium">{{ selectedOrder.customer.name }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="text-sm font-medium">{{ selectedOrder.customer.email }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Telefone</p>
                <p class="text-sm font-medium">{{ selectedOrder.customer.phone }}</p>
              </div>
              <div>
                <p class="text-sm text-gray-500">Endereço</p>
                <p class="text-sm font-medium">{{ selectedOrder.customer.address }}</p>
              </div>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Itens do Pedido</h3>
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Produto</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Quantidade</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Preço</th>
                  <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr v-for="item in selectedOrder.items" :key="item.id">
                  <td class="px-4 py-2 text-sm">{{ item.name }}</td>
                  <td class="px-4 py-2 text-sm">{{ item.quantity }}</td>
                  <td class="px-4 py-2 text-sm">R$ {{ item.price.toFixed(2) }}</td>
                  <td class="px-4 py-2 text-sm">R$ {{ (item.quantity * item.price).toFixed(2) }}</td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="3" class="px-4 py-2 text-right font-medium">Subtotal:</td>
                  <td class="px-4 py-2 text-sm">R$ {{ selectedOrder.subtotal.toFixed(2) }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="px-4 py-2 text-right font-medium">Frete:</td>
                  <td class="px-4 py-2 text-sm">R$ {{ selectedOrder.shipping.toFixed(2) }}</td>
                </tr>
                <tr>
                  <td colspan="3" class="px-4 py-2 text-right font-medium">Total:</td>
                  <td class="px-4 py-2 text-sm font-bold">R$ {{ selectedOrder.total.toFixed(2) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Atualização de Status -->
    <div v-if="orderToUpdate" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-900">Atualizar Status do Pedido #{{ orderToUpdate.id }}</h2>
          <button @click="orderToUpdate = null" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
          </button>
        </div>

        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Novo Status</label>
            <select 
              v-model="newStatus"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
              <option value="pending">Pendente</option>
              <option value="processing">Processando</option>
              <option value="shipped">Enviado</option>
              <option value="delivered">Entregue</option>
              <option value="cancelled">Cancelado</option>
            </select>
          </div>

          <div class="flex justify-end space-x-3">
            <button 
              @click="orderToUpdate = null"
              class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50"
            >
              Cancelar
            </button>
            <button 
              @click="saveOrderStatus"
              class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]"
            >
              Salvar
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';

interface Customer {
  name: string;
  email: string;
  phone: string;
  address: string;
}

interface OrderItem {
  id: number;
  name: string;
  quantity: number;
  price: number;
}

interface Order {
  id: number;
  customer: Customer;
  date: string;
  items: OrderItem[];
  subtotal: number;
  shipping: number;
  total: number;
  status: 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled';
}

// Estado
const filters = ref({
  search: '',
  status: '',
  startDate: '',
  endDate: ''
});

const pagination = ref({
  currentPage: 1,
  perPage: 10,
  total: 0,
  start: 1,
  end: 10
});

const orders = ref<Order[]>([
  {
    id: 1,
    customer: {
      name: 'João Silva',
      email: 'joao@email.com',
      phone: '(11) 99999-9999',
      address: 'Rua Exemplo, 123 - São Paulo, SP'
    },
    date: '2024-03-20',
    items: [
      {
        id: 1,
        name: 'Produto 1',
        quantity: 2,
        price: 99.90
      },
      {
        id: 2,
        name: 'Produto 2',
        quantity: 1,
        price: 149.90
      }
    ],
    subtotal: 349.70,
    shipping: 15.00,
    total: 364.70,
    status: 'pending'
  }
]);

const selectedOrder = ref<Order | null>(null);
const orderToUpdate = ref<Order | null>(null);
const newStatus = ref<Order['status']>('pending');

// Computed
const totalPages = computed(() => Math.ceil(pagination.value.total / pagination.value.perPage));

// Métodos
const getStatusText = (status: Order['status']) => {
  const statusMap = {
    pending: 'Pendente',
    processing: 'Processando',
    shipped: 'Enviado',
    delivered: 'Entregue',
    cancelled: 'Cancelado'
  };
  return statusMap[status];
};

const viewOrder = (order: Order) => {
  selectedOrder.value = order;
};

const updateOrderStatus = (order: Order) => {
  orderToUpdate.value = order;
  newStatus.value = order.status;
};

const saveOrderStatus = () => {
  if (orderToUpdate.value) {
    orderToUpdate.value.status = newStatus.value;
    orderToUpdate.value = null;
  }
};

const exportOrders = () => {
  // Aqui você implementaria a lógica para exportar os pedidos
  console.log('Exportando pedidos...');
};

const previousPage = () => {
  if (pagination.value.currentPage > 1) {
    pagination.value.currentPage--;
  }
};

const nextPage = () => {
  if (pagination.value.currentPage < totalPages.value) {
    pagination.value.currentPage++;
  }
};
</script> 