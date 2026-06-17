<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Auth\User as Authenticatable; //  Autenticación con MongoDB
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $connection = 'mongodb';
    protected $collection = 'users';

    /**
     * Los atributos que son asignables en masa.
     */
    protected $fillable = [
        'code',
        'user',          // Correo electrónico único 
        'name',          // Nombre requerido 
        'password',      // Contraseña cifrada 
        'phone',         // Teléfono opcional con código de país 
        'profile_pic',   // URL o string de foto de perfil requerida 
        'profile_codes'  // Lista de códigos de perfiles relacionados 
    ];

    /**
     * Los atributos que deben ocultarse en los JSON de la API.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversión de tipos de datos nativos.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Cifrado automático de seguridad 
        'profile_codes' => 'array' // MongoDB guardará esto como un arreglo dinámico []
    ];

    /**
     * Evento de arranque del modelo para automatizar procesos.
     */
    protected static function booted(): void
    {
         // Cuenta los usuarios actuales para generar el consecutivo automático
        static::creating(function (User $user) {
            $count = static::count();
            $user->code = 'USR-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        });

        //automático para cuando se EDITE un usuario
        static::updated(function (User $user) {
            AuditLog::create([
                'user_code'  => Auth::user()?->code ?? 'SISTEMA',
                'action'     => 'EDITAR',
                'module'     => 'USUARIOS',
                'old_values' => array_intersect_key($user->getOriginal(), $user->getDirty()),
                'new_values' => $user->getDirty()
            ]);
        });

        //Sensor automático para cuando se ELIMINE un usuario
        static::deleted(function (User $user) {
            AuditLog::create([
                'user_code'  => Auth::user()?->code ?? 'SISTEMA',
                'action'     => 'ELIMINAR',
                'module'     => 'USUARIOS',
                'old_values' => $user->toArray(),
                'new_values' => null
            ]);
        });
    }
}