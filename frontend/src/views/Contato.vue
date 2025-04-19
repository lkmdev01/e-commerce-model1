<template>
  <div class="contato">
    <section class="hero-section relative">
      <div class="absolute inset-0">
        <img 
          src="/images/bg-home.jpg"
          alt="Background"
          class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-white/30"></div>
      </div>
      <div class="relative z-10 container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold text-[#9810FA] mb-4">Entre em Contato</h1>
        <p class="text-xl text-gray-700">Estamos aqui para ajudar</p>
      </div>
    </section>
    
    <section class="contact-section py-12 bg-white">
      <div class="container mx-auto px-4">
        <div class="flex flex-col lg:flex-row gap-12">
          <div class="lg:w-1/2">
            <h2 class="text-3xl font-bold mb-6">Entre em Contato</h2>
            <p class="text-gray-600 mb-6">
              Tem alguma dúvida ou precisa de informações? Preencha o formulário 
              e nossa equipe entrará em contato o mais breve possível.
            </p>
            
            <!-- Mensagem de sucesso -->
            <div v-if="successMessage" class="mb-6 p-4 bg-green-100 text-green-700 rounded-md">
              {{ successMessage }}
            </div>
            
            <!-- Mensagem de erro -->
            <div v-if="errorMessage" class="mb-6 p-4 bg-red-100 text-red-700 rounded-md">
              {{ errorMessage }}
            </div>
            
            <form @submit.prevent="handleSubmit" class="space-y-4">
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                <input 
                  type="text" 
                  id="name" 
                  v-model="form.name" 
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
              
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                  type="email" 
                  id="email" 
                  v-model="form.email" 
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
              
              <div>
                <label for="subject" class="block text-sm font-medium text-gray-700">Assunto</label>
                <input 
                  type="text" 
                  id="subject" 
                  v-model="form.subject" 
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                />
              </div>
              
              <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Mensagem</label>
                <textarea 
                  id="message" 
                  v-model="form.message" 
                  rows="4" 
                  required
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                ></textarea>
              </div>
              
              <div>
                <button 
                  type="submit" 
                  :disabled="loading"
                  class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                >
                  <span v-if="loading">Enviando...</span>
                  <span v-else>Enviar Mensagem</span>
                </button>
              </div>
            </form>
          </div>
          
          <div class="lg:w-1/2">
            <h2 class="text-3xl font-bold mb-6">Informações de Contato</h2>
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </div>
                <div class="ml-3 text-gray-600">
                  <p class="text-sm font-medium">Endereço</p>
                  <p>Av. Principal, 1000 - Centro</p>
                  <p>São Paulo - SP, 01000-000</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                  </svg>
                </div>
                <div class="ml-3 text-gray-600">
                  <p class="text-sm font-medium">Telefone</p>
                  <p>(11) 1234-5678</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                  </svg>
                </div>
                <div class="ml-3 text-gray-600">
                  <p class="text-sm font-medium">Email</p>
                  <p>contato@ecommercesol.com</p>
                </div>
              </div>
              
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <svg class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div class="ml-3 text-gray-600">
                  <p class="text-sm font-medium">Horário de Atendimento</p>
                  <p>Segunda a Sexta: 9h às 18h</p>
                  <p>Sábado: 9h às 13h</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { contactService } from '@/services/api';

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
});

const loading = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

const handleSubmit = async () => {
  loading.value = true;
  successMessage.value = '';
  errorMessage.value = '';
  
  try {
    const response = await contactService.send(form.value);
    successMessage.value = response.message || 'Mensagem enviada com sucesso!';
    // Limpar formulário após envio bem-sucedido
    form.value = {
      name: '',
      email: '',
      subject: '',
      message: ''
    };
  } catch (error: any) {
    console.error('Erro ao enviar mensagem:', error);
    errorMessage.value = error.response?.data?.message || 'Ocorreu um erro ao enviar sua mensagem. Tente novamente mais tarde.';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.contato {
  min-height: 100vh;
}

.hero-section {
  height: 50vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.container {
  max-width: 1200px;
}
</style> 