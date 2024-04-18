<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login(Request $request){
        $body = $request->all();

        $validator = Validator::make($body, [
            'email' => ['required', 'email:filter'],
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $login = Auth::attempt($body);
        if ($login) {
            
            $user = Auth::user();
            $token = auth()->login($user);
            return response()->json([
                'user' => $user->name,
                'saldo' => $user->saldo,
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60,
            ]);
        }

        return response()->json(['message' => 'credenciais inválidas'],400);
    }

}
