<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Maneja el inicio de sesión de los usuarios (Login).
     */
    public function login(Request $request): JsonResponse
    {
        // 1. Validamos los datos requeridos
        $credentials = $request->validate([
            'user' => 'required|email',
            'password' => 'required|string',
        ]);

        // 2. Buscamos al usuario en MongoDB Atlas
        $user = User::where('user', $credentials['user'])->first();

        // 3. Validamos que el usuario exista y la contraseña coincida (Seguridad)
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.'
            ], 401);
        }

        // 4. Generamos un Token de acceso seguro para Angular
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Retornamos la respuesta con sus datos de perfil y secciones autorizadas
        return response()->json([
            'message' => '¡Inicio de sesión exitoso!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'code' => $user->code,
                'name' => $user->name,
                'user' => $user->user,
                'profile_pic' => $user->profile_pic,
                'profiles' => $user->profile_codes // Angular usará esto para bloquear/desbloquear pantallas
            ]
        ], 200);
    }

    /**
     * Maneja el cierre de sesión (Logout).
     */
    public function logout(Request $request): JsonResponse
    {
        // Revocamos el token que el usuario está usando en ese momento
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.'
        ], 200);
    }
}