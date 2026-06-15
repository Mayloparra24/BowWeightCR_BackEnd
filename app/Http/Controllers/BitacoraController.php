<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\BitacoraResource;
use App\Models\BitacoraActividad;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        $query = BitacoraActividad::with('usuario')
            ->orderByDesc('creada_el');

        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->input('usuario_id'));
        }

        if ($request->filled('accion')) {
            $query->where('accion', 'like', '%'.$request->input('accion').'%');
        }

        if ($request->filled('entidad_tipo')) {
            $query->where('entidad_tipo', $request->input('entidad_tipo'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('creada_el', '>=', $request->input('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('creada_el', '<=', $request->input('hasta'));
        }

        $bitacoras = $query->paginate($request->input('per_page', 15));

        return ApiResponse::paginated(
            paginator: $bitacoras,
            message: 'Bitácora obtenida correctamente.',
        );
    }
}
