<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user->name,
                'saldo' => $user->saldo,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => config('auth.token_lifetime') * 60,
            ]);
        }

        return response()->json(['message' => 'credenciais inválidas'], 400);
    }
}
