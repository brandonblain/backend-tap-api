<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; 

class Profile extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'profiles';

    // Habilitamos los timestamps para garantizar que exista la "Fecha de creación"
    public $timestamps = true; 

    // Campos autorizados por la rúbrica para asignación masiva
    protected $fillable = [
        'code',     // 1. Código de perfil (Auto-generado en el booted)
        'name',     // 2. Nombre del perfil
        'sections'  // d. Lista de secciones relacionadas al perfil (Permisos)
    ];

    // Requisito d: Forzamos a MongoDB a tratar las secciones como un arreglo nativo
    protected $casts = [
        'sections' => 'array' 
    ];

    /**
     * Automatización del Código de Perfil
     */
    protected static function booted(): void
    {
        static::creating(function (Profile $profile) {
            // Requisito 1: Genera el consecutivo estricto PROF-XXXX basado en tu conteo
            $count = static::count();
            $profile->code = 'PROF-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}