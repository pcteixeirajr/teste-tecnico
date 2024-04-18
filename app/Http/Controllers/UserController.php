<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function login(LoginRequest $request){
        $body = $request->all();

        $login = Auth::attempt($body);
        if ($login) {
            $user = Auth::user();
            $token = auth()->login($user);
            return response()->json([
                'user' => $user->name,
                'saldo' => $user->saldo,
                'token' => $token,
            ]);
        }

        return response()->json(['message' => 'credenciais inválidas'],400);
    }

}
