<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    /**
     * Obter todos os endereços do usuário atual.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $addresses = $user->addresses;

        return response()->json($addresses);
    }

    /**
     * Criar um novo endereço.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'number' => 'required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|size:2',
            'zipcode' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $data = $validator->validated();

        // Se este é o primeiro endereço ou está marcado como padrão
        if ($request->is_default || $user->addresses()->count() === 0) {
            // Desmarcar outros endereços padrão
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address = $user->addresses()->create($data);

        return response()->json([
            'message' => 'Endereço criado com sucesso',
            'address' => $address
        ], 201);
    }

    /**
     * Atualizar um endereço existente.
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $address = $user->addresses()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'street' => 'sometimes|required|string|max:255',
            'number' => 'sometimes|required|string|max:20',
            'complement' => 'nullable|string|max:255',
            'district' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:255',
            'state' => 'sometimes|required|string|size:2',
            'zipcode' => 'sometimes|required|string|max:10',
            'is_default' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();

        // Se está marcando como padrão
        if (!empty($data['is_default']) && $data['is_default']) {
            // Desmarcar outros endereços padrão
            $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $address->update($data);

        return response()->json([
            'message' => 'Endereço atualizado com sucesso',
            'address' => $address
        ]);
    }

    /**
     * Excluir um endereço.
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $address = $user->addresses()->findOrFail($id);
        
        // Verificar se é o único endereço
        $wasDefault = $address->is_default;
        
        $address->delete();
        
        // Se o endereço excluído era o padrão, marcar outro como padrão
        if ($wasDefault) {
            $newDefault = $user->addresses()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'message' => 'Endereço excluído com sucesso'
        ]);
    }
} 