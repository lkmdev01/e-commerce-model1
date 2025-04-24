<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Dashboard</h1>
      <div class="flex gap-4">
        <router-link to="/dashboard/products" class="bg-purple-600 text-white px-4 py-2 rounded-md flex items-center gap-2 hover:bg-purple-700">
          <i class="fas fa-plus"></i>
          Novo Produto
        </router-link>
        <button class="text-gray-700 px-4 py-2 rounded-md border border-gray-300 flex items-center gap-2 hover:bg-gray-50">
          <i class="fas fa-download"></i>
          Exportar Relatório
        </button>
      </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <!-- Card Vendas Hoje -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm mb-2">Vendas Hoje</h3>
            <p class="text-2xl font-bold">R$ 2.500</p>
            <p class="text-sm text-green-500 flex items-center mt-2">
              <i class="fas fa-arrow-up mr-1"></i>
              12% vs. ontem
            </p>
          </div>
          <div class="bg-green-100 p-3 rounded-full">
            <i class="fas fa-shopping-cart text-green-500"></i>
          </div>
        </div>
      </div>

      <!-- Card Pedidos Pendentes -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm mb-2">Pedidos Pendentes</h3>
            <p class="text-2xl font-bold">15</p>
            <p class="text-sm text-yellow-500 flex items-center mt-2">
              <i class="fas fa-arrow-up mr-1"></i>
              5% vs. ontem
            </p>
          </div>
          <div class="bg-yellow-100 p-3 rounded-full">
            <i class="fas fa-clock text-yellow-500"></i>
          </div>
        </div>
      </div>

      <!-- Card Produtos Vendidos -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm mb-2">Produtos Vendidos</h3>
            <p class="text-2xl font-bold">45</p>
            <p class="text-sm text-[#9810FA] flex items-center mt-2">
              <i class="fas fa-arrow-up mr-1"></i>
              8% vs. ontem
            </p>
          </div>
          <div class="bg-blue-100 p-3 rounded-full">
            <i class="fas fa-box text-[#9810FA]"></i>
          </div>
        </div>
      </div>

      <!-- Card Receita Mensal -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <div class="flex justify-between items-start">
          <div>
            <h3 class="text-gray-500 text-sm mb-2">Receita Mensal</h3>
            <p class="text-2xl font-bold">R$ 45.000</p>
            <p class="text-sm text-purple-500 flex items-center mt-2">
              <i class="fas fa-arrow-up mr-1"></i>
              15% vs. mês anterior
            </p>
          </div>
          <div class="bg-purple-100 p-3 rounded-full">
            <i class="fas fa-chart-line text-purple-500"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Gráfico de Vendas -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Vendas por Período</h3>
        <div class="h-80">
          <LineChart :data="salesChartData" :options="chartOptions" />
        </div>
      </div>

      <!-- Gráfico de Produtos -->
      <div class="bg-white rounded-lg p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Produtos Mais Vendidos</h3>
        <div class="h-80">
          <BarChart :data="productsChartData" :options="chartOptions" />
        </div>
      </div>
    </div>

    <!-- Últimos Pedidos -->
    <div class="bg-white rounded-lg p-6 shadow-sm">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Últimos Pedidos</h3>
        <div class="flex gap-2">
          <button class="p-2 rounded hover:bg-gray-100" @click="previousPage" :disabled="currentPage === 1">
            <i class="fas fa-chevron-left"></i>
          </button>
          <button class="p-2 rounded hover:bg-gray-100" @click="nextPage" :disabled="currentPage === totalPages">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produtos</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="order in paginatedOrders" :key="order.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">#{{ order.id }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ order.customer }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ order.items }} itens</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ order.total.toFixed(2) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(order.status)">
                  {{ order.status }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.date) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend } from 'chart.js';
import { Line as LineChart, Bar as BarChart } from 'vue-chartjs';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend
);

// Dados do gráfico de vendas
const salesChartData = {
  labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
  datasets: [
    {
      label: 'Vendas',
      data: [30000, 35000, 25000, 45000, 40000, 50000],
      borderColor: '#9333EA',
      backgroundColor: 'rgba(147, 51, 234, 0.1)',
      tension: 0.4,
      fill: true
    }
  ]
};

// Dados do gráfico de produtos
const productsChartData = {
  labels: ['Produto A', 'Produto B', 'Produto C', 'Produto D', 'Produto E'],
  datasets: [
    {
      label: 'Vendas',
      data: [120, 90, 70, 60, 50],
      backgroundColor: '#9333EA',
    }
  ]
};

// Opções dos gráficos
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
};

// Dados dos pedidos
const orders = ref([
  {
    id: 1234,
    customer: 'João Silva',
    items: 3,
    total: 299.90,
    status: 'Concluído',
    date: new Date('2024-04-10')
  },
  {
    id: 1235,
    customer: 'Maria Santos',
    items: 1,
    total: 149.90,
    status: 'Pendente',
    date: new Date('2024-04-10')
  },
  // Adicione mais pedidos aqui
]);

// Paginação
const itemsPerPage = 5;
const currentPage = ref(1);
const totalPages = computed(() => Math.ceil(orders.value.length / itemsPerPage));

const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  const end = start + itemsPerPage;
  return orders.value.slice(start, end);
});

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

// Formatação e estilos
const formatDate = (date: Date) => {
  return new Intl.DateTimeFormat('pt-BR').format(date);
};

const getStatusClass = (status: string) => {
  const baseClasses = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full';
  switch (status) {
    case 'Concluído':
      return `${baseClasses} bg-green-100 text-green-800`;
    case 'Pendente':
      return `${baseClasses} bg-yellow-100 text-yellow-800`;
    case 'Cancelado':
      return `${baseClasses} bg-red-100 text-red-800`;
    default:
      return `${baseClasses} bg-gray-100 text-gray-800`;
  }
};
</script> 