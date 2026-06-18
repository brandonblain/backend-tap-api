<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;
use App\Models\Profile;
use App\Models\Products;
use App\Models\User;

// Ruta pública para iniciar sesión
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (Solo pueden usarse si el usuario ya inició sesión)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('products', ProductController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('profiles', ProfileController::class);
});