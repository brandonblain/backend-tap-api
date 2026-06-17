<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Sanctum\PersonalAccessToken as SanctumToken;
use MongoDB\Laravel\Eloquent\DocumentModel;

// Creamos una clase espejo interna para que Sanctum herede de MongoDB de forma limpia
class MongoPersonalAccessToken extends SanctumToken {

    use DocumentModel;

    protected $connection = 'mongodb';
    protected $collection = 'personal_access_tokens';
    protected $primaryKey = '_id';
    protected $keyType = 'string';
    protected $guarded = [];
}

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 👈 Le decimos a Sanctum que use nuestro modelo NoSQL
        Sanctum::usePersonalAccessTokenModel(MongoPersonalAccessToken::class);
    }
}