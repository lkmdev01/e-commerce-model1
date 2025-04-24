<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Sprained\Correios\Frete;
use Sprained\Correios\Service;

class ShippingController extends Controller
{
    /**
     * Calcula o frete e prazo de entrega com base no CEP.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateShipping(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cep_origem' => 'sometimes|string|size:8',
            'cep_destino' => 'required|string|size:8',
            'itens' => 'required|array',
            'itens.*.largura' => 'required|numeric|min:1|max:100',
            'itens.*.altura' => 'required|numeric|min:1|max:100',
            'itens.*.comprimento' => 'required|numeric|min:1|max:100',
            'itens.*.peso' => 'required|numeric|min:0.1|max:30',
            'itens.*.quantidade' => 'sometimes|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos para o cálculo de frete',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Usa o CEP de origem da configuração se não for fornecido
            $cepOrigem = $request->cep_origem ?? Config::get('shipping.cep_origem');
            $cepOrigem = preg_replace('/[^0-9]/', '', $cepOrigem);
            
            $cepDestino = preg_replace('/[^0-9]/', '', $request->cep_destino);
            
            // Debug de valores
            Log::info('Iniciando cálculo de frete', [
                'cep_origem' => $cepOrigem,
                'cep_destino' => $cepDestino,
                'itens' => $request->itens
            ]);
            
            // Preparar itens para envio
            $items = [];
            foreach ($request->itens as $item) {
                $quantidade = isset($item['quantidade']) ? (int) $item['quantidade'] : 1;
                for ($i = 0; $i < $quantidade; $i++) {
                    $items[] = [
                        $item['largura'],
                        $item['altura'],
                        $item['comprimento'],
                        $item['peso']
                    ];
                }
            }
            
            Log::info('Itens formatados', ['items' => $items]);

            // Configuração dos serviços habilitados
            $servicos = [];
            if (Config::get('shipping.servicos.pac')) {
                $servicos[] = Service::PAC;
            }
            if (Config::get('shipping.servicos.sedex')) {
                $servicos[] = Service::SEDEX;
            }
            if (Config::get('shipping.servicos.sedex_10')) {
                $servicos[] = Service::SEDEX_10;
            }
            if (Config::get('shipping.servicos.sedex_12')) {
                $servicos[] = Service::SEDEX_12;
            }
            if (Config::get('shipping.servicos.sedex_hoje')) {
                $servicos[] = Service::SEDEX_HOJE;
            }
            
            Log::info('Serviços habilitados', ['servicos' => $servicos]);

            // Para demonstração, usaremos diretamente a versão de fallback
            // Em produção, removeria esta linha e usaria a API real dos Correios
            $useFallback = true;
            
            if ($useFallback) {
                Log::info('Usando valores de fallback para o cálculo de frete');
                $opcoesFrete = $this->getFallbackShippingOptions($servicos, $items);
                
                return response()->json([
                    'message' => 'Cálculo de frete realizado com sucesso (valores aproximados)',
                    'opcoes_frete' => $opcoesFrete
                ]);
            }
            
            try {
                // Esta parte só seria executada se $useFallback fosse false
                $frete = new Frete();
                
                // Inicializa a consulta de frete com os parâmetros
                $freteQuery = $frete->origem($cepOrigem)
                    ->destino($cepDestino)
                    ->servico(...$servicos)
                    ->items($items);

                // Verifica se há credenciais dos Correios configuradas
                $codigoEmpresa = Config::get('shipping.correios.codigo_empresa');
                $senha = Config::get('shipping.correios.senha');

                if (!empty($codigoEmpresa) && !empty($senha)) {
                    $freteQuery->credenciais($codigoEmpresa, $senha);
                }

                // Tenta calcular o frete através da API
                Log::info('Executando cálculo de frete');
                
                try {
                    $result = $freteQuery->calculo();
                    Log::info('Resultado do cálculo', ['result' => $result]);
                    
                    // Formatar resposta
                    $opcoesFrete = [];
                    foreach ($result as $option) {
                        // Verifica se retornou um erro
                        if (isset($option['code']) && isset($option['messagge'])) {
                            Log::warning('Erro no cálculo de frete (Correios API)', [
                                'code' => $option['code'],
                                'message' => $option['messagge']
                            ]);
                            continue;
                        }
                        
                        $opcoesFrete[] = [
                            'servico' => $this->getServiceName($option['codigo']),
                            'codigo' => $option['codigo'],
                            'valor' => $option['valor'],
                            'prazo' => $option['prazo'],
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao chamar a API dos Correios', [
                        'message' => $e->getMessage()
                    ]);
                    
                    // Se ocorrer erro na API, use valores de fallback
                    $opcoesFrete = $this->getFallbackShippingOptions($servicos, $items);
                }
                
                // Se após tentativas não conseguirmos obter opções, use valores de fallback
                if (empty($opcoesFrete)) {
                    $opcoesFrete = $this->getFallbackShippingOptions($servicos, $items);
                }

                return response()->json([
                    'message' => 'Cálculo de frete realizado com sucesso',
                    'opcoes_frete' => $opcoesFrete
                ]);
            } catch (\Exception $e) {
                Log::error('Erro interno no cálculo de frete', [
                    'mensagem' => $e->getMessage(),
                    'linha' => $e->getLine(),
                    'arquivo' => $e->getFile(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Se ocorrer erro na API, use valores de fallback
                $opcoesFrete = $this->getFallbackShippingOptions($servicos, $items);
                
                return response()->json([
                    'message' => 'Cálculo de frete realizado com sucesso (valores aproximados)',
                    'opcoes_frete' => $opcoesFrete
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao calcular o frete',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Gera valores de fallback para o frete quando a API dos Correios falha.
     * Estes são valores aproximados para demonstração.
     * 
     * @param array $servicos Serviços selecionados
     * @param array $items Itens para cálculo
     * @return array
     */
    private function getFallbackShippingOptions($servicos, $items)
    {
        $opcoes = [];
        $pesoTotal = 0;
        
        // Calcula o peso total dos itens
        foreach ($items as $item) {
            $pesoTotal += $item[3]; // O peso está na posição 3 do array
        }
        
        // Cria valores de fallback para cada serviço
        foreach ($servicos as $servico) {
            $valorBase = 0;
            $prazo = '';
            
            switch ($servico) {
                case Service::PAC:
                    $valorBase = 20.00 + ($pesoTotal * 3);
                    $prazo = '5 a 8 Dias';
                    break;
                case Service::SEDEX:
                    $valorBase = 35.00 + ($pesoTotal * 5);
                    $prazo = '1 a 3 Dias';
                    break;
                case Service::SEDEX_10:
                    $valorBase = 60.00 + ($pesoTotal * 6);
                    $prazo = '1 Dia';
                    break;
                case Service::SEDEX_12:
                    $valorBase = 50.00 + ($pesoTotal * 5.5);
                    $prazo = '1 Dia';
                    break;
                case Service::SEDEX_HOJE:
                    $valorBase = 80.00 + ($pesoTotal * 7);
                    $prazo = 'Mesmo Dia';
                    break;
                default:
                    $valorBase = 30.00 + ($pesoTotal * 4);
                    $prazo = '3 a 5 Dias';
            }
            
            $opcoes[] = [
                'servico' => $this->getServiceName($servico),
                'codigo' => $servico,
                'valor' => number_format($valorBase, 2, '.', ''),
                'prazo' => $prazo,
            ];
        }
        
        return $opcoes;
    }

    /**
     * Consulta CEP para endereço.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchCep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cep' => 'required|string|min:8|max:9',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'CEP inválido',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $cep = preg_replace('/[^0-9]/', '', $request->cep);
            
            $cepService = new \Sprained\Correios\Cep();
            $endereco = $cepService->cep($cep);

            return response()->json([
                'message' => 'CEP consultado com sucesso',
                'endereco' => $endereco
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao consultar o CEP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna o CEP de origem da loja.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getShopCep()
    {
        return response()->json([
            'cep' => Config::get('shipping.cep_origem')
        ]);
    }

    /**
     * Retorna o nome do serviço com base no código.
     *
     * @param int $code
     * @return string
     */
    private function getServiceName($code)
    {
        $services = [
            Service::PAC => 'PAC',
            Service::SEDEX => 'SEDEX',
            Service::SEDEX_10 => 'SEDEX 10',
            Service::SEDEX_12 => 'SEDEX 12',
            Service::SEDEX_HOJE => 'SEDEX Hoje',
        ];

        return $services[$code] ?? 'Desconhecido';
    }
} 