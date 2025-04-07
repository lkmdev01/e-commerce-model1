<template>
  <section class="py-16 bg-white">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Secure Payment -->
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-[#9810FA] flex items-center justify-center">
            <i class="fas fa-lock text-white text-xl"></i>
          </div>
          <div>
            <h3 class="font-bold text-lg mb-1">PAGAMENTO SEGURO</h3>
            <p class="text-gray-600">Todos os nossos pagamentos são SSL seguros</p>
          </div>
        </div>

        <!-- Delivered with Care -->
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-[#9810FA] flex items-center justify-center">
            <i class="fas fa-box text-white text-xl"></i>
          </div>
          <div>
            <h3 class="font-bold text-lg mb-1">ENTREGA COM CUIDADO</h3>
            <p class="text-gray-600">Envio super rápido até sua porta</p>
          </div>
        </div>

        <!-- Excellent Service -->
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-full bg-[#9810FA] flex items-center justify-center">
            <i class="fas fa-headset text-white text-xl"></i>
          </div>
          <div>
            <h3 class="font-bold text-lg mb-1">EXCELENTE SERVIÇO</h3>
            <p class="text-gray-600">Chat ao vivo e suporte por telefone</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
// Componente usando Composition API
</script>

<style scoped>
.container {
  max-width: 1200px;
}
</style> 