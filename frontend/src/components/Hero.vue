<template>
  <section class="relative hero flex items-center justify-center overflow-hidden">
    <div class="container px-4 py-56 relative z-10 text-center">
      <h1 class="text-6xl md:text-8xl font-bold mb-6">YASMIM SOL</h1>
      <h2 class="text-2xl md:text-3xl mb-8">NOVIDADES NA LOJA!</h2>
      <router-link 
        to="/comprar" 
        class="inline-block cta-button rounded-sm transition-colors"
      >
        COMPRAR AGORA
      </router-link>
    </div>
  </section>
</template>

<script setup lang="ts">
// Componente usando Composition API
</script>

<style scoped>
.hero {
  background-image: url(/images/bg-home.jpg	);
  background-position: center right;
  background-repeat: no-repeat;
  background-size: cover;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.3));
}

h1 {
  font-size: 4rem;
  font-weight: bold;
  margin-bottom: 1rem;
  color: #8B008B;
}

p {
  font-size: 1.5rem;
  margin-bottom: 2rem;
}

.cta-button {
  display: inline-block;
  padding: 0.7rem 2rem;
  background-color: #8B008B;
  color: white;
  text-decoration: none;
  transition: background-color 0.3s ease;
}

.cta-button:hover {
  background-color: #6a006a;
}
</style> 