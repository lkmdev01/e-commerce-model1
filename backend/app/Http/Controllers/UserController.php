<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Listar todos os usuários
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return response()->json($users);
    }

    /**
     * Armazenar um novo usuário
     */
    public function store(Request $request)
    {
        // Log para debug
        \Log::info('Criando usuário - Dados recebidos:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6', // Reduzindo o mínimo para 6 caracteres
            'role' => 'sometimes|string|in:admin,user',
            'status' => 'sometimes',
        ]);

        if ($validator->fails()) {
            \Log::warning('Validação falhou:', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Garantir que status seja tratado como booleano
        $status = true; // valor padrão
        if ($request->has('status')) {
            $status = filter_var($request->status, FILTER_VALIDATE_BOOLEAN);
            \Log::info('Status após conversão:', ['original' => $request->status, 'convertido' => $status]);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'user',
                'status' => $status,
            ]);
            
            \Log::info('Usuário criado com sucesso:', ['id' => $user->id]);
            return response()->json($user, 201);
        } catch (\Exception $e) {
            \Log::error('Erro ao criar usuário:', ['message' => $e->getMessage()]);
            return response()->json(['errors' => ['geral' => $e->getMessage()]], 422);
        }
    }

    /**
     * Exibir um usuário específico
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Atualizar um usuário específico
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|string|in:admin,user',
            'status' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Atualizar os campos fornecidos
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        
        if ($request->has('role')) {
            $user->role = $request->role;
        }
        
        if ($request->has('status')) {
            $user->status = filter_var($request->status, FILTER_VALIDATE_BOOLEAN);
        }
        
        $user->save();

        return response()->json($user);
    }

    /**
     * Remover um usuário específico
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
} 