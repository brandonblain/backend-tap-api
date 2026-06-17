<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model; 

class Profile extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'profiles';

    protected $fillable = [
        'code',
        'name',
        'sections' // Arreglo de secciones permitidas, ej: ["products", "users"]
    ];

    protected $casts = [
        'sections' => 'array' // Mapeo automático NoSQL para arreglos de strings
    ];

    protected static function booted(): void
    {
        static::creating(function (Profile $profile) {
            // Genera de forma automática el consecutivo PROF-0001
            $count = static::count();
            $profile->code = 'PROF-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        });
    }
}