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
    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Bitácora obtenida correctamente.', 'data' => [
            ['id' => 1, 'accion' => 'Inicio de sesión', 'entidad_tipo' => 'usuario', 'entidad_id' => 1, 'creada_el' => '2026-06-17T10:00:00+00:00', 'usuario' => ['id' => 1, 'nombre_completo' => 'Iván Chavarría']],
            ['id' => 2, 'accion' => 'Creación de bovino', 'entidad_tipo' => 'bovino', 'entidad_id' => 3, 'creada_el' => '2026-06-17T11:00:00+00:00', 'usuario' => ['id' => 1, 'nombre_completo' => 'Iván Chavarría']],
        ], 'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 15, 'total' => 2, 'from' => 1, 'to' => 2]], status: 200,
    )]
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
