<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        \Log::info('CheckRole middleware - Verificando role', [
            'required_role' => $role,
            'user' => $request->user(),
            'user_role' => $request->user() ? $request->user()->role : null
        ]);

        if (!$request->user()) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        if ($request->user()->role !== $role) {
            return response()->json([
                'message' => 'Acesso não autorizado.',
                'required_role' => $role,
                'user_role' => $request->user()->role
            ], 403);
        }

        return $next($request);
    }
} 