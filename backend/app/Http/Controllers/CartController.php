<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Carrega o carrinho do usuário autenticado.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function load(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Busca o carrinho do usuário com os itens relacionados
        $cart = $user->cart;
        
        if (!$cart) {
            // Se o usuário não tem carrinho, retorna um carrinho vazio
            return response()->json([
                'items' => [],
                'selectedShipping' => null,
                'shippingAddress' => null
            ]);
        }
        
        // Formata os itens do carrinho para o formato esperado pelo frontend
        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->product_id,
                'name' => $item->product_name,
                'price' => $item->price,
                'image' => $item->image,
                'quantity' => $item->quantity
            ];
        });
        
        return response()->json([
            'items' => $items,
            'selectedShipping' => $cart->shipping_option,
            'shippingAddress' => $cart->shipping_address
        ]);
    }
    
    /**
     * Salva o carrinho para o usuário autenticado.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required',
            'items.*.image' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'selectedShipping' => 'nullable',
            'shippingAddress' => 'nullable'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Registro de log para depuração
            Log::info('Salvando carrinho', [
                'user_id' => $user->id,
                'item_count' => count($data['items']),
                'items' => $data['items']
            ]);
            
            // Busca ou cria um novo carrinho para o usuário
            $cart = $user->cart;
            
            if (!$cart) {
                $cart = new Cart([
                    'user_id' => $user->id,
                    'shipping_option' => $data['selectedShipping'] ?? null,
                    'shipping_address' => $data['shippingAddress'] ?? null
                ]);
                $cart->save();
                
                Log::info('Criado novo carrinho', ['cart_id' => $cart->id]);
            } else {
                // Atualiza as opções de envio e endereço
                $cart->shipping_option = $data['selectedShipping'] ?? null;
                $cart->shipping_address = $data['shippingAddress'] ?? null;
                $cart->save();
                
                // Remove todos os itens existentes
                $oldItemCount = $cart->items()->count();
                $cart->items()->delete();
                
                Log::info('Atualizado carrinho existente', [
                    'cart_id' => $cart->id,
                    'old_item_count' => $oldItemCount
                ]);
            }
            
            // Adiciona os novos itens
            foreach ($data['items'] as $itemData) {
                $item = new CartItem([
                    'cart_id' => $cart->id,
                    'product_id' => $itemData['id'],
                    'product_name' => $itemData['name'],
                    'price' => $itemData['price'],
                    'image' => $itemData['image'] ?? null,
                    'quantity' => $itemData['quantity']
                ]);
                $item->save();
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Carrinho salvo com sucesso',
                'cart_id' => $cart->id
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao salvar carrinho: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Erro ao salvar o carrinho',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
