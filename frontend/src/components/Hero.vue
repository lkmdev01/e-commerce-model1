<template>
  <section class="relative hero flex items-center justify-center overflow-hidden">
    <div class="container px-4 py-20 md:py-56 relative z-10 text-center">
      <h1 class="font-bold mb-5">LOJA VIRTUAL</h1>
      <h2 class="text-xl sm:text-2xl md:text-3xl mb-6 md:mb-5">NOVIDADES NA LOJA!</h2>
      <router-link 
        to="/comprar" 
        class="inline-block cta-button rounded-sm transition-colors text-sm sm:text-base px-4 py-2 md:px-6 md:py-3 mt-3"
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
  background-image: url(/images/bg-home.jpg);
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
  min-height: 60vh;
}

@media (min-width: 768px) {
  .hero {
    min-height: 80vh;
  }
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
  color: #8B008B;
  font-size: 80px;
  text-transform: uppercase;
  line-height: 1em;
  letter-spacing: 10px;
}

h2 {
  color: #050505;
  font-size: 40px;
  font-weight: 300;
  text-transform: uppercase;
  letter-spacing: 6.3px;
}

.cta-button {
  background-color: #8B008B;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.cta-button:hover {
  background-color: #6a006a;
  transform: translateY(-2px);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}
</style> 