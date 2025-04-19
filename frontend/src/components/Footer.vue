<template>
  <footer class="py-8 bg-white border-t border-gray-200">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
        <!-- Menu de Navegação -->
        <nav class="w-full md:w-auto">
          <ul class="flex flex-wrap justify-center md:justify-start gap-4 md:gap-6">
            <li>
              <router-link to="/" class="text-gray-600 hover:text-gray-900 text-sm md:text-base">Home</router-link>
            </li>
            <li>
              <router-link to="/sobre" class="text-gray-600 hover:text-gray-900 text-sm md:text-base">Sobre</router-link>
            </li>
            <li>
              <router-link to="/comprar" class="text-gray-600 hover:text-gray-900 text-sm md:text-base">Loja</router-link>
            </li>
            <li>
              <router-link to="/contato" class="text-gray-600 hover:text-gray-900 text-sm md:text-base">Contato</router-link>
            </li>
          </ul>
        </nav>

        <!-- Logo -->
        <div class="text-xl md:text-2xl font-bold text-gray-900">
          LOJA VIRTUAL
        </div>

        <!-- Copyright -->
        <div class="text-gray-600 text-sm md:text-base text-center md:text-left">
          Copyright © 2024 Loja Virtual
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
}

@media (max-width: 768px) {
  .container {
    padding: 0 1rem;
  }
}
</style> 