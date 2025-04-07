<template>
  <div class="conta">
    <section class="account-section">
      <div class="container">
        <h1>Minha Conta</h1>
        
        <div class="account-content">
          <nav class="account-nav">
            <button 
              v-for="tab in tabs" 
              :key="tab.id"
              :class="['tab-btn', { active: activeTab === tab.id }]"
              @click="activeTab = tab.id"
            >
              <i :class="tab.icon"></i>
              {{ tab.name }}
            </button>
          </nav>
          
          <div class="tab-content">
            <!-- Perfil -->
            <div v-if="activeTab === 'profile'" class="profile-tab">
              <h2>Meu Perfil</h2>
              <form @submit.prevent="updateProfile" class="profile-form">
                <div class="form-group">
                  <label for="name">Nome</label>
                  <input type="text" id="name" v-model="profile.name">
                </div>
                
                <div class="form-group">
                  <label for="email">E-mail</label>
                  <input type="email" id="email" v-model="profile.email">
                </div>
                
                <div class="form-group">
                  <label for="phone">Telefone</label>
                  <input type="tel" id="phone" v-model="profile.phone">
                </div>
                
                <button type="submit" class="save-btn">Salvar Alterações</button>
              </form>
            </div>
            
            <!-- Pedidos -->
            <div v-if="activeTab === 'orders'" class="orders-tab">
              <h2>Meus Pedidos</h2>
              <div v-if="orders.length > 0" class="orders-list">
                <div v-for="order in orders" :key="order.id" class="order-item">
                  <div class="order-header">
                    <span class="order-number">Pedido #{{ order.id }}</span>
                    <span :class="['order-status', order.status]">{{ order.status }}</span>
                  </div>
                  <div class="order-details">
                    <p>Data: {{ order.date }}</p>
                    <p>Total: R$ {{ order.total.toFixed(2) }}</p>
                  </div>
                  <button class="view-order-btn">Ver Detalhes</button>
                </div>
              </div>
              <div v-else class="empty-orders">
                <p>Você ainda não tem pedidos</p>
              </div>
            </div>
            
            <!-- Endereços -->
            <div v-if="activeTab === 'addresses'" class="addresses-tab">
              <h2>Meus Endereços</h2>
              <div class="addresses-list">
                <div v-for="address in addresses" :key="address.id" class="address-card">
                  <div class="address-content">
                    <h3>{{ address.name }}</h3>
                    <p>{{ address.street }}, {{ address.number }}</p>
                    <p>{{ address.city }} - {{ address.state }}</p>
                    <p>CEP: {{ address.zipCode }}</p>
                  </div>
                  <div class="address-actions">
                    <button class="edit-btn">Editar</button>
                    <button class="delete-btn">Excluir</button>
                  </div>
                </div>
                <button class="add-address-btn">
                  <i class="fas fa-plus"></i>
                  Adicionar Novo Endereço
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'

const activeTab = ref('profile')

const tabs = [
  { id: 'profile', name: 'Perfil', icon: 'fas fa-user' },
  { id: 'orders', name: 'Pedidos', icon: 'fas fa-shopping-bag' },
  { id: 'addresses', name: 'Endereços', icon: 'fas fa-map-marker-alt' }
]

const profile = reactive({
  name: 'João Silva',
  email: 'joao@email.com',
  phone: '(11) 99999-9999'
})

const orders = ref([
  {
    id: 1,
    date: '2024-04-04',
    status: 'entregue',
    total: 150.00
  }
])

const addresses = ref([
  {
    id: 1,
    name: 'Casa',
    street: 'Rua das Flores',
    number: '123',
    city: 'São Paulo',
    state: 'SP',
    zipCode: '01234-567'
  }
])

const updateProfile = () => {
  // Implementar lógica de atualização do perfil
  console.log('Profile updated:', profile)
}
</script>

<style scoped>
.conta {
  min-height: 100vh;
  padding: 4rem 0;
  background-color: #f9f9f9;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

h1 {
  font-size: 2.5rem;
  color: #333;
  margin-bottom: 2rem;
}

.account-content {
  display: grid;
  grid-template-columns: 250px 1fr;
  gap: 2rem;
}

.account-nav {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.tab-btn {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.3s ease;
  text-align: left;
}

.tab-btn i {
  width: 20px;
}

.tab-btn.active {
  background: #8B008B;
  color: white;
}

.tab-content {
  background: white;
  border-radius: 8px;
  padding: 2rem;
}

h2 {
  color: #333;
  margin-bottom: 2rem;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  max-width: 500px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

label {
  color: #333;
  font-weight: 500;
}

input {
  padding: 0.8rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

.save-btn {
  padding: 1rem;
  background: #8B008B;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.save-btn:hover {
  background: #6a006a;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.order-item {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 1rem;
}

.order-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1rem;
}

.order-status {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.9rem;
}

.order-status.entregue {
  background: #e8f5e9;
  color: #2e7d32;
}

.view-order-btn {
  margin-top: 1rem;
  padding: 0.5rem 1rem;
  background: transparent;
  border: 1px solid #8B008B;
  color: #8B008B;
  border-radius: 4px;
  cursor: pointer;
}

.addresses-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1rem;
}

.address-card {
  border: 1px solid #ddd;
  border-radius: 4px;
  padding: 1rem;
}

.address-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1rem;
}

.edit-btn,
.delete-btn {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
}

.edit-btn {
  background: transparent;
  border: 1px solid #8B008B;
  color: #8B008B;
}

.delete-btn {
  background: transparent;
  border: 1px solid #ff4444;
  color: #ff4444;
}

.add-address-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 1rem;
  background: #f5f5f5;
  border: 2px dashed #ddd;
  border-radius: 4px;
  color: #666;
  cursor: pointer;
  transition: all 0.3s ease;
}

.add-address-btn:hover {
  border-color: #8B008B;
  color: #8B008B;
}

@media (max-width: 768px) {
  .account-content {
    grid-template-columns: 1fr;
  }
  
  .account-nav {
    flex-direction: row;
    overflow-x: auto;
  }
  
  .tab-btn {
    flex: 0 0 auto;
  }
}
</style> 