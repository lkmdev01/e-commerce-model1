<template>
  <div class="api-test">
    <h2>Teste de Conexão com API</h2>
    <div v-if="loading">Carregando...</div>
    <div v-else-if="error" class="error">
      {{ error }}
    </div>
    <div v-else class="success">
      {{ message }}
    </div>
    <button @click="testConnection" :disabled="loading">
      Testar Conexão
    </button>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import api from '../services/api';

const loading = ref(false);
const error = ref('');
const message = ref('');

const testConnection = async () => {
  loading.value = true;
  error.value = '';
  message.value = '';

  try {
    const response = await api.get('/test');
    message.value = response.data.message;
  } catch (err) {
    error.value = 'Erro ao conectar com a API';
    console.error(err);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
.api-test {
  padding: 20px;
  max-width: 600px;
  margin: 0 auto;
}

.error {
  color: red;
  margin: 10px 0;
}

.success {
  color: green;
  margin: 10px 0;
}

button {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}
</style> 