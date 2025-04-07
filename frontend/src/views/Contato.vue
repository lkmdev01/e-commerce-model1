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
    
    <section class="contact-section bg-white">
      <div class="container mx-auto px-4 py-16">
        <div class="max-w-5xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Informações de Contato -->
            <div class="bg-gray-50 p-8 rounded-lg">
              <h2 class="text-3xl font-bold mb-8 text-gray-800">Informações de Contato</h2>
              <div class="space-y-6">
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-full bg-[#9810FA] flex items-center justify-center mr-4">
                    <i class="fas fa-envelope text-white text-xl"></i>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-800">E-mail</h3>
                    <p class="text-gray-600">contato@yasmimsol.com</p>
                  </div>
                </div>
                
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-full bg-[#9810FA] flex items-center justify-center mr-4">
                    <i class="fas fa-phone text-white text-xl"></i>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-800">Telefone</h3>
                    <p class="text-gray-600">(11) 99999-9999</p>
                  </div>
                </div>
                
                <div class="flex items-center">
                  <div class="w-12 h-12 rounded-full bg-[#9810FA] flex items-center justify-center mr-4">
                    <i class="fas fa-map-marker-alt text-white text-xl"></i>
                  </div>
                  <div>
                    <h3 class="font-semibold text-gray-800">Endereço</h3>
                    <p class="text-gray-600">São Paulo, SP - Brasil</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Formulário de Contato -->
            <div>
              <h2 class="text-3xl font-bold mb-8 text-gray-800">Envie uma Mensagem</h2>
              <form @submit.prevent="handleSubmit" class="space-y-6">
                <div>
                  <label for="name" class="block text-gray-700 mb-2">Nome</label>
                  <input 
                    type="text" 
                    id="name" 
                    v-model="form.name" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                    required
                  >
                </div>
                
                <div>
                  <label for="email" class="block text-gray-700 mb-2">E-mail</label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="form.email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                    required
                  >
                </div>
                
                <div>
                  <label for="subject" class="block text-gray-700 mb-2">Assunto</label>
                  <input 
                    type="text" 
                    id="subject" 
                    v-model="form.subject" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                    required
                  >
                </div>
                
                <div>
                  <label for="message" class="block text-gray-700 mb-2">Mensagem</label>
                  <textarea 
                    id="message" 
                    v-model="form.message" 
                    rows="5" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#9810FA] focus:border-transparent"
                    required
                  ></textarea>
                </div>
                
                <button 
                  type="submit" 
                  class="w-full bg-[#9810FA] text-white py-3 px-6 rounded-md hover:bg-[#8609d8] transition-colors duration-300"
                >
                  Enviar Mensagem
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'

const form = reactive({
  name: '',
  email: '',
  subject: '',
  message: ''
})

const handleSubmit = () => {
  // Implementar lógica de envio do formulário
  console.log('Form submitted:', form)
}
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