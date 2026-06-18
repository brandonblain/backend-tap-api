<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Maneja el inicio de sesión de los usuarios (Login).
     */
    public function login(Request $request): JsonResponse
    {
        //Validamos los datos del usuario
        $credentials = $request->validate([
            'user' => 'required|email',
            'password' => 'required|string',
        ]);

        //Busca al usuario en Atlas
        $user = User::where('user', $credentials['user'])->first();

        //Valida que el usuario exista y la contraseña coincida (Seguridad)
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Las credenciales proporcionadas son incorrectas.'
            ], 401);
        }

        //Genera un Token de acceso seguro para Angular
        $token = $user->createToken('auth_token')->plainTextToken;

        //Retornamos el response con sus datos de perfil y secciones autorizadas
        return response()->json([
            'message' => '¡Inicio de sesión exitoso!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'code' => $user->code,
                'name' => $user->name,
                'user' => $user->user,
                'profile_pic' => $user->profile_pic,
                'profiles' => $user->profile_codes 
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

    /**
     *Recuperación de CONTRASEÑA
     */
    public function recoverPassword(Request $request): JsonResponse
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        //Valida que el usuario realmente existe en la base de datos
        $user = User::where('user', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'El correo electrónico no se encuentra registrado en el sistema.'
            ], 444);
        }

        try {
            //Genera credencial temporal de 8 caracteres aleatorios
            $temporaryPassword = \Illuminate\Support\Str::random(8);

            //Actualizar la contraseña encriptada en el modelo del usuario
            $user->password = Hash::make($temporaryPassword);
            $user->save();

            //Enviar las credenciales nuevas al correo registrado del usuario
            Mail::to($user->user)->send(new RecoverPasswordMail($temporaryPassword, $user->name));

            return response()->json([
                'message' => 'Credenciales actualizadas. Se ha enviado la nueva contraseña a tu correo.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar el envío del correo.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}