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
            <option value="manager">Gerente</option>
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
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Lista de Usuários -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
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
          <tr v-for="user in users" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img 
                    :src="user.avatar || '/images/default-avatar.png'" 
                    :alt="user.name"
                    class="h-10 w-10 rounded-full object-cover"
                  >
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                  <div class="text-sm text-gray-500">{{ user.phone }}</div>
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
    <div class="flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Mostrando {{ pagination.start }} a {{ pagination.end }} de {{ pagination.total }} usuários
      </div>
      <div class="flex space-x-2">
        <button 
          @click="changePage(pagination.currentPage - 1)"
          :disabled="pagination.currentPage === 1"
          class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Anterior
        </button>
        <button 
          @click="changePage(pagination.currentPage + 1)"
          :disabled="pagination.currentPage === totalPages"
          class="px-3 py-1 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Próxima
        </button>
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
              <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
              <input 
                type="tel" 
                v-model="userForm.phone"
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
                <option value="manager">Gerente</option>
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
                <option value="active">Ativo</option>
                <option value="inactive">Inativo</option>
              </select>
            </div>

            <div v-if="!isEditing">
              <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
              <input 
                type="password" 
                v-model="userForm.password"
                required
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
              >
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
import { ref, computed } from 'vue';

interface User {
  id: number;
  name: string;
  email: string;
  phone: string;
  role: 'admin' | 'manager' | 'user';
  status: 'active' | 'inactive';
  avatar?: string;
}

interface UserForm {
  name: string;
  email: string;
  phone: string;
  role: User['role'];
  status: User['status'];
  password?: string;
}

// Estado
const filters = ref({
  search: '',
  role: '',
  status: ''
});

const pagination = ref({
  currentPage: 1,
  perPage: 10,
  total: 0,
  start: 1,
  end: 10
});

const users = ref<User[]>([
  {
    id: 1,
    name: 'João Silva',
    email: 'joao@email.com',
    phone: '(11) 99999-9999',
    role: 'admin',
    status: 'active',
    avatar: '/images/person1.jpeg'
  },
  // Adicione mais usuários mockados aqui
]);

const showUserModal = ref(false);
const isEditing = ref(false);
const userForm = ref<UserForm>({
  name: '',
  email: '',
  phone: '',
  role: 'user',
  status: 'active',
  password: ''
});

// Computed
const totalPages = computed(() => Math.ceil(pagination.value.total / pagination.value.perPage));

// Métodos
const getRoleClass = (role: User['role']) => {
  const classes = {
    admin: 'bg-purple-100 text-purple-800',
    manager: 'bg-blue-100 text-blue-800',
    user: 'bg-gray-100 text-gray-800'
  };
  return classes[role];
};

const getRoleText = (role: User['role']) => {
  const texts = {
    admin: 'Administrador',
    manager: 'Gerente',
    user: 'Usuário'
  };
  return texts[role];
};

const getStatusClass = (status: User['status']) => {
  const classes = {
    active: 'bg-green-100 text-green-800',
    inactive: 'bg-red-100 text-red-800'
  };
  return classes[status];
};

const getStatusText = (status: User['status']) => {
  const texts = {
    active: 'Ativo',
    inactive: 'Inativo'
  };
  return texts[status];
};

const openUserModal = () => {
  isEditing.value = false;
  userForm.value = {
    name: '',
    email: '',
    phone: '',
    role: 'user',
    status: 'active',
    password: ''
  };
  showUserModal.value = true;
};

const editUser = (user: User) => {
  isEditing.value = true;
  userForm.value = {
    name: user.name,
    email: user.email,
    phone: user.phone,
    role: user.role,
    status: user.status
  };
  showUserModal.value = true;
};

const closeUserModal = () => {
  showUserModal.value = false;
  isEditing.value = false;
  userForm.value = {
    name: '',
    email: '',
    phone: '',
    role: 'user',
    status: 'active',
    password: ''
  };
};

const saveUser = () => {
  if (isEditing.value) {
    // Aqui você implementaria a lógica para atualizar o usuário
    const index = users.value.findIndex(u => u.email === userForm.value.email);
    if (index !== -1) {
      users.value[index] = {
        ...users.value[index],
        ...userForm.value
      };
    }
  } else {
    // Aqui você implementaria a lógica para criar um novo usuário
    const newUser: User = {
      id: users.value.length + 1,
      ...userForm.value,
      avatar: '/images/default-avatar.png'
    };
    users.value.push(newUser);
  }
  closeUserModal();
};

const deleteUser = (user: User) => {
  if (confirm('Tem certeza que deseja excluir este usuário?')) {
    // Aqui você implementaria a lógica para excluir o usuário
    users.value = users.value.filter(u => u.id !== user.id);
  }
};

const changePage = (page: number) => {
  if (page >= 1 && page <= totalPages.value) {
    pagination.value.currentPage = page;
    // Aqui você faria a chamada para a API para buscar os usuários da página
  }
};
</script> 