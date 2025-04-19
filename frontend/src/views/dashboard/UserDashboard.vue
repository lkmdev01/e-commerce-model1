<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Cabeçalho do Dashboard -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Bem-vindo, {{ user?.name }}</h1>
      <p class="text-gray-600">Gerencie sua conta e acompanhe seus pedidos</p>
    </div>

    <!-- Grid de Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <!-- Card de Pedidos -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-800">Meus Pedidos</h2>
          <span class="text-blue-600">
            <i class="fas fa-shopping-bag"></i>
          </span>
        </div>
        <p class="text-gray-600 mb-4">Acompanhe seus pedidos e entregas</p>
        <router-link to="/dashboard/orders" class="text-blue-600 hover:text-blue-800">
          Ver pedidos <i class="fas fa-arrow-right ml-2"></i>
        </router-link>
      </div>

      <!-- Card de Endereços -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-800">Endereços</h2>
          <span class="text-green-600">
            <i class="fas fa-map-marker-alt"></i>
          </span>
        </div>
        <p class="text-gray-600 mb-4">Gerencie seus endereços de entrega</p>
        <button @click="showAddressModal = true" class="text-green-600 hover:text-green-800">
          Gerenciar <i class="fas fa-arrow-right ml-2"></i>
        </button>
      </div>

      <!-- Card de Configurações -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-xl font-semibold text-gray-800">Configurações</h2>
          <span class="text-gray-600">
            <i class="fas fa-cog"></i>
          </span>
        </div>
        <p class="text-gray-600 mb-4">Preferências e segurança da conta</p>
        <button @click="showSettingsModal = true" class="text-gray-600 hover:text-gray-800">
          Configurar <i class="fas fa-arrow-right ml-2"></i>
        </button>
      </div>
    </div>

    <!-- Seção de Pedidos Recentes -->
    <div id="orders-section" class="mt-8">
      <h2 class="text-xl font-semibold text-gray-800 mb-4">Pedidos Recentes</h2>
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Número do Pedido
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Data
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Total
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ações
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="!orders?.length">
              <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                Nenhum pedido encontrado
              </td>
            </tr>
            <tr v-for="order in orders" :key="order.id">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                #{{ order.id }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ new Date(order.created_at).toLocaleDateString() }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(order.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusText(order.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                R$ {{ order.total.toFixed(2) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button @click="showOrderDetails(order)" class="text-blue-600 hover:text-blue-800">
                  <i class="fas fa-eye mr-2"></i> Detalhes
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de Endereços -->
    <div v-if="showAddressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-xl">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Meus Endereços</h2>
            <button @click="showAddressModal = false" class="text-gray-500 hover:text-gray-700">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <!-- Lista de Endereços -->
          <div v-if="addresses.length" class="mb-6">
            <div v-for="(address, index) in addresses" :key="index" class="border rounded-lg p-4 mb-4">
              <div class="flex justify-between">
                <div>
                  <p class="font-semibold">{{ address.name }}</p>
                  <p class="text-sm text-gray-600">{{ address.street }}, {{ address.number }}</p>
                  <p class="text-sm text-gray-600">{{ address.district }} - {{ address.city }}/{{ address.state }}</p>
                  <p class="text-sm text-gray-600">CEP: {{ address.zipcode }}</p>
                </div>
                <div class="flex space-x-2">
                  <button @click="editAddress(index)" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-pen"></i>
                  </button>
                  <button @click="deleteAddress(index)" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="py-6 text-center text-gray-500">
            <p>Nenhum endereço cadastrado</p>
          </div>

          <!-- Formulário de Endereço -->
          <div v-if="showAddressForm" class="border rounded-lg p-4">
            <h3 class="font-semibold mb-4">{{ editingAddressIndex === -1 ? 'Novo Endereço' : 'Editar Endereço' }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Identificação</label>
                <input 
                  v-model="addressForm.name" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                  placeholder="Ex: Casa, Trabalho"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                <input 
                  v-model="addressForm.zipcode" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                  placeholder="00000-000"
                >
              </div>
              <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rua</label>
                <input 
                  v-model="addressForm.street" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Número</label>
                <input 
                  v-model="addressForm.number" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Complemento</label>
                <input 
                  v-model="addressForm.complement" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bairro</label>
                <input 
                  v-model="addressForm.district" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                <input 
                  v-model="addressForm.city" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                >
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <input 
                  v-model="addressForm.state" 
                  type="text" 
                  class="w-full p-2 border rounded-md"
                  maxlength="2"
                >
              </div>
            </div>
            <div class="flex justify-end space-x-3">
              <button 
                @click="showAddressForm = false" 
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button 
                @click="saveAddress" 
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
              >
                Salvar
              </button>
            </div>
          </div>

          <button 
            v-if="!showAddressForm" 
            @click="newAddress" 
            class="mt-4 w-full py-2 bg-[#9810FA] text-white rounded-md hover:bg-[#8609d8]"
          >
            <i class="fas fa-plus mr-2"></i> Adicionar Endereço
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de Configurações -->
    <div v-if="showSettingsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-xl">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Configurações da Conta</h2>
            <button @click="showSettingsModal = false" class="text-gray-500 hover:text-gray-700">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <!-- Formulário de Configurações -->
          <div class="space-y-6">
            <div>
              <h3 class="text-lg font-semibold mb-3">Informações Pessoais</h3>
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
                  <input 
                    v-model="userForm.name" 
                    type="text" 
                    class="w-full p-2 border rounded-md"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                  <input 
                    v-model="userForm.email" 
                    type="email" 
                    class="w-full p-2 border rounded-md bg-gray-100"
                    disabled
                  >
                  <p class="text-xs text-gray-500 mt-1">O e-mail não pode ser alterado</p>
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-semibold mb-3">Alterar Senha</h3>
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Senha Atual</label>
                  <input 
                    v-model="userForm.currentPassword" 
                    type="password" 
                    class="w-full p-2 border rounded-md"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nova Senha</label>
                  <input 
                    v-model="userForm.newPassword" 
                    type="password" 
                    class="w-full p-2 border rounded-md"
                  >
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Nova Senha</label>
                  <input 
                    v-model="userForm.confirmPassword" 
                    type="password" 
                    class="w-full p-2 border rounded-md"
                  >
                </div>
              </div>
            </div>

            <div>
              <h3 class="text-lg font-semibold mb-3">Preferências</h3>
              <div class="grid grid-cols-1 gap-4">
                <div class="flex items-center">
                  <input 
                    id="newsletter" 
                    v-model="userForm.newsletter" 
                    type="checkbox" 
                    class="h-4 w-4 text-[#9810FA] border-gray-300 rounded"
                  >
                  <label for="newsletter" class="ml-2 block text-sm text-gray-700">
                    Receber novidades e promoções por e-mail
                  </label>
                </div>
              </div>
            </div>
          </div>

          <div class="flex justify-end space-x-3 mt-6">
            <button 
              @click="showSettingsModal = false" 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
            >
              Cancelar
            </button>
            <button 
              @click="saveUserSettings" 
              class="px-4 py-2 bg-[#9810FA] text-white rounded-md hover:bg-[#8609d8]"
            >
              Salvar Alterações
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Detalhes do Pedido -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Pedido #{{ selectedOrder.id }}</h2>
            <button @click="selectedOrder = null" class="text-gray-500 hover:text-gray-700">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <div class="mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="border rounded-lg p-3 bg-gray-50">
                <p class="text-sm text-gray-500">Data do Pedido</p>
                <p class="font-medium">{{ new Date(selectedOrder.created_at).toLocaleDateString() }}</p>
              </div>
              <div class="border rounded-lg p-3 bg-gray-50">
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-medium">
                  <span :class="getStatusClass(selectedOrder.status)" class="px-2 py-1 rounded-full text-xs">
                    {{ getStatusText(selectedOrder.status) }}
                  </span>
                </p>
              </div>
              <div class="border rounded-lg p-3 bg-gray-50">
                <p class="text-sm text-gray-500">Total</p>
                <p class="font-medium">R$ {{ selectedOrder.total.toFixed(2) }}</p>
              </div>
            </div>
          </div>

          <div class="mb-6">
            <h3 class="font-semibold mb-3">Itens do Pedido</h3>
            <div class="border rounded-lg overflow-hidden">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantidade</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex items-center">
                        <div class="h-10 w-10 bg-gray-200 rounded-md flex-shrink-0 mr-3"></div>
                        <div>
                          <p class="text-sm font-medium text-gray-900">Produto de Exemplo</p>
                          <p class="text-xs text-gray-500">SKU: 12345</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ 99.95</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ 199.90</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="font-semibold mb-3">Endereço de Entrega</h3>
              <div class="border rounded-lg p-4">
                <p>João Silva</p>
                <p class="text-sm text-gray-600">Rua Exemplo, 123</p>
                <p class="text-sm text-gray-600">Bairro - Cidade/UF</p>
                <p class="text-sm text-gray-600">CEP: 00000-000</p>
              </div>
            </div>
            <div>
              <h3 class="font-semibold mb-3">Informações de Pagamento</h3>
              <div class="border rounded-lg p-4">
                <p>Cartão de Crédito</p>
                <p class="text-sm text-gray-600">**** **** **** 1234</p>
                <p class="text-sm text-gray-600">Pagamento aprovado</p>
              </div>
            </div>
          </div>

          <div class="mt-6 border-t pt-4">
            <div class="flex justify-between">
              <p class="text-gray-700">Subtotal:</p>
              <p class="font-medium">R$ 199.90</p>
            </div>
            <div class="flex justify-between mt-1">
              <p class="text-gray-700">Frete:</p>
              <p class="font-medium">R$ 0.00</p>
            </div>
            <div class="flex justify-between mt-3 font-bold">
              <p>Total:</p>
              <p>R$ {{ selectedOrder.total.toFixed(2) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useApi } from '@/composables/useApi'
import { addressService, userSettingsService } from '@/services/api'
import type { Address } from '@/services/api'

interface Order {
  id: number
  created_at: string
  status: 'pending' | 'processing' | 'completed' | 'cancelled'
  total: number
}

const authStore = useAuthStore()
const api = useApi()

const user = ref(authStore.user)
const orders = ref<Order[]>([])
const selectedOrder = ref<Order | null>(null)

// Modais
const showAddressModal = ref(false)
const showSettingsModal = ref(false)
const showAddressForm = ref(false)

// Endereços
const addresses = ref<Array<Address & { id?: number }>>([
  {
    name: 'Casa',
    street: 'Rua das Flores',
    number: '123',
    district: 'Centro',
    city: 'São Paulo',
    state: 'SP',
    zipcode: '01001-000',
    id: 1
  }
])

const addressForm = reactive<Address>({
  name: '',
  street: '',
  number: '',
  complement: '',
  district: '',
  city: '',
  state: '',
  zipcode: ''
})

const editingAddressIndex = ref(-1)

// Formulário de usuário
const userForm = reactive({
  name: user.value?.name || '',
  email: user.value?.email || '',
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
  newsletter: true
})

const getStatusClass = (status: Order['status']) => {
  const classes: Record<Order['status'], string> = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'processing': 'bg-blue-100 text-blue-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800'
  }
  return classes[status]
}

const getStatusText = (status: Order['status']) => {
  const texts: Record<Order['status'], string> = {
    'pending': 'Pendente',
    'processing': 'Em Processamento',
    'completed': 'Concluído',
    'cancelled': 'Cancelado'
  }
  return texts[status]
}

// Métodos para pedidos
const showOrderDetails = (order: Order) => {
  selectedOrder.value = order
}

// Métodos para endereços
const newAddress = () => {
  Object.keys(addressForm).forEach(key => {
    // @ts-ignore
    addressForm[key] = key === 'complement' ? '' : ''
  })
  editingAddressIndex.value = -1
  showAddressForm.value = true
}

const editAddress = (index: number) => {
  const address = addresses.value[index]
  Object.keys(address).forEach(key => {
    // @ts-ignore
    addressForm[key] = address[key]
  })
  editingAddressIndex.value = index
  showAddressForm.value = true
}

const deleteAddress = async (index: number) => {
  if (confirm('Tem certeza que deseja excluir este endereço?')) {
    try {
      const addressId = addresses.value[index].id
      if (addressId) {
        await addressService.delete(addressId)
        // Remover da lista local
        addresses.value.splice(index, 1)
      }
    } catch (error) {
      console.error('Erro ao excluir endereço:', error)
      alert('Ocorreu um erro ao excluir o endereço. Tente novamente.')
    }
  }
}

const saveAddress = async () => {
  // Simples validação
  if (!addressForm.name || !addressForm.street || !addressForm.number || !addressForm.district || 
      !addressForm.city || !addressForm.state || !addressForm.zipcode) {
    alert('Por favor, preencha todos os campos obrigatórios')
    return
  }

  try {
    if (editingAddressIndex.value >= 0) {
      // Edição
      const addressId = addresses.value[editingAddressIndex.value].id
      if (addressId) {
        await addressService.update(addressId, addressForm)
        // Atualizar na lista local
        addresses.value[editingAddressIndex.value] = { ...addressForm, id: addressId }
      }
    } else {
      // Novo endereço
      const newAddress = await addressService.create(addressForm)
      addresses.value.push(newAddress)
    }
    
    showAddressForm.value = false
  } catch (error) {
    console.error('Erro ao salvar endereço:', error)
    alert('Ocorreu um erro ao salvar o endereço. Tente novamente.')
  }
}

// Métodos para configurações do usuário
const saveUserSettings = async () => {
  // Validação básica
  if (!userForm.name) {
    alert('Por favor, informe seu nome')
    return
  }

  try {
    // Salvar configurações básicas
    await userSettingsService.update({
      name: userForm.name,
      newsletter: userForm.newsletter
    })
    
    // Alterar senha, se necessário
    if (userForm.currentPassword || userForm.newPassword || userForm.confirmPassword) {
      if (!userForm.currentPassword) {
        alert('Por favor, informe sua senha atual')
        return
      }
      if (!userForm.newPassword) {
        alert('Por favor, informe a nova senha')
        return
      }
      if (userForm.newPassword !== userForm.confirmPassword) {
        alert('As senhas não coincidem')
        return
      }
      if (userForm.newPassword.length < 8) {
        alert('A nova senha deve ter pelo menos 8 caracteres')
        return
      }
      
      await userSettingsService.changePassword({
        current_password: userForm.currentPassword,
        new_password: userForm.newPassword,
        new_password_confirmation: userForm.confirmPassword
      })
    }
    
    // Atualizar dados locais
    if (user.value) {
      user.value.name = userForm.name
    }
    
    alert('Suas configurações foram salvas com sucesso!')
    showSettingsModal.value = false
    
    // Limpar campos de senha
    userForm.currentPassword = ''
    userForm.newPassword = ''
    userForm.confirmPassword = ''
  } catch (error: any) {
    console.error('Erro ao salvar configurações:', error)
    if (error.response && error.response.data && error.response.data.message) {
      alert(error.response.data.message)
    } else {
      alert('Ocorreu um erro ao salvar as configurações. Tente novamente.')
    }
  }
}

// Carregar endereços do usuário
const loadAddresses = async () => {
  try {
    const userAddresses = await addressService.getAll()
    addresses.value = userAddresses
  } catch (error) {
    console.error('Erro ao carregar endereços:', error)
    // Manter os dados de exemplo em caso de falha
  }
}

// Carregar configurações do usuário
const loadUserSettings = async () => {
  try {
    const settings = await userSettingsService.get()
    userForm.name = settings.name
    userForm.email = settings.email
    userForm.newsletter = settings.newsletter
  } catch (error) {
    console.error('Erro ao carregar configurações:', error)
    // Manter os dados padrão em caso de falha
  }
}

onMounted(async () => {
  try {
    // Carregar pedidos
    const response = await api.get('/orders')
    orders.value = response.data
    
    // Carregar endereços e configurações
    await Promise.all([
      loadAddresses(),
      loadUserSettings()
    ])
  } catch (error) {
    console.error('Erro ao carregar dados:', error)
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
      }
    ]
  }
})
</script>

<style scoped>
/* Estilos específicos do componente */
</style> 