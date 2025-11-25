<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $r)
    {
        $data = $r->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!\Illuminate\Support\Facades\Auth::attempt($data)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        $user = $r->user();

        //  Bloquear vendedores que NO estén aprobados
        if (strtolower($user->rol ?? '') === 'vendedor' && $user->vendor_status !== 'approved') {
            return response()->json([
                'message' => $user->vendor_status === 'pending'
                    ? 'Tu cuenta de vendedor está pendiente de aprobación.'
                    : 'Tu solicitud de vendedor fue rechazada.',
                'code' => $user->vendor_status, // 'pending' | 'rejected'
            ], 403);
        }

        //   aquí creamos el token
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => $user->load(['perfilCliente', 'perfilVendedor']),
        ]);
    }


    public function logout(Request $r)
    {
        $r->user()->currentAccessToken()?->delete();
        return response()->json(['ok' => true]);
    }
}
