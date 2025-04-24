<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações de Frete
    |--------------------------------------------------------------------------
    |
    | Este arquivo contém as configurações relacionadas ao cálculo de frete.
    |
    */

    // CEP de origem padrão (CEP da loja)
    'cep_origem' => env('SHIPPING_CEP_ORIGEM', '01001000'),
    
    // Serviços de frete habilitados
    'servicos' => [
        'pac' => true,         // PAC
        'sedex' => true,       // SEDEX
        'sedex_10' => false,   // SEDEX 10
        'sedex_12' => false,   // SEDEX 12
        'sedex_hoje' => false, // SEDEX Hoje
    ],
    
    // Dimensões padrão para produtos sem dimensões especificadas (em cm)
    'dimensoes_padrao' => [
        'altura' => 16,
        'largura' => 11,
        'comprimento' => 11,
    ],
    
    // Peso padrão para produtos sem peso especificado (em kg)
    'peso_padrao' => 0.3,
    
    // Configurações para o serviço dos Correios
    'correios' => [
        'codigo_empresa' => env('CORREIOS_CODIGO', ''),
        'senha' => env('CORREIOS_SENHA', ''),
    ],
]; 