<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    // Devuelve todos los perfiles de MongoDB (PROF-0001, PROF-0002, etc.)
    public function index(): JsonResponse
    {
        $profiles = Profile::all();
        return response()->json($profiles, 200);
    }
}