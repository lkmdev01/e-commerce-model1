<template>
  <section class="py-30 border-t-1 border-zinc-200 bg-white">
    <div class="container mx-auto px-4">
      <h2 class="text-4xl font-bold mb-16">O QUE NOSSOS CLIENTES DIZEM</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
        <!-- Depoimento 1 -->
        <div class="flex flex-col">
          <div class="quote-mark text-[#556B2F]">"</div>
          <p class="text-gray-700 text-lg mb-8 leading-relaxed">
            Entrega rápida e excelente atendimento ao cliente. O produto superou minhas expectativas. Com certeza voltarei a comprar.
          </p>
          <div class="mt-auto">
            <div class="w-14 h-14 mb-3">
              <img 
                src="/images/person1.jpeg" 
                alt="Jennifer Lewis" 
                class="w-full h-full object-cover rounded-full"
              >
            </div>
            <h3 class="font-bold text-base text-gray-900">JENNIFER LEWIS</h3>
          </div>
        </div>

        <!-- Depoimento 2 -->
        <div class="flex flex-col">
          <div class="quote-mark text-[#556B2F]">"</div>
          <p class="text-gray-700 text-lg mb-8 leading-relaxed">
            Ótima experiência de usuário no site. Encontrei exatamente o que procurava a um ótimo preço. Vou definitivamente recomendar para meus amigos.
          </p>
          <div class="mt-auto">
            <div class="w-14 h-14 mb-3">
              <img 
                src="/images/person2.jpeg" 
                alt="Alicia Heart" 
                class="w-full h-full object-cover rounded-full"
              >
            </div>
            <h3 class="font-bold text-base text-gray-900">ALICIA HEART</h3>
          </div>
        </div>

        <!-- Depoimento 3 -->
        <div class="flex flex-col">
          <div class="quote-mark text-[#556B2F]">"</div>
          <p class="text-gray-700 text-lg mb-8 leading-relaxed">
            Obrigado pela excelente experiência de compra. Chegou rapidamente e estava exatamente como descrito. Certamente voltarei a comprar no futuro.
          </p>
          <div class="mt-auto">
            <div class="w-14 h-14 mb-3">
              <img 
                src="/images/person3.jpeg" 
                alt="Juan Carlos" 
                class="w-full h-full object-cover rounded-full"
              >
            </div>
            <h3 class="font-bold text-base text-gray-900">JUAN CARLOS</h3>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.container {
  max-width: 1200px;
}

.quote-mark {
  font-size: 3.5rem;
  line-height: 1;
  font-family: Georgia, serif;
}

p {
  font-size: 1.125rem;
  line-height: 1.75;
}
</style> 