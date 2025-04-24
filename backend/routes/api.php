<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\CheckRole;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas públicas (sem middleware)
Route::group([], function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/id/{id}', [ProductController::class, 'showById']);
    Route::get('/products/{product:slug}', [ProductController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    Route::get('/test', function () {
        return response()->json([
            'message' => 'API está funcionando!',
            'timestamp' => now()
        ]);
    });
    Route::get('/sanctum-test', function () {
        return response()->json(['message' => 'Sanctum está funcionando corretamente!']);
    });
    Route::post('/contact', [ContactController::class, 'send']);
    
    // Rotas de frete
    Route::post('/shipping/calculate', [ShippingController::class, 'calculateShipping']);
    Route::post('/shipping/cep', [ShippingController::class, 'searchCep']);
    Route::get('/shipping/shop-cep', [ShippingController::class, 'getShopCep']);
});

// Rotas protegidas que requerem autenticação e role de admin
Route::middleware(['auth:sanctum'])->group(function () {
    Route::middleware([CheckRole::class . ':admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
        
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);
    });

    // Rotas que requerem apenas autenticação (sem verificação de role)
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Rotas para configurações do usuário
    Route::get('/user/settings', [UserController::class, 'getSettings']);
    Route::put('/user/settings', [UserController::class, 'updateSettings']);
    
    // Rotas para endereços do usuário
    Route::get('/addresses', [AddressController::class, 'index']);
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::put('/addresses/{id}', [AddressController::class, 'update']);
    Route::delete('/addresses/{id}', [AddressController::class, 'destroy']);
    
    // Rotas para o carrinho do usuário
    Route::get('/cart/load', [CartController::class, 'load']);
    Route::post('/cart/save', [CartController::class, 'save']);
    
    // Rota temporária para pedidos (será substituída por um controller adequado no futuro)
    Route::get('/orders', function (Request $request) {
        // Retornar dados fictícios de pedidos para o usuário autenticado
        return response()->json([
            [
                'id' => 1001,
                'created_at' => now()->toISOString(),
                'status' => 'pending',
                'total' => 199.90
            ],
            [
                'id' => 1002,
                'created_at' => now()->subDay()->toISOString(),
                'status' => 'completed',
                'total' => 149.50
            ],
            [
                'id' => 1003,
                'created_at' => now()->subDays(3)->toISOString(),
                'status' => 'processing',
                'total' => 299.90
            ]
        ]);
    });
}); 