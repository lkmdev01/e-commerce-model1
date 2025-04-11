<template>
  <div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Produtos</h1>
      <button 
        @click="openNewProductModal"
        class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8] transition-colors"
      >
        <i class="fas fa-plus mr-2"></i>
        Novo Produto
      </button>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-4 rounded-xl shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative">
          <input
            type="text"
            v-model="filters.search"
            placeholder="Buscar produtos..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          />
          <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
        <div>
          <select
            v-model="filters.category"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          >
            <option value="">Todas as Categorias</option>
            <option v-for="category in categories" :key="category.id" :value="category.name">
              {{ category.name }}
            </option>
          </select>
        </div>
        <div>
          <select
            v-model="filters.status"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          >
            <option value="">Todos os Status</option>
            <option value="active">Ativo</option>
            <option value="inactive">Inativo</option>
          </select>
        </div>
        <div>
          <select
            v-model="filters.sort"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
          >
            <option value="newest">Mais Recentes</option>
            <option value="oldest">Mais Antigos</option>
            <option value="price_asc">Menor Preço</option>
            <option value="price_desc">Maior Preço</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Lista de Produtos -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <!-- Loading Indicator -->
      <div v-if="loading" class="p-6 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#9810FA]"></div>
        <p class="mt-2 text-gray-600">Carregando produtos...</p>
      </div>
      
      <!-- Error Message -->
      <div v-else-if="error" class="p-6 text-center text-red-600">
        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
        <p>{{ error }}</p>
        <button @click="fetchProducts" class="mt-2 px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]">
          Tentar novamente
        </button>
      </div>
      
      <!-- Empty State -->
      <div v-else-if="products.length === 0" class="p-12 text-center">
        <i class="fas fa-box-open text-gray-400 text-5xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
        <p class="text-gray-600 mb-4">Comece adicionando um novo produto à sua loja.</p>
        <button 
          @click="openNewProductModal"
          class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]"
        >
          <i class="fas fa-plus mr-2"></i>
          Adicionar Produto
        </button>
      </div>
      
      <!-- Products Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produto</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estoque</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="product in paginatedProducts" :key="product.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-10 w-10 flex-shrink-0">
                    <img 
                      :src="product.image_url || '/images/placeholder.jpg'" 
                      :alt="product.name"
                      class="h-10 w-10 rounded-lg object-cover"
                    />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                    <div class="text-sm text-gray-500">{{ product.slug }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ typeof product.category === 'object' && product.category !== null
                   ? (product.category as any).name 
                   : product.category }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ Number(product.price).toFixed(2) }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ product.stock }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    product.active 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ product.active ? 'Ativo' : 'Inativo' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="editProduct(product)"
                  class="text-[#9810FA] hover:text-[#7a0dc8] mr-3"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  @click="deleteProduct(product)"
                  class="text-red-600 hover:text-red-800"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Paginação -->
    <div v-if="products.length > 0" class="flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Mostrando {{ pagination.start }} a {{ pagination.end }} de {{ pagination.total }} produtos
      </div>
      <div class="flex space-x-2">
        <button 
          @click="prevPage"
          :disabled="currentPage === 1"
          class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Anterior
        </button>
        <button 
          v-for="page in displayedPages" 
          :key="page"
          @click="goToPage(page)"
          :class="[
            'px-3 py-1 rounded-md text-sm font-medium',
            currentPage === page
              ? 'bg-[#9810FA] text-white'
              : 'text-gray-700 hover:bg-gray-50'
          ]"
        >
          {{ page }}
        </button>
        <button 
          @click="nextPage"
          :disabled="currentPage === totalPages"
          class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Próxima
        </button>
      </div>
    </div>

    <!-- Modal de Produto -->
    <div v-if="showProductModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ editingProduct ? 'Editar Produto' : 'Novo Produto' }}
            </h3>
            <button 
              @click="closeProductModal"
              class="text-gray-400 hover:text-gray-500"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        
        <form @submit.prevent="saveProduct" class="p-6">
          <!-- Error Message in Form -->
          <div v-if="error" class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg">
            <p><i class="fas fa-exclamation-triangle mr-2"></i> {{ error }}</p>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome do Produto</label>
              <input 
                type="text" 
                v-model="productForm.name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                required
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Preço</label>
              <input 
                type="number" 
                v-model="productForm.price"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                min="0"
                step="0.01"
                required
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Estoque</label>
              <input 
                type="number" 
                v-model="productForm.stock"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                min="0"
                required
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
              <select 
                v-model="productForm.category_id"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                required
              >
                <option value="">Selecione uma categoria</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="productForm.active"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                required
              >
                <option :value="true">Ativo</option>
                <option :value="false">Inativo</option>
              </select>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
              <textarea 
                v-model="productForm.description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                required
              ></textarea>
            </div>
            
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Imagem do Produto</label>
              
              <!-- Image Preview -->
              <div v-if="productForm.image_preview" class="mb-4 flex justify-center">
                <img 
                  :src="productForm.image_preview" 
                  class="h-40 w-auto object-contain rounded-lg border border-gray-300"
                  alt="Preview"
                />
              </div>
              
              <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                <div class="space-y-1 text-center">
                  <div class="flex text-sm text-gray-600">
                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-[#9810FA] hover:text-[#7a0dc8] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#9810FA]">
                      <span>Upload de arquivo</span>
                      <input 
                        type="file" 
                        class="sr-only"
                        @change="handleImageUpload"
                        accept="image/*"
                      >
                    </label>
                    <p class="pl-1">ou arraste e solte</p>
                  </div>
                  <p class="text-xs text-gray-500">PNG, JPG, GIF até 10MB</p>
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-6 flex justify-end space-x-3">
            <button 
              type="button"
              @click="closeProductModal"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              :disabled="loading"
            >
              Cancelar
            </button>
            <button 
              type="submit"
              class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8] disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              :disabled="loading"
            >
              <span v-if="loading" class="mr-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ loading ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { productService, categoryService, type Product, type Category } from '@/services/api';

