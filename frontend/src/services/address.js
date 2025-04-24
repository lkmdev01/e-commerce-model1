import api from './api';

/**
 * Service para gerenciamento de endereços
 */
export default {
  /**
   * Obtém todos os endereços do usuário
   * @returns {Promise} - Promise com os endereços do usuário
   */
  async getAddresses() {
    try {
      const response = await api.get('/addresses');
      return response.data;
    } catch (error) {
      console.error('Erro ao buscar endereços:', error);
      throw error;
    }
  },

  /**
   * Converte um endereço do formato do backend para o formato do ShippingCalculator
   * @param {Object} address - Endereço no formato do backend
   * @returns {Object} - Endereço no formato do ShippingCalculator
   */
  formatAddressForShipping(address) {
    return {
      cep: address.zipcode,
      logradouro: address.street,
      numero: address.number,
      complemento: address.complement || '',
      bairro: address.district,
      localidade: address.city,
      uf: address.state
    };
  }
}; 