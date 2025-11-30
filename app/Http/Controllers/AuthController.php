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

        // return ['token' => $token->plainTextToken];
    }
    public function logout()
    {
        return 'Logout';
    }
    public function refresh()
    {
        return 'Refresh';
    }
    public function me()
    {
        return 'Me';
    }
}