interface ProductForm {
  name: string;
  description: string;
  price: number;
  stock: number;
  category_id: string | number;
  active: boolean;
  image: File | null;
  image_preview?: string;
}

// Estado
const filters = ref({
  search: '',
  category: '',
  status: '',
  sort: 'newest'
});

const pagination = ref({
  currentPage: 1,
  perPage: 10,
  total: 0,
  start: 1,
  end: 10
});

const loading = ref(false);
const error = ref<string | null>(null);
const showProductModal = ref(false);
const editingProduct = ref<Product | null>(null);
const productForm = ref<ProductForm>({
  name: '',
  description: '',
  price: 0,
  stock: 0,
  category_id: '',
  active: true,
  image: null,
  image_preview: undefined
});

// Dados dos produtos e categorias
const products = ref<Product[]>([]);
const categories = ref<Category[]>([]);

// Filtro de status
const statusFilter = ref({
  active: 'active',
  inactive: 'inactive'
});

// Buscar produtos e categorias do backend
onMounted(() => {
  fetchProducts();
  fetchCategories();
});

// Buscar categorias
const fetchCategories = async () => {
  try {
    const data = await categoryService.getAll();
    categories.value = data;
    
    // Caso não tenha categorias ou haja erro de carregamento, adicionamos algumas padrão
    if (categories.value.length === 0) {
      categories.value = [
        { id: 1, name: 'Eletrônicos', slug: 'eletronicos', description: 'Produtos eletrônicos', active: true, image_url: null, created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
        { id: 2, name: 'Roupas', slug: 'roupas', description: 'Vestuário', active: true, image_url: null, created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
        { id: 3, name: 'Acessórios', slug: 'acessorios', description: 'Acessórios diversos', active: true, image_url: null, created_at: new Date().toISOString(), updated_at: new Date().toISOString() }
      ];
    }
  } catch (err) {
    console.error('Erro ao buscar categorias:', err);
    // Em caso de erro, adiciona categorias padrão
    categories.value = [
      { id: 1, name: 'Eletrônicos', slug: 'eletronicos', description: 'Produtos eletrônicos', active: true, image_url: null, created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
      { id: 2, name: 'Roupas', slug: 'roupas', description: 'Vestuário', active: true, image_url: null, created_at: new Date().toISOString(), updated_at: new Date().toISOString() },
      { id: 3, name: 'Acessórios', slug: 'acessorios', description: 'Acessórios diversos', active: true, image_url: null, created_at: new Date().toISOString(), updated_at: new Date().toISOString() }
    ];
  }
};

// Buscar produtos do backend
const fetchProducts = async () => {
  try {
    loading.value = true;
    error.value = null;
    const data = await productService.getAll();
    products.value = data;
    
    pagination.value.total = data.length; // Ou use meta.total se a API retornar paginação
    updatePaginationInfo();
  } catch (err) {
    console.error('Erro ao buscar produtos:', err);
    error.value = 'Erro ao carregar produtos. Tente novamente.';
  } finally {
    loading.value = false;
  }
};

// Atualizar informações de paginação
const updatePaginationInfo = () => {
  const start = (pagination.value.currentPage - 1) * pagination.value.perPage + 1;
  const end = Math.min(start + pagination.value.perPage - 1, pagination.value.total);
  
  pagination.value.start = start;
  pagination.value.end = end;
};

// Computed
const currentPage = computed(() => pagination.value.currentPage);
const totalPages = computed(() => Math.ceil(pagination.value.total / pagination.value.perPage));

const displayedPages = computed(() => {
  const pages = [];
  const maxPages = 5;
  let start = Math.max(1, currentPage.value - Math.floor(maxPages / 2));
  let end = Math.min(totalPages.value, start + maxPages - 1);
  
  if (end - start + 1 < maxPages) {
    start = Math.max(1, end - maxPages + 1);
  }
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  
  return pages;
});

// Filtrar produtos
const filteredProducts = computed(() => {
  let result = [...products.value];
  
  // Filtrar por pesquisa
  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase();
    result = result.filter(product => 
      product.name.toLowerCase().includes(searchTerm) ||
      (product.description && product.description.toLowerCase().includes(searchTerm))
    );
  }
  
  // Filtrar por categoria
  if (filters.value.category) {
    result = result.filter(product => {
      // Verifica se product.category é um objeto ou string
      if (typeof product.category === 'object' && product.category !== null) {
        return (product.category as any).name === filters.value.category;
      } else {
        return product.category === filters.value.category;
      }
    });
  }
  
  // Filtrar por status (usando a coluna active)
  if (filters.value.status) {
    const isActive = filters.value.status === 'active';
    result = result.filter(product => product.active === isActive);
  }
  
  // Ordenar produtos
  switch (filters.value.sort) {
    case 'newest':
      result.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
      break;
    case 'oldest':
      result.sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime());
      break;
    case 'price_asc':
      result.sort((a, b) => a.price - b.price);
      break;
    case 'price_desc':
      result.sort((a, b) => b.price - a.price);
      break;
  }
  
  return result;
});

// Produtos paginados
const paginatedProducts = computed(() => {
  const start = (pagination.value.currentPage - 1) * pagination.value.perPage;
  const end = start + pagination.value.perPage;
  return filteredProducts.value.slice(start, end);
});

// Métodos
const openNewProductModal = () => {
  editingProduct.value = null;
  productForm.value = {
    name: '',
    description: '',
    price: 0,
    stock: 0,
    category_id: '',
    active: true,
    image: null,
    image_preview: undefined
  };
  showProductModal.value = true;
};

const closeProductModal = () => {
  showProductModal.value = false;
  editingProduct.value = null;
};

const handleImageUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    const file = input.files[0];
    productForm.value.image = file;
    productForm.value.image_preview = URL.createObjectURL(file);
  }
};

