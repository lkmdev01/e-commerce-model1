<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rota de teste simplificada
Route::get('/test', function () {
    return response()->json([
        'message' => 'API está funcionando!',
        'timestamp' => now()
    ]);
}); 