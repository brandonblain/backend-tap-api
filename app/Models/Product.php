<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\AuditLog;

class Product extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'code',
        'name',
        'brand',
        'price'
    ];

    protected static function booted(): void
    {
        // 1. Evento automático antes de crear (El código PROD-XXXX)
        static::creating(function (Product $product) {
            $count = static::count();
            $product->code = 'PROD-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        });

        // 2. Sensor automático para cuando alguien EDITA un producto
        static::updated(function (Product $product) {
            AuditLog::create([
                'user_code'  => Auth::user()?->code ?? 'SISTEMA',
                'action'     => 'EDITAR',
                'module'     => 'PRODUCTOS',
                'old_values' => array_intersect_key($product->getOriginal(), $product->getDirty()),
                'new_values' => $product->getDirty()
            ]);
        });

        // 3. Sensor automático para cuando alguien ELIMINA un producto
        static::deleted(function (Product $product) {
            AuditLog::create([
                'user_code'  => Auth::user()?->code ?? 'SISTEMA',
                'action'     => 'ELIMINAR',
                'module'     => 'PRODUCTOS',
                'old_values' => $product->toArray(),
                'new_values' => null
            ]);
        });
    }
}