<template>
  <div class="shipping-calculator">
    <h3 class="text-lg font-medium mb-4">Calcular Frete e Prazo</h3>
    
    <!-- Opções de entrada do CEP (abas) -->
    <div v-if="authStore.isAuthenticated && userAddresses.length > 0" class="mb-4">
      <div class="flex border-b border-gray-200">
        <button 
          @click="inputMode = 'saved'" 
          class="py-2 px-4 focus:outline-none"
          :class="inputMode === 'saved' ? 'bg-white text-gray-800 font-medium border-t border-l border-r rounded-t-md border-gray-300' : 'text-gray-500 hover:text-gray-700'"
        >
          Usar endereço salvo
        </button>
        <button 
          @click="inputMode = 'manual'" 
          class="py-2 px-4 focus:outline-none"
          :class="inputMode === 'manual' ? 'bg-white text-gray-800 font-medium border-t border-l border-r rounded-t-md border-gray-300' : 'text-gray-500 hover:text-gray-700'"
        >
          Digitar CEP
        </button>
      </div>
    </div>
    
    <!-- Seleção de endereços salvos -->
    <div v-if="(authStore.isAuthenticated && userAddresses.length > 0) && inputMode === 'saved'" class="mb-4">
      <div class="w-full mb-4">
        <select 
          v-model="selectedSavedAddress" 
          class="w-full p-2 border border-gray-300 rounded focus:ring-gray-500 focus:border-gray-500"
        >
          <option :value="null" disabled>Selecione um endereço</option>
          <option 
            v-for="(address, index) in userAddresses" 
            :key="index" 
            :value="address"
          >
            {{ address.name }} - {{ address.street }}, {{ address.number }}
          </option>
        </select>
      </div>
      
      <button 
        @click="useSelectedAddress" 
        class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded"
        :disabled="!selectedSavedAddress || isLoading"
      >
        <span v-if="isLoading && inputMode === 'saved'">
          <svg class="inline-block animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Calculando...
        </span>
        <span v-else>Calcular com este endereço</span>
      </button>
    </div>
    
    <!-- Input manual de CEP -->
    <div v-if="!authStore.isAuthenticated || !userAddresses.length || inputMode === 'manual'" class="mb-4">
      <div class="flex mb-4">
        <input 
          type="text" 
          v-model="cep" 
          placeholder="Digite seu CEP" 
          @input="formatCep"
          @blur="cepBlur"
          class="flex-1 p-2 border border-gray-300 rounded-l focus:ring-gray-500 focus:border-gray-500"
          :class="{ 'border-red-500': cepError }"
        />
        <button 
          @click="calcularFrete" 
          class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-r"
          :disabled="isLoading || cepError"
        >
          <span v-if="isLoading && inputMode === 'manual'">
            <svg class="inline-block animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          <span v-if="!isLoading || inputMode !== 'manual'" class="text-white">Calcular</span>
        </button>
      </div>
      <small v-if="cepError" class="text-red-500">{{ cepError }}</small>
    </div>

    <!-- Endereço encontrado -->
    <div v-if="endereco" class="mb-4 p-3 bg-gray-50 rounded-md">
      <h4 class="font-medium mb-1">Endereço de entrega:</h4>
      <p>{{ endereco.logradouro }}{{ endereco.numero ? ', ' + endereco.numero : '' }}</p>
      <p>{{ endereco.bairro }} - {{ endereco.localidade }}/{{ endereco.uf }}</p>
      <p>CEP: {{ endereco.cep }}</p>
    </div>

    <!-- Opções de frete -->
    <div v-if="freteOptions.length > 0" class="mt-4">
      <h4 class="font-medium mb-2">Opções de entrega:</h4>
      <div class="grid gap-3">
        <div 
          v-for="(option, index) in freteOptions" 
          :key="index" 
          class="border border-gray-200 rounded-md p-3 transition-colors hover:border-gray-300"
          :class="{'bg-gray-100 border-gray-400': selectedOption && selectedOption.codigo === option.codigo}"
          @click="selectShipping(option)"
        >
          <div class="flex justify-between items-center">
            <div>
              <h5 class="font-medium text-gray-900">{{ option.servico }}</h5>
              <p class="text-sm text-gray-600">Entrega em {{ option.prazo }}</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-lg text-gray-900">R$ {{ formatPrice(option.valor) }}</p>
              <button 
                class="mt-1 text-sm bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded transition-colors"
                :class="{'bg-gray-700': selectedOption && selectedOption.codigo === option.codigo}"
              >
                {{ selectedOption && selectedOption.codigo === option.codigo ? 'Selecionado' : 'Selecionar' }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mensagem de erro -->
    <div v-if="showError" class="mt-4 p-3 bg-red-50 text-red-700 rounded border border-red-200">
      <p class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
        </svg>
        {{ errorMessage }}
      </p>
    </div>
  </div>
</template>

<script>
import shippingService from '@/services/shipping';
import addressService from '@/services/address';
import { useCartStore } from '@/stores/cartStore';
import { useAuthStore } from '@/stores/auth';

export default {
  name: 'ShippingCalculator',
  
  props: {
    /**
     * Itens do carrinho para cálculo do frete
     */
    items: {
      type: Array,
      required: true
    },
    
    /**
     * CEP de origem (da loja) - opcional, se não for fornecido, busca do backend
     */
    cepOrigem: {
      type: String,
      default: null
    },
    
    /**
     * Valor total do pedido (opcional)
     */
    totalValue: {
      type: Number,
      default: 0
    },
    
    /**
     * Usar o CartStore para gerenciar o estado do frete (opcional)
     */
    useCartStore: {
      type: Boolean,
      default: false
    }
  },
  
  data() {
    return {
      inputMode: 'saved', // 'saved' ou 'manual'
      cep: '',
      cepError: '',
      isLoading: false,
      endereco: null,
      freteOptions: [],
      showError: false,
      errorMessage: '',
      selectedOption: null,
      lojaOrigemCep: null,
      cartStore: null,
      authStore: null,
      userAddresses: [],
      selectedSavedAddress: null
    };
  },
  
  async created() {
    // Inicializar o auth store
    this.authStore = useAuthStore();
    
    // Se useCartStore estiver ativado, use o store para gerenciar os dados
    if (this.useCartStore) {
      this.cartStore = useCartStore();
      
      // Se houver um endereço já salvo no store, use-o
      if (this.cartStore.shippingAddress) {
        this.cep = this.cartStore.shippingAddress.cep;
        this.endereco = this.cartStore.shippingAddress;
        
        // Se já tinha calculado o frete antes, mostre as opções novamente
        if (this.cartStore.selectedShipping) {
          this.selectedOption = {
            servico: this.cartStore.selectedShipping.service,
            valor: this.cartStore.selectedShipping.price,
            prazo: this.cartStore.selectedShipping.days,
            codigo: this.cartStore.selectedShipping.code
          };
          this.calcularFrete();
        }
      }
    }
    
    // Se não tiver CEP de origem definido nas props, busca do backend
    if (!this.cepOrigem) {
      try {
        this.lojaOrigemCep = await shippingService.obterCepLoja();
      } catch (error) {
        console.error('Erro ao buscar CEP da loja:', error);
        this.lojaOrigemCep = '01001000'; // CEP padrão caso ocorra erro
      }
    }
    
    // Busca os endereços salvos do usuário se estiver autenticado
    if (this.authStore.isAuthenticated) {
      await this.loadUserAddresses();
      
      // Se não tem endereços salvos, muda para modo manual
      if (!this.userAddresses.length) {
        this.inputMode = 'manual';
      }
    } else {
      this.inputMode = 'manual';
    }
  },
  
  computed: {
    // Retorna o CEP de origem a ser usado, da prop ou do backend
    cepOrigemFinal() {
      return this.cepOrigem || this.lojaOrigemCep || '01001000';
    }
  },
  
  methods: {
    /**
     * Carrega os endereços salvos do usuário
     */
    async loadUserAddresses() {
      try {
        this.userAddresses = await addressService.getAddresses();
        
        // Seleciona o endereço padrão, se existir
        const defaultAddress = this.userAddresses.find(addr => addr.is_default);
        if (defaultAddress) {
          this.selectedSavedAddress = defaultAddress;
        } else if (this.userAddresses.length > 0) {
          this.selectedSavedAddress = this.userAddresses[0];
        }
      } catch (error) {
        console.error('Erro ao carregar endereços do usuário:', error);
        this.userAddresses = [];
        this.inputMode = 'manual';
      }
    },
    
    /**
     * Usa o endereço selecionado para calcular o frete
     */
    useSelectedAddress() {
      if (!this.selectedSavedAddress) return;
      
      this.isLoading = true;
      
      // Formata o endereço para o formato do componente
      const formattedAddress = addressService.formatAddressForShipping(this.selectedSavedAddress);
      
      // Atualiza os dados
      this.endereco = formattedAddress;
      this.cep = formattedAddress.cep;
      
      // Se estiver usando o CartStore, salva o endereço
      if (this.cartStore) {
        this.cartStore.setShippingAddress(formattedAddress);
      }
      
      // Calcula o frete
      this.calcularFrete();
    },
    
    /**
     * Formata o CEP durante a digitação
     */
    formatCep() {
      // Remove caracteres não numéricos
      const numericValue = this.cep.replace(/\D/g, '');
      
      // Formata como 00000-000
      if (numericValue.length <= 5) {
        this.cep = numericValue;
      } else {
        this.cep = `${numericValue.slice(0, 5)}-${numericValue.slice(5, 8)}`;
      }
      
      // Limpa erro se o CEP tiver o formato correto
      if (numericValue.length === 8) {
        this.cepError = '';
      }
    },
    
    /**
     * Valida o CEP quando o campo perde o foco
     */
    cepBlur() {
      const numericCep = this.cep.replace(/\D/g, '');
      if (numericCep.length !== 8) {
        this.cepError = 'CEP inválido. Deve conter 8 dígitos.';
      } else {
        this.cepError = '';
      }
    },
    
    /**
     * Realiza o cálculo do frete
     */
    async calcularFrete() {
      // Valida o CEP antes de prosseguir
      const numericCep = this.cep.replace(/\D/g, '');
      if (numericCep.length !== 8) {
        this.cepError = 'CEP inválido. Deve conter 8 dígitos.';
        this.isLoading = false;
        return;
      }
      
      this.isLoading = true;
      this.showError = false;
      this.freteOptions = [];
      
      try {
        // Primeiro, consulta o CEP se não estiver usando um endereço salvo
        if (!this.endereco || this.endereco.cep !== this.cep) {
          const cepResponse = await shippingService.consultarCep(this.cep);
          this.endereco = cepResponse.endereco;
        }
        
        // Se estiver usando o CartStore, salva o endereço
        if (this.cartStore) {
          this.cartStore.setShippingAddress({
            cep: this.cep,
            ...this.endereco
          });
        }
        
        // Prepara os itens para o cálculo de frete
        const itensFormatados = this.items.map(item => ({
          largura: item.width || 11,
          altura: item.height || 16,
          comprimento: item.length || 11,
          peso: item.weight || 0.3,
          quantidade: item.quantity || 1
        }));
        
        // Calcula o frete
        const freteResponse = await shippingService.calcularFrete({
          cep_origem: this.cepOrigemFinal.replace(/\D/g, ''),
          cep_destino: numericCep,
          itens: itensFormatados
        });
        
        this.freteOptions = freteResponse.opcoes_frete;
      } catch (error) {
        this.showError = true;
        this.errorMessage = 
          error.response?.data?.message || 
          'Erro ao calcular o frete. Verifique o CEP e tente novamente.';
      } finally {
        this.isLoading = false;
      }
    },
    
    /**
     * Formata o preço
     */
    formatPrice(value) {
      return Number(value).toFixed(2).replace('.', ',');
    },
    
    /**
     * Seleciona uma opção de frete
     */
    selectShipping(option) {
      this.selectedOption = option;
      
      // Emite evento para o componente pai
      this.$emit('shipping-selected', {
        service: option.servico,
        price: Number(option.valor),
        days: option.prazo,
        code: option.codigo
      });
      
      // Se estiver usando o CartStore, armazena a opção selecionada
      if (this.cartStore) {
        this.cartStore.setShipping({
          service: option.servico,
          price: Number(option.valor),
          days: option.prazo,
          code: option.codigo
        });
      }
    }
  }
};
</script>

<style scoped>
.shipping-calculator {
  border: 1px solid #e5e7eb;
  border-radius: 0.5rem;
  padding: 1.25rem;
  margin-bottom: 1rem;
  background-color: white;
}
</style> 