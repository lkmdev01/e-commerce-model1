<template>
  <footer class="py-8 bg-white border-t border-gray-200">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <!-- Menu de Navegação -->
        <nav class="mb-4 md:mb-0">
          <ul class="flex space-x-6">
            <li>
              <router-link to="/" class="text-gray-600 hover:text-gray-900">Home</router-link>
            </li>
            <li>
              <router-link to="/sobre" class="text-gray-600 hover:text-gray-900">Sobre</router-link>
            </li>
            <li>
              <router-link to="/comprar" class="text-gray-600 hover:text-gray-900">Loja</router-link>
            </li>
            <li>
              <router-link to="/contato" class="text-gray-600 hover:text-gray-900">Contato</router-link>
            </li>
          </ul>
        </nav>

        <!-- Logo -->
        <div class="text-2xl font-bold text-gray-900 mb-4 md:mb-0">
          YASMIM SOL
        </div>

        <!-- Copyright -->
        <div class="text-gray-600">
          Copyright © 2024 Yasmim Sol
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.container {
  max-width: 1200px;
}
</style> 