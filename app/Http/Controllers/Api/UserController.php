<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Listar todos los usuarios con sus perfiles embebidos o relacionados
    public function index(): JsonResponse
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    // Crear un nuevo usuario desde el panel del Supervisor
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'user'          => 'required|string|email|unique:users,user',
            'name'          => 'required|string|max:255',
            'password'      => 'required|string|min:6',
            'phone'         => 'nullable|string',
            'profile_pic'   => 'nullable|string',
            'profile_codes' => 'required|array' // Arreglo dinámico []
        ]);

        // Autogenerar el código de usuario consecutivo USR-XXXX
        $count = User::count();
        $userCode = 'USR-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        $user = User::create([
            'user'          => $request->user,
            'name'          => $request->name,
            'password'      => $request->password, // El modelo lo cifra solo por tu cast 'hashed'
            'phone'         => $request->phone,
            'profile_pic'   => $request->profile_pic,
            'profile_codes' => $request->profile_codes
        ]);

        return response()->json([
            'message' => 'Usuario creado exitosamente con sus perfiles',
            'user'    => $user
        ], 201);
    }

    // Ver un usuario en específico
    public function show(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'user'          => 'required|string|email|unique:users,user,' . $id . ',_id', // Evita marcar duplicado si es su propio correo
            'name'          => 'required|string|max:255',
            'password'      => 'nullable|string|min:6', // Opcional por si no quieren cambiar la contraseña
            'phone'         => 'nullable|string',
            'profile_pic'   => 'nullable|string',
            'profile_codes' => 'required|array'
        ]);

        $data = [
            'user'          => $request->user,
            'name'          => $request->name,
            'phone'         => $request->phone,
            'profile_pic'   => $request->profile_pic,
            'profile_codes' => $request->profile_codes
        ];

        // Solo si el supervisor escribió una contraseña nueva, la actualizamos
        if ($request->filled('password')) {
            $data['password'] = $request->password; // El cast 'hashed' del modelo la cifra solo
        }

        $user->update($data);

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user'    => $user
        ], 200);
    }

    // Eliminar un usuario
    public function destroy(string $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente'], 200);
    }
}