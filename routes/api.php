<?php

use App\Http\Controllers\PesajeController;
use App\Http\Controllers\WeightEstimationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('bovinos/{bovino}')->group(function () {
    Route::get('pesajes', [PesajeController::class, 'index']);
});

Route::post('/pesajes', [PesajeController::class, 'store']);
Route::put('/pesajes/{pesaje}/corregir', [PesajeController::class, 'correct']);

Route::post('/pesajes/estimar', [WeightEstimationController::class, 'estimate']);
