<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * 📊 REQUISITO EXAMEN: Mostrar tabla con listado general
     * GET /api/profiles
     * Alimenta la tabla, el buscador y las descargas en PDF y Excel.
     */
    public function index(): JsonResponse
    {
        // Los ordenamos por fecha de creación para que los nuevos aparezcan arriba
        $profiles = Profile::orderBy('created_at', 'desc')->get();
        return response()->json($profiles, 200);
    }

    /**
     * ➕ EXTRA NECESARIO: Registrar nuevo perfil
     * POST /api/profiles
     * Procesa el alta. El 'code' se autogenera en el modelo NoSQL.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'sections' => 'required|array' // Requisito d: Lista de secciones relacionadas
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profile = Profile::create([
            'name'     => $request->input('name'),
            'sections' => $request->input('sections')
        ]);

        return response()->json($profile, 201);
    }

    /**
     * 👁️ REQUISITO EXAMEN: Detalle del perfil (modal)
     * GET /api/profiles/{id}
     * Devuelve el objeto con code, name, created_at y el arreglo de secciones.
     */
    public function show(string $id): JsonResponse
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        return response()->json($profile, 200);
    }

    /**
     * ✏️ REQUISITO EXAMEN - ACCIÓN 1: Edición
     * PUT /api/profiles/{id}
     * Modifica el nombre o las secciones asignadas en MongoDB.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'     => 'sometimes|required|string|max:255',
            'sections' => 'sometimes|required|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profile->update($request->only(['name', 'sections']));

        return response()->json($profile, 200);
    }

    /**
     * 🗑️ REQUISITO EXAMEN - ACCIÓN 2: Eliminación
     * DELETE /api/profiles/{id}
     * Borra el registro de la colección NoSQL permanentemente.
     */
    public function destroy(string $id): JsonResponse
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Perfil no encontrado'], 404);
        }

        $profile->delete();

        return response()->json(['message' => 'Perfil eliminado correctamente'], 200);
    }
}