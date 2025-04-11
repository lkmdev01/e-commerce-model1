<template>
  <div class="space-y-6">
    <!-- Cabeçalho -->
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-gray-900">Configurações</h1>
      <button 
        @click="saveSettings"
        class="px-4 py-2 bg-[#9810FA] text-white rounded-lg hover:bg-[#7a0dc8] transition-colors"
      >
        <i class="fas fa-save mr-2"></i>
        Salvar Alterações
      </button>
    </div>

    <!-- Configurações -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <!-- Configurações Gerais -->
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Configurações Gerais</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nome da Loja</label>
            <input 
              type="text" 
              v-model="settings.storeName"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email de Contato</label>
            <input 
              type="email" 
              v-model="settings.contactEmail"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone de Contato</label>
            <input 
              type="tel" 
              v-model="settings.contactPhone"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
            <textarea 
              v-model="settings.address"
              rows="3"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            ></textarea>
          </div>
        </div>
      </div>

      <!-- Configurações de Pagamento -->
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Configurações de Pagamento</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Chave PIX</label>
            <input 
              type="text" 
              v-model="settings.pixKey"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Chave da API do Mercado Pago</label>
            <input 
              type="text" 
              v-model="settings.mercadopagoKey"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Chave da API do Stripe</label>
            <input 
              type="text" 
              v-model="settings.stripeKey"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>
        </div>
      </div>

      <!-- Configurações de Entrega -->
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Configurações de Entrega</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Valor do Frete Fixo</label>
            <input 
              type="number" 
              v-model="settings.fixedShipping"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">CEP de Origem</label>
            <input 
              type="text" 
              v-model="settings.originCep"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Chave da API dos Correios</label>
            <input 
              type="text" 
              v-model="settings.correiosKey"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>
        </div>
      </div>

      <!-- Configurações de Email -->
      <div class="bg-white p-6 rounded-lg shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Configurações de Email</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Servidor SMTP</label>
            <input 
              type="text" 
              v-model="settings.smtpServer"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Porta SMTP</label>
            <input 
              type="number" 
              v-model="settings.smtpPort"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email SMTP</label>
            <input 
              type="email" 
              v-model="settings.smtpEmail"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Senha SMTP</label>
            <input 
              type="password" 
              v-model="settings.smtpPassword"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';

interface Settings {
  // Configurações Gerais
  storeName: string;
  contactEmail: string;
  contactPhone: string;
  address: string;

  // Configurações de Pagamento
  pixKey: string;
  mercadopagoKey: string;
  stripeKey: string;

  // Configurações de Entrega
  fixedShipping: number;
  originCep: string;
  correiosKey: string;

  // Configurações de Email
  smtpServer: string;
  smtpPort: number;
  smtpEmail: string;
  smtpPassword: string;
}

// Estado
const settings = ref<Settings>({
  // Configurações Gerais
  storeName: 'Yasmim Sol',
  contactEmail: 'contato@yasmimsol.com',
  contactPhone: '(11) 99999-9999',
  address: 'Rua Exemplo, 123 - São Paulo, SP',

  // Configurações de Pagamento
  pixKey: '',
  mercadopagoKey: '',
  stripeKey: '',

  // Configurações de Entrega
  fixedShipping: 0,
  originCep: '',
  correiosKey: '',

  // Configurações de Email
  smtpServer: '',
  smtpPort: 587,
  smtpEmail: '',
  smtpPassword: ''
});

// Métodos
const saveSettings = () => {
  // Aqui você implementaria a lógica para salvar as configurações
  console.log('Configurações salvas:', settings.value);
  alert('Configurações salvas com sucesso!');
};
</script> 