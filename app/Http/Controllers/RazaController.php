<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\RazaResource;
use App\Models\Raza;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class RazaController extends Controller
{
    public function index(): JsonResponse
    {
        $razas = Raza::orderBy('nombre_raza')->get();

        return ApiResponse::resource(
            resource: RazaResource::collection($razas),
            message: 'Razas obtenidas correctamente.',
        );
    }
}
