<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credenciais = $request->only(['email', 'password']);

        if (Auth::attempt($credenciais)) {
            $token = $request->user()->createToken('auth_token');
            return response()->json(['token' => $token->plainTextToken], 200);
        }

        return response()->json(['message' => 'Usuário ou senha inválida'], 401);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso!'], 200);
    }
    public function refresh(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('auth_token');

        return response()->json(['token' => $token->plainTextToken], 200);
    }
    public function me()
    {
        return response()->json(Auth::user(), 200);
    }
}
