<template>
  <div class="sobre">
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
        <h1 class="text-5xl font-bold text-[#9810FA] mb-4">Sobre a Yasmim Sol</h1>
        <p class="text-xl text-gray-700 text-center">Conheça nossa história e compromisso com produtos naturais</p>
      </div>
    </section>
    
    <section class="content-section">
      <div class="container mx-auto px-4 py-16">
        <div class="max-w-3xl mx-auto">
          <div class="mb-12">
            <h2 class="text-3xl font-bold mb-4 text-gray-800">Nossa História</h2>
            <p class="text-gray-600 leading-relaxed">
              A Yasmim Sol nasceu do amor pela natureza e do desejo de criar produtos que harmonizam beleza e sustentabilidade. Cada produto é cuidadosamente elaborado para trazer o melhor da natureza para seu dia a dia.
            </p>
          </div>
          
          <div class="mb-12">
            <h2 class="text-3xl font-bold mb-4 text-gray-800">Nossa Missão</h2>
            <p class="text-gray-600 leading-relaxed">
              Desenvolver produtos naturais de alta qualidade que promovam bem-estar e consciência ambiental, sempre respeitando a natureza e valorizando práticas sustentáveis.
            </p>
          </div>
          
          <div>
            <h2 class="text-3xl font-bold mb-4 text-gray-800">Nossos Valores</h2>
            <ul class="space-y-3 text-gray-600">
              <li class="flex items-center">
                <span class="w-2 h-2 bg-[#9810FA] rounded-full mr-3"></span>
                Compromisso com a natureza
              </li>
              <li class="flex items-center">
                <span class="w-2 h-2 bg-[#9810FA] rounded-full mr-3"></span>
                Qualidade em cada detalhe
              </li>
              <li class="flex items-center">
                <span class="w-2 h-2 bg-[#9810FA] rounded-full mr-3"></span>
                Sustentabilidade em primeiro lugar
              </li>
              <li class="flex items-center">
                <span class="w-2 h-2 bg-[#9810FA] rounded-full mr-3"></span>
                Transparência com nossos clientes
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>
    
    <Features />
  </div>
</template>

<script setup lang="ts">
import Features from '../components/Features.vue'
</script>

<style scoped>
.sobre {
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

h1 {
  font-size: 3rem;
  color: #8B008B;
  margin-bottom: 1rem;
}

.hero-section p {
  font-size: 1.2rem;
  color: #666;
  max-width: 600px;
  margin: 0 auto;
}

.content-section {
  padding: 4rem 0;
}

.text-content {
  max-width: 800px;
}

h2 {
  color: #333;
  margin: 2rem 0 1rem;
}

p {
  color: #666;
  line-height: 1.8;
  margin-bottom: 1.5rem;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  color: #666;
  margin-bottom: 0.5rem;
  position: relative;
}
</style> 