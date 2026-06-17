<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'audit_logs'; // Se guardará en una colección separada

    protected $fillable = [
        'user_code',   // Quién lo hizo (USR-0002)
        'action',      // Qué hizo (CREAR, EDITAR, ELIMINAR)
        'module',      // En qué pantalla (PRODUCTOS, USUARIOS)
        'old_values',  // Cómo estaban los datos antes (Arreglo NoSQL)
        'new_values'   // Cómo quedaron los datos después (Arreglo NoSQL)
    ];
}