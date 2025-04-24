<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            Log::info('Login attempt', ['email' => $request->email]);

            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'remember' => 'boolean',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                Log::warning('User not found', ['email' => $request->email]);
                throw ValidationException::withMessages([
                    'email' => ['As credenciais fornecidas estão incorretas.'],
                ]);
            }

            if (!Hash::check($request->password, $user->password)) {
                Log::warning('Invalid password', ['email' => $request->email]);
                throw ValidationException::withMessages([
                    'email' => ['As credenciais fornecidas estão incorretas.'],
                ]);
            }

            // Define o tempo de expiração do token com base na opção "lembrar-me"
            $remember = $request->remember ?? false;
            $tokenExpiration = $remember ? now()->addDays(30) : now()->addHours(24);
            
            // Criar token com expiração definida
            $tokenInstance = $user->createToken('auth-token', ['*'], $tokenExpiration);
            $token = $tokenInstance->plainTextToken;

            Log::info('Login successful', [
                'email' => $request->email,
                'remember' => $remember,
                'expiration' => $tokenExpiration->toDateTimeString()
            ]);

            return response()->json([
                'user' => $user,
                'token' => $token,
                'token_expiration' => $tokenExpiration->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            Log::error('Login error', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso']);
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    /**
     * Solicitar redefinição de senha
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Se o seu e-mail estiver registrado, você receberá um link para redefinir sua senha.'
            ]);
        }

        // Gerar token de redefinição de senha
        $token = \Str::random(60);
        
        // Armazenar token no banco de dados
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Na prática, aqui seria enviado um email com o link para redefinição
        // Mas para fins de demonstração, apenas retornamos o token
        $resetUrl = env('FRONTEND_URL', 'http://localhost:5173') . '/reset-password?token=' . $token . '&email=' . urlencode($request->email);
        
        Log::info('Password reset requested', [
            'email' => $request->email,
            'reset_url' => $resetUrl
        ]);

        return response()->json([
            'message' => 'Se o seu e-mail estiver registrado, você receberá um link para redefinir sua senha.',
            // Apenas para desenvolvimento, remover em produção
            'reset_url' => $resetUrl,
            'token' => $token
        ]);
    }

    /**
     * Redefinir senha
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $passwordReset = \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return response()->json([
                'message' => 'Token inválido ou expirado'
            ], 422);
        }

        // Verificar se o token não expirou (1 hora)
        $tokenCreatedAt = Carbon::parse($passwordReset->created_at);
        if ($tokenCreatedAt->diffInMinutes(now()) > 60) {
            return response()->json([
                'message' => 'Token expirado'
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'message' => 'Usuário não encontrado'
            ], 404);
        }

        // Atualizar senha
        $user->password = Hash::make($request->password);
        $user->save();

        // Remover token
        \DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Senha redefinida com sucesso'
        ]);
    }
} 