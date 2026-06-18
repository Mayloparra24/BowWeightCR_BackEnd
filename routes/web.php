<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return view('swagger');
});

Route::get('/docs.openapi', function () {
    $path = storage_path('app/private/scribe/openapi.yaml');

    if (! file_exists($path)) {
        abort(404, 'OpenAPI spec not found. Run php artisan scribe:generate');
    }

    return response()->file($path, ['Content-Type' => 'text/yaml']);
});
