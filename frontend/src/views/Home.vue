<template>
  <div class="home">
    <Hero />
    <ProductGrid />
    <Testimonials />
    <CallToAction />
    <Features />
  </div>
</template>

<script setup lang="ts">
import Hero from '@/components/Hero.vue'
import ProductGrid from '@/components/ProductGrid.vue'
import Testimonials from '@/components/Testimonials.vue'
import CallToAction from '@/components/CallToAction.vue'
import Features from '@/components/Features.vue'
</script>

<style scoped>
.home {
  min-height: 100vh;
}
</style> 