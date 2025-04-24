import api from './api';

/**
 * Service para cálculo de frete e consulta de CEP
 */
export default {
  /**
   * Consulta CEP e retorna o endereço correspondente
   * @param {string} cep - CEP para consulta (apenas números ou com hífen)
   * @returns {Promise} - Promise com o resultado da consulta
   */
  async consultarCep(cep) {
    try {
      const response = await api.post('/shipping/cep', { cep });
      return response.data;
    } catch (error) {
      console.error('Erro ao consultar CEP:', error);
      throw error;
    }
  },

  /**
   * Calcula o frete com base nos parâmetros enviados
   * @param {Object} params - Parâmetros para cálculo do frete
   * @param {string} params.cep_origem - CEP de origem (apenas números) - opcional, usa o da loja se não for fornecido
   * @param {string} params.cep_destino - CEP de destino (apenas números)
   * @param {Array} params.itens - Array de itens para cálculo do frete
   * @returns {Promise} - Promise com o resultado do cálculo
   */
  async calcularFrete(params) {
    try {
      const response = await api.post('/shipping/calculate', params);
      return response.data;
    } catch (error) {
      console.error('Erro ao calcular frete:', error);
      throw error;
    }
  },

  /**
   * Obtém o CEP de origem da loja
   * @returns {Promise} - Promise com o CEP da loja
   */
  async obterCepLoja() {
    try {
      const response = await api.get('/shipping/shop-cep');
      return response.data.cep;
    } catch (error) {
      console.error('Erro ao obter CEP da loja:', error);
      throw error;
    }
  }
}; 