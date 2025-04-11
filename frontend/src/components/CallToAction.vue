<template>
  <section class="relative py-32 overflow-hidden">
    <div class="absolute inset-0">
      <img 
        src="/images/Call-to-action.jpg"
        alt="Montanhas"
        class="w-full h-full object-cover bg-bottom"
      >
      <div class="absolute inset-0 bg-sky-100/80"></div>
    </div>
    
    <div class="relative container mx-auto px-4 text-center">
      <h2 class="text-4xl md:text-5xl font-bold mb-4 tracking-wide">
        DÊ O PRESENTE DE UM POSTAL
      </h2>
      <p class="text-lg md:text-xl mb-8 text-gray-700">
        Dê o presente de uma memória duradoura com um postal
      </p>
      <router-link 
        to="/comprar" 
        class="inline-block bg-[#9810FA] text-white px-8 py-3 font-semibold hover:bg-[#7a9e48] transition-colors duration-300"
      >
        COMPRAR UM POSTAL
      </router-link>
    </div>
  </section>
</template>

<style scoped>
.container {
  max-width: 1200px;
}
</style> 