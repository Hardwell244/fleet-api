<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login - Gera token de acesso
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais incorretas.'],
            ]);
        }

        // Verificar se a empresa está ativa
        if (!$user->company || !$user->company->is_active) {
            return response()->json([
                'message' => 'Empresa inativa. Entre em contato com o suporte.',
            ], 403);
        }

        // Revogar tokens anteriores (opcional - apenas um device ativo)
        // $user->tokens()->delete();

        // Criar token
        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'company' => [
                    'id' => $user->company->id,
                    'name' => $user->company->name,
                    'cnpj' => $user->company->cnpj_formatted,
                ],
            ],
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }

    /**
     * Logout - Revoga token atual
     */
    public function logout(Request $request)
    {
        // Revogar apenas o token atual
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso!',
        ], 200);
    }

    /**
     * Me - Retorna dados do usuário autenticado
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'role' => $request->user()->role,
                'company' => [
                    'id' => $request->user()->company->id,
                    'name' => $request->user()->company->name,
                    'cnpj' => $request->user()->company->cnpj_formatted,
                    'is_active' => $request->user()->company->is_active,
                ],
            ],
        ], 200);
    }

    /**
     * Refresh - Gera novo token (revoga o antigo)
     */
    public function refresh(Request $request)
    {
        // Revogar token atual
        $request->user()->currentAccessToken()->delete();

        // Criar novo token
        $token = $request->user()->createToken('api-token', ['*'])->plainTextToken;

        return response()->json([
            'message' => 'Token renovado com sucesso!',
            'token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
}