const saveProduct = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    // Criar objeto FormData para envio multipart/form-data
    const formData = new FormData();
    formData.append('name', productForm.value.name);
    formData.append('description', productForm.value.description);
    formData.append('price', productForm.value.price.toString());
    formData.append('stock', productForm.value.stock.toString());
    formData.append('category_id', productForm.value.category_id.toString());
    formData.append('active', productForm.value.active === true ? '1' : '0');
    
    if (productForm.value.image) {
      formData.append('image', productForm.value.image);
    }
    
    if (editingProduct.value) {
      // Atualizar produto existente
      await productService.update(editingProduct.value.id, formData);
    } else {
      // Criar novo produto
      await productService.create(formData);
    }
    
    // Recarregar produtos
    fetchProducts();
    closeProductModal();
  } catch (err: any) {
    console.error('Erro ao salvar produto:', err);
    error.value = err.response?.data?.message || 'Erro ao salvar produto. Tente novamente.';
  } finally {
    loading.value = false;
  }
};

const editProduct = (product: Product) => {
  editingProduct.value = product;
  productForm.value = {
    name: product.name,
    description: product.description,
    price: product.price,
    stock: product.stock,
    category_id: product.category_id ?? '',
    active: Boolean(product.active),
    image: null,
    image_preview: product.image_url || undefined
  };
  showProductModal.value = true;
};

const deleteProduct = async (product: Product) => {
  if (!confirm(`Tem certeza que deseja excluir o produto "${product.name}"?`)) {
    return;
  }
  
  try {
    loading.value = true;
    error.value = null;
    await productService.delete(product.id);
    
    // Remover produto da lista
    products.value = products.value.filter(p => p.id !== product.id);
    
    // Atualizar paginação
    pagination.value.total = products.value.length;
    updatePaginationInfo();
  } catch (err: any) {
    console.error('Erro ao excluir produto:', err);
    error.value = err.response?.data?.message || 'Erro ao excluir produto. Tente novamente.';
  } finally {
    loading.value = false;
  }
};

const prevPage = () => {
  if (pagination.value.currentPage > 1) {
    pagination.value.currentPage--;
    updatePaginationInfo();
  }
};

const nextPage = () => {
  if (pagination.value.currentPage < totalPages.value) {
    pagination.value.currentPage++;
    updatePaginationInfo();
  }
};

const goToPage = (page: number) => {
  pagination.value.currentPage = page;
  updatePaginationInfo();
};
</script> 