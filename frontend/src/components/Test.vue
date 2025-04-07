<template>
  <div class="test-container">
    <h2>Teste de API</h2>
    <button @click="testAPI" :disabled="loading">
      {{ loading ? 'Testando...' : 'Testar API' }}
    </button>
    <div v-if="result" class="result">
      <p>Mensagem: {{ result.message }}</p>
      <p>Timestamp: {{ result.timestamp }}</p>
    </div>
    <div v-if="error" class="error">
      <p>Erro: {{ error }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { testService } from '../services/api';

const loading = ref(false);
const result = ref<{ message: string; timestamp: string } | null>(null);
const error = ref<string | null>(null);

const testAPI = async () => {
  loading.value = true;
  error.value = null;
  result.value = null;

  try {
    result.value = await testService.test();
  } catch (e) {
    error.value = e instanceof Error ? e.message : 'Erro desconhecido';
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.test-container {
  max-width: 600px;
  margin: 2rem auto;
  padding: 1rem;
  text-align: center;
}

button {
  padding: 0.5rem 1rem;
  font-size: 1rem;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin: 1rem 0;
}

button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}

.result {
  margin-top: 1rem;
  padding: 1rem;
  background-color: #e8f5e9;
  border-radius: 4px;
}

.error {
  margin-top: 1rem;
  padding: 1rem;
  background-color: #ffebee;
  border-radius: 4px;
  color: #c62828;
}
</style> 