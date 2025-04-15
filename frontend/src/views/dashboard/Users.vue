<template>
  <div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Usuários</h1>
      <div class="flex space-x-4">
        <button 
          @click="openUserModal()"
          class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8] transition-colors"
        >
          <i class="fas fa-plus mr-2"></i>
          Novo Usuário
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm p-6 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#9810FA]"></div>
      <p class="mt-2 text-gray-600">Carregando usuários...</p>
    </div>

    <!-- Erro -->
    <div v-else-if="error" class="bg-white rounded-xl shadow-sm p-6 text-center">
      <p class="text-red-600">{{ error }}</p>
      <button @click="fetchUsers" class="mt-2 px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]">
        Tentar novamente
      </button>
    </div>

    <!-- Lista vazia -->
    <div v-else-if="users.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center">
      <i class="fas fa-users text-gray-400 text-5xl mb-4"></i>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum usuário encontrado</h3>
      <p class="text-gray-600 mb-4">Comece adicionando um novo usuário ao sistema.</p>
      <button 
        @click="openUserModal()"
        class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]"
      >
        <i class="fas fa-plus mr-2"></i>
        Adicionar Usuário
      </button>
    </div>

    <!-- Conteúdo normal -->
    <div v-else>
      <!-- Filtros -->
      <div class="bg-white p-4 rounded-lg shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
            <input 
              type="text" 
              v-model="filters.search" 
              placeholder="Buscar por nome ou email..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Função</label>
            <select 
              v-model="filters.role"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
              <option value="">Todas</option>
              <option value="admin">Administrador</option>
              <option value="user">Usuário</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
              v-model="filters.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
              <option value="">Todos</option>
              <option value="true">Ativo</option>
              <option value="false">Inativo</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Lista de Usuários -->
      <div class="bg-white rounded-lg shadow-sm overflow-hidden mt-6">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuário</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Função</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in filteredUsers" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                      <i class="fas fa-user"></i>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getRoleClass(user.role)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getRoleText(user.role) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getStatusClass(user.status)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                  {{ getStatusText(user.status) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button @click="editUser(user)" class="text-[#9810FA] hover:text-[#7a0dc8] mr-3">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteUser(user)" class="text-red-600 hover:text-red-900">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-700">
          Mostrando {{ users.length > 0 ? 1 : 0 }} a {{ users.length }} de {{ users.length }} usuários
        </div>
      </div>
    </div>

    <!-- Modal de Usuário -->
    <div v-if="showUserModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="p-6">
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900">
              {{ isEditing ? 'Editar Usuário' : 'Novo Usuário' }}
            </h2>
            <button @click="closeUserModal" class="text-gray-400 hover:text-gray-500">
              <i class="fas fa-times"></i>
            </button>
          </div>

          <form @submit.prevent="saveUser" class="space-y-4">
            <!-- Mensagem de erro -->
            <div v-if="error" class="bg-red-50 p-4 rounded-md text-red-600 text-sm mb-4" v-html="error"></div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome</label>
              <input 
                type="text" 
                v-model="userForm.name"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input 
                type="email" 
                v-model="userForm.email"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
              >
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Função</label>
              <select 
                v-model="userForm.role"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
              >
                <option value="admin">Administrador</option>
                <option value="user">Usuário</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="userForm.status"
                required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
              >
                <option :value="true">Ativo</option>
                <option :value="false">Inativo</option>
              </select>
            </div>

            <div v-if="!isEditing || changePassword">
              <div class="flex justify-between">
                <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                <button 
                  v-if="isEditing && !changePassword" 
                  type="button" 
                  @click="changePassword = true"
                  class="text-xs text-[#9810FA]"
                >
                  Alterar senha
                </button>
              </div>
              <input 
                type="password" 
                v-model="userForm.password"
                :required="!isEditing || changePassword"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
              >
            </div>

            <div class="flex justify-end space-x-4 mt-6">
              <button 
                type="button"
                @click="closeUserModal"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Cancelar
              </button>
              <button 
                type="submit"
                class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]"
                :disabled="formSubmitting"
              >
                <span v-if="formSubmitting" class="mr-2">
                  <div class="inline-block animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                </span>
                {{ isEditing ? 'Salvar' : 'Criar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { userService, type User } from '@/services/api';

interface UserForm {
  name: string;
  email: string;
  role: string;
  status: boolean;
  password?: string;
}

// Estado
const filters = ref({
  search: '',
  role: '',
  status: ''
});

const users = ref<User[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const showUserModal = ref(false);
const isEditing = ref(false);
const changePassword = ref(false);
const formSubmitting = ref(false);
const editingUserId = ref<number | null>(null);

const userForm = ref<UserForm>({
  name: '',
  email: '',
  role: 'user',
  status: true,
  password: ''
});

// Computed
const filteredUsers = computed(() => {
  let result = [...users.value];
  
  // Filtrar por busca
  if (filters.value.search) {
    const search = filters.value.search.toLowerCase();
    result = result.filter(user => 
      user.name.toLowerCase().includes(search) || 
      user.email.toLowerCase().includes(search)
    );
  }
  
  // Filtrar por role
  if (filters.value.role) {
    result = result.filter(user => user.role === filters.value.role);
  }
  
  // Filtrar por status
  if (filters.value.status !== '') {
    const status = filters.value.status === 'true';
    result = result.filter(user => user.status === status);
  }
  
  return result;
});

// Carregar usuários ao montar o componente
onMounted(() => {
  fetchUsers();
});

// Buscar usuários da API
const fetchUsers = async () => {
  try {
    loading.value = true;
    error.value = null;
    const data = await userService.getAll();
    users.value = data;
  } catch (err) {
    console.error('Erro ao carregar usuários:', err);
    error.value = 'Não foi possível carregar a lista de usuários. Tente novamente mais tarde.';
  } finally {
    loading.value = false;
  }
};

// Métodos
const getRoleClass = (role: string) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    user: 'bg-gray-100 text-gray-800',
    default: 'bg-blue-100 text-blue-800'
  };
  return classes[role as keyof typeof classes] || classes.default;
};

const getRoleText = (role: string) => {
  const texts: Record<string, string> = {
    admin: 'Administrador',
    user: 'Usuário'
  };
  return texts[role] || role;
};

const getStatusClass = (status: boolean) => {
  return status 
    ? 'bg-green-100 text-green-800' 
    : 'bg-red-100 text-red-800';
};

const getStatusText = (status: boolean) => {
  return status ? 'Ativo' : 'Inativo';
};

const openUserModal = () => {
  isEditing.value = false;
  changePassword.value = false;
  editingUserId.value = null;
  userForm.value = {
    name: '',
    email: '',
    role: 'user',
    status: true,
    password: ''
  };
  showUserModal.value = true;
};

const editUser = (user: User) => {
  isEditing.value = true;
  changePassword.value = false;
  editingUserId.value = user.id;
  userForm.value = {
    name: user.name,
    email: user.email,
    role: user.role,
    status: user.status,
    password: ''
  };
  showUserModal.value = true;
};

const closeUserModal = () => {
  showUserModal.value = false;
  isEditing.value = false;
  changePassword.value = false;
  editingUserId.value = null;
};

const saveUser = async () => {
  try {
    formSubmitting.value = true;
    error.value = null;
    
    // Para novos usuários, garantir que a senha tenha pelo menos 6 caracteres
    if (!isEditing.value && (!userForm.value.password || userForm.value.password.length < 6)) {
      error.value = 'A senha deve ter pelo menos 6 caracteres';
      formSubmitting.value = false;
      return;
    }
    
    // Para usuários existentes com alteração de senha
    if (isEditing.value && changePassword.value && (!userForm.value.password || userForm.value.password.length < 6)) {
      error.value = 'A senha deve ter pelo menos 6 caracteres';
      formSubmitting.value = false;
      return;
    }
    
    // Dados para envio
    const userData: Partial<User> = {
      name: userForm.value.name,
      email: userForm.value.email,
      role: userForm.value.role
    };
    
    // Adicionar status explicitamente como boolean
    userData.status = userForm.value.status === true;
    
    // Adicionar senha para novos usuários ou edição com alteração de senha
    if (!isEditing.value || (isEditing.value && changePassword.value && userForm.value.password)) {
      userData.password = userForm.value.password;
    }
    
    console.log('Enviando dados:', userData);
    
    if (isEditing.value && editingUserId.value) {
      await userService.update(editingUserId.value, userData);
    } else {
      await userService.create(userData);
    }
    
    // Recarregar a lista de usuários
    await fetchUsers();
    closeUserModal();
  } catch (err: any) {
    console.error('Erro ao salvar usuário:', err);
    
    // Tentar exibir mensagens específicas de validação
    if (err.response?.data?.errors) {
      const errors = err.response.data.errors;
      // Converter o objeto de erros em um array de mensagens
      const errorMessages = Object.entries(errors).map(([field, messages]) => {
        if (Array.isArray(messages)) {
          return `${field}: ${messages.join(', ')}`;
        }
        return `${field}: ${messages}`;
      });
      
      error.value = errorMessages.join('<br>');
    } else if (err.response?.data?.message) {
      error.value = err.response.data.message;
    } else {
      error.value = 'Erro ao salvar usuário. Verifique os dados e tente novamente.';
    }
  } finally {
    formSubmitting.value = false;
  }
};

const deleteUser = async (user: User) => {
  if (confirm(`Tem certeza que deseja excluir o usuário ${user.name}?`)) {
    try {
      loading.value = true;
      await userService.delete(user.id);
      await fetchUsers();
    } catch (err) {
      console.error('Erro ao excluir usuário:', err);
      error.value = 'Não foi possível excluir o usuário. Tente novamente mais tarde.';
    } finally {
      loading.value = false;
    }
  }
};
</script> 