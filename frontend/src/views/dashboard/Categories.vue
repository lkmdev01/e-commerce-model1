<template>
  <div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Categorias</h1>
      <button 
        @click="openNewCategoryModal"
        class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8] transition-colors"
      >
        <i class="fas fa-plus mr-2"></i>
        Nova Categoria
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-xl shadow-sm p-6 text-center">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#9810FA]"></div>
      <p class="mt-2 text-gray-600">Carregando categorias...</p>
    </div>

    <!-- Error Message -->
    <div v-else-if="error && categories.length === 0" class="bg-white rounded-xl shadow-sm p-6 text-center text-red-600">
      <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
      <p>{{ error }}</p>
      <button @click="fetchCategories" class="mt-2 px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]">
        Tentar novamente
      </button>
    </div>

    <!-- Warning Message for Virtual Categories -->
    <div v-if="error && categories.length > 0" class="mb-6 p-4 bg-yellow-50 text-yellow-700 rounded-lg">
      <p><i class="fas fa-exclamation-triangle mr-2"></i> {{ error }}</p>
    </div>

    <!-- Empty State -->
    <div v-else-if="categories.length === 0" class="bg-white rounded-xl shadow-sm p-12 text-center">
      <i class="fas fa-folder-open text-gray-400 text-5xl mb-4"></i>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhuma categoria encontrada</h3>
      <p class="text-gray-600 mb-4">Comece adicionando uma nova categoria para organizar seus produtos.</p>
      <button 
        @click="openNewCategoryModal"
        class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8]"
      >
        <i class="fas fa-plus mr-2"></i>
        Adicionar Categoria
      </button>
    </div>

    <!-- Lista de Categorias -->
    <div v-else class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="category in categories" :key="category.id" :class="{'bg-yellow-50': category.isVirtual}">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-10 w-10 flex-shrink-0">
                    <img 
                      :src="category.image_url || '/images/product-placeholder.jpg'" 
                      :alt="category.name"
                      class="h-10 w-10 rounded-lg object-cover"
                    />
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 flex items-center">
                      {{ category.name }}
                      <span v-if="category.isVirtual" class="ml-2 text-xs font-medium px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">
                        Virtual
                      </span>
                    </div>
                    <div class="text-sm text-gray-500">{{ category.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ category.slug }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span 
                  :class="[
                    'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                    category.active 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]"
                >
                  {{ category.active ? 'Ativa' : 'Inativa' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <button 
                  @click="editCategory(category)"
                  class="text-[#9810FA] hover:text-[#7a0dc8] mr-3"
                >
                  <i class="fas fa-edit"></i>
                </button>
                <button 
                  v-if="!category.isVirtual"
                  @click="deleteCategory(category)"
                  class="text-red-600 hover:text-red-800"
                >
                  <i class="fas fa-trash"></i>
                </button>
                <button 
                  v-else
                  class="text-gray-400 cursor-not-allowed"
                  title="Categorias virtuais precisam ser salvas antes de serem excluídas"
                >
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de Categoria -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              {{ editingCategory ? 'Editar Categoria' : 'Nova Categoria' }}
            </h3>
            <button 
              @click="closeCategoryModal"
              class="text-gray-400 hover:text-gray-500"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
        
        <form @submit.prevent="saveCategory" class="p-6">
          <!-- Mensagem de erro -->
          <div v-if="formError" class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg">
            <p><i class="fas fa-exclamation-triangle mr-2"></i> {{ formError }}</p>
          </div>

          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Categoria</label>
              <input 
                type="text" 
                v-model="categoryForm.name"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                required
              >
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
              <textarea 
                v-model="categoryForm.description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
              ></textarea>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
              <input 
                type="text" 
                v-model="categoryForm.slug"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                required
              >
              <p class="mt-1 text-xs text-gray-500">URL amigável, ex: "sabonetes-naturais"</p>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select 
                v-model="categoryForm.active"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                required
              >
                <option :value="true">Ativa</option>
                <option :value="false">Inativa</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Imagem da Categoria</label>
              
              <!-- Preview da imagem selecionada -->
              <div v-if="imagePreview" class="mb-3">
                <img :src="imagePreview" alt="Preview" class="h-32 w-auto rounded-lg">
                <button 
                  type="button" 
                  @click="removeImage" 
                  class="mt-2 text-sm text-red-600 hover:text-red-800"
                >
                  Remover imagem
                </button>
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
              @click="closeCategoryModal"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              :disabled="formSubmitting"
            >
              Cancelar
            </button>
            <button 
              type="submit"
              class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8] disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
              :disabled="formSubmitting"
            >
              <span v-if="formSubmitting" class="mr-2">
                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
              </span>
              {{ formSubmitting ? 'Salvando...' : 'Salvar' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { categoryService, productService, type Category } from '@/services/api';

interface CategoryForm {
  name: string;
  slug: string;
  description: string;
  active: boolean;
  image?: File | undefined;
  id?: number;
  isVirtual?: boolean;
}

interface ExtendedCategory extends Category {
  isVirtual?: boolean;
}

// Estado
const categories = ref<ExtendedCategory[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const showCategoryModal = ref(false);
const editingCategory = ref<ExtendedCategory | null>(null);
const formError = ref<string | null>(null);
const formSubmitting = ref(false);
const imagePreview = ref<string | null>(null);
const imageFile = ref<File | null>(null);

const categoryForm = ref<CategoryForm>({
  name: '',
  slug: '',
  description: '',
  active: true,
});

// Carregar categorias ao montar o componente
onMounted(() => {
  fetchCategories();
});

// Buscar categorias da API
const fetchCategories = async () => {
  try {
    loading.value = true;
    error.value = null;
    const data = await categoryService.getAll();
    
    if (data.length === 0) {
      // Se não há categorias no banco, vamos buscar dos produtos
      await fetchCategoriesFromProducts();
    } else {
      categories.value = data;
    }
  } catch (err) {
    console.error('Erro ao carregar categorias:', err);
    // Tentar buscar a partir dos produtos em caso de erro
    await fetchCategoriesFromProducts();
  } finally {
    loading.value = false;
  }
};

// Extrair categorias a partir dos produtos
const fetchCategoriesFromProducts = async () => {
  try {
    const products = await productService.getAll();
    
    // Map para armazenar categorias únicas
    const uniqueCategories = new Map();
    
    // Função para obter o valor da categoria (string)
    const getCategoryValue = (category: string | Category): string => {
      if (typeof category === 'object' && category !== null) {
        return category.name;
      }
      return category as string;
    };
    
    // Extrair categorias únicas dos produtos
    products.forEach(product => {
      if (product.category) {
        const categoryName = getCategoryValue(product.category);
        
        if (!uniqueCategories.has(categoryName)) {
          // Criar "categoria virtual" a partir do produto
          uniqueCategories.set(categoryName, {
            id: -uniqueCategories.size - 1, // IDs negativos para categorias virtuais
            name: categoryName,
            slug: categoryName.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, ''),
            description: `Produtos de ${categoryName}`,
            image_url: null,
            active: true,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString(),
            isVirtual: true // Marcador para categorias não salvas no banco
          });
        }
      }
    });
    
    // Converter para array e ordenar
    categories.value = Array.from(uniqueCategories.values()).sort((a, b) => 
      a.name.localeCompare(b.name)
    );
    
    // Adicionar aviso se encontramos categorias virtuais
    if (categories.value.length > 0) {
      error.value = 'As categorias mostradas foram extraídas dos produtos mas não estão salvas no banco de dados. Salve-as para que fiquem permanentes.';
    }
  } catch (err) {
    console.error('Erro ao extrair categorias dos produtos:', err);
    error.value = 'Não foi possível carregar as categorias. Tente novamente mais tarde.';
    categories.value = [];
  }
};

// Abrir modal para nova categoria
const openNewCategoryModal = () => {
  editingCategory.value = null;
  categoryForm.value = {
    name: '',
    slug: '',
    description: '',
    active: true,
  };
  imagePreview.value = null;
  imageFile.value = null;
  formError.value = null;
  showCategoryModal.value = true;
};

// Fechar modal
const closeCategoryModal = () => {
  showCategoryModal.value = false;
  editingCategory.value = null;
};

// Lidar com upload de imagem
const handleImageUpload = (event: Event) => {
  const input = event.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    const file = input.files[0];
    imageFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

// Remover imagem selecionada
const removeImage = () => {
  imageFile.value = null;
  imagePreview.value = null;
};

// Adiciona função slugify
const slugify = (text: string): string => {
  return text
    .toString()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .toLowerCase()
    .trim()
    .replace(/\s+/g, '-')
    .replace(/[^\w-]+/g, '')
    .replace(/--+/g, '-');
};

// Salvar categoria
const saveCategory = async () => {
  if (categoryForm.value) {
    // Para novas categorias ou categorias virtuais sendo salvas
    if (!categoryForm.value.id || (categoryForm.value.id && categoryForm.value.isVirtual)) {
      const newId = categoryForm.value.id || Math.max(...categories.value.map(c => c.id), 0) + 1;
      const newCategory: Category = {
        id: newId,
        name: categoryForm.value.name,
        slug: categoryForm.value.slug || slugify(categoryForm.value.name),
        description: categoryForm.value.description,
        image_url: categoryForm.value.image ? URL.createObjectURL(categoryForm.value.image) : null,
        active: categoryForm.value.active,
        created_at: new Date().toISOString(),
        updated_at: new Date().toISOString(),
        isVirtual: false // Ao salvar, a categoria deixa de ser virtual
      };

      if (!categoryForm.value.id) {
        // Nova categoria
        categories.value.push(newCategory);
      } else {
        // Atualizando categoria virtual
        const index = categories.value.findIndex(c => c.id === categoryForm.value?.id);
        if (index !== -1) {
          categories.value[index] = newCategory;
        }
      }
    } else {
      // Atualizando categoria existente
      const index = categories.value.findIndex(c => c.id === categoryForm.value?.id);
      if (index !== -1) {
        categories.value[index] = {
          ...categories.value[index],
          name: categoryForm.value.name,
          slug: categoryForm.value.slug || slugify(categoryForm.value.name),
          description: categoryForm.value.description,
          image_url: imageFile.value ? URL.createObjectURL(imageFile.value) : categories.value[index].image_url,
          active: categoryForm.value.active,
          updated_at: new Date().toISOString()
        };
      }
    }
    closeCategoryModal();
  }
};

// Editar categoria existente
const editCategory = (category: ExtendedCategory) => {
  editingCategory.value = category;
  categoryForm.value = {
    name: category.name,
    slug: category.slug,
    description: category.description || '',
    active: category.active,
  };
  
  // Mostrar imagem se existir
  imagePreview.value = category.image_url || null;
  imageFile.value = null;
  
  formError.value = null;
  showCategoryModal.value = true;
};

// Excluir categoria
const deleteCategory = async (category: ExtendedCategory) => {
  if (!confirm(`Tem certeza que deseja excluir a categoria "${category.name}"?`)) {
    return;
  }
  
  try {
    loading.value = true;
    await categoryService.delete(category.id);
    
    // Remover da lista local
    categories.value = categories.value.filter(c => c.id !== category.id);
  } catch (err) {
    console.error('Erro ao excluir categoria:', err);
    error.value = 'Não foi possível excluir a categoria. Tente novamente mais tarde.';
  } finally {
    loading.value = false;
  }
};
</script> 