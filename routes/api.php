<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BovinoController;
use App\Http\Controllers\FincaController;
use App\Http\Controllers\PesajeController;
use App\Http\Controllers\WeightEstimationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsignacionVeterinarioController;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Fincas
    Route::apiResource('fincas', FincaController::class);

    // Bovinos
    Route::apiResource('bovinos', BovinoController::class);
    Route::patch('bovinos/{bovino}/inactivar', [BovinoController::class, 'marcarInactivo']);
    Route::patch('bovinos/{bovino}/activar', [BovinoController::class, 'marcarActivo']);

    // Pesajes
    Route::get('bovinos/{bovino}/pesajes', [PesajeController::class, 'index']);
    Route::post('/pesajes', [PesajeController::class, 'store']);
    Route::put('/pesajes/{pesaje}/corregir', [PesajeController::class, 'correct']);
    Route::post('/pesajes/estimar', [WeightEstimationController::class, 'estimate']);

    // Asignaciones de veterinarios
    Route::get('fincas/{finca}/veterinarios', [AsignacionVeterinarioController::class, 'index']);
    Route::post('fincas/{finca}/veterinarios', [AsignacionVeterinarioController::class, 'store']);
    Route::delete('fincas/{finca}/veterinarios/{asignacion}', [AsignacionVeterinarioController::class, 'destroy']);
});