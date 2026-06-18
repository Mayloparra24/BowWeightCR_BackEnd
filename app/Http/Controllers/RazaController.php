<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\RazaResource;
use App\Models\Raza;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class RazaController extends Controller
{
    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Razas obtenidas correctamente.', 'data' => [
            ['id' => 1, 'nombre_raza' => 'Brahman', 'enfoque' => 'Carne', 'constante_peso' => 140.0, 'descripcion' => 'Raza de carne...'],
            ['id' => 2, 'nombre_raza' => 'Jersey', 'enfoque' => 'Leche', 'constante_peso' => 110.0, 'descripcion' => 'Raza lechera...'],
        ]], status: 200,
    )]
    public function index(): JsonResponse
    {
        $razas = Raza::orderBy('nombre_raza')->get();

        return ApiResponse::resource(
            resource: RazaResource::collection($razas),
            message: 'Razas obtenidas correctamente.',
        );
    }
}
