<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Resources\FincaResource;
use App\Models\BitacoraActividad;
use App\Models\Finca;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FincaController extends Controller
{
    #[\Knuckles\Scribe\Attributes\Response(
        content: [
            'success' => true,
            'message' => 'Fincas obtenidas correctamente.',
            'data' => [
                [
                    'id' => 1,
                    'propietario_id' => 1,
                    'nombre_finca' => 'Finca La Esperanza',
                    'ubicacion' => 'Liberia, Guanacaste',
                    'canton' => 'Liberia',
                    'provincia' => 'Guanacaste',
                    'esta_activa' => true,
                    'creado_en' => '2026-06-17T00:00:00+00:00',
                    'propietario' => [
                        'id' => 1,
                        'nombre_completo' => 'Iván Chavarría',
                        'correo_electronico' => 'ganadero@bovweight.com',
                        'rol' => 'ganadero',
                    ],
                ],
            ],
        ],
        status: 200,
    )]
    public function index(Request $request): JsonResponse
    {
        $usuario = $request->user();

        if ($usuario->esAdministrador()) {
            $fincas = Finca::with('propietario')->get();
        } elseif ($usuario->esVeterinario()) {
            $fincas = Finca::whereHas('asignaciones', function ($query) use ($usuario) {
                $query->where('usuario_id', $usuario->id)
                    ->where('esta_activa', true);
            })->with('propietario')->get();
        } else {
            $fincas = Finca::where('propietario_id', $usuario->id)
                ->with('propietario')
                ->get();

            if ($usuario->esAsistente()) {
                $asignadas = Finca::whereHas('asignaciones', function ($query) use ($usuario) {
                    $query->where('usuario_id', $usuario->id)->where('esta_activa', true);
                })->with('propietario')->get();
                $fincas = $fincas->merge($asignadas)->unique('id');
            }
        }

        return ApiResponse::resource(
            resource: FincaResource::collection($fincas),
            message: 'Fincas obtenidas correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: [
            'success' => true,
            'message' => 'Finca registrada correctamente.',
            'data' => [
                'id' => 1,
                'propietario_id' => 1,
                'nombre_finca' => 'Finca La Esperanza',
                'ubicacion' => 'Liberia, Guanacaste',
                'canton' => 'Liberia',
                'provincia' => 'Guanacaste',
                'esta_activa' => true,
                'creado_en' => '2026-06-17T00:00:00+00:00',
                'propietario' => [
                    'id' => 1,
                    'nombre_completo' => 'Iván Chavarría',
                    'correo_electronico' => 'ganadero@bovweight.com',
                    'rol' => 'ganadero',
                ],
            ],
        ],
        status: 201,
    )]
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Finca::class);

        $data = $request->validate([
            'nombre_finca' => ['required', 'string', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'canton' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
        ]);

        $finca = Finca::create([
            ...$data,
            'propietario_id' => $request->user()->id,
        ]);

        BitacoraActividad::registrar(
            accion: 'crear',
            usuario: $request->user(),
            entidadTipo: 'finca',
            entidadId: $finca->id,
            descripcion: "Se creó la finca {$finca->nombre_finca}",
            ip: $request->ip(),
        );

        return ApiResponse::resource(
            resource: new FincaResource($finca->load('propietario')),
            message: 'Finca registrada correctamente.',
            status: 201,
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: [
            'success' => true, 'message' => 'Finca obtenida correctamente.',
            'data' => [
                'id' => 1, 'propietario_id' => 1, 'nombre_finca' => 'Finca La Esperanza',
                'ubicacion' => 'Liberia, Guanacaste', 'canton' => 'Liberia', 'provincia' => 'Guanacaste',
                'esta_activa' => true, 'creado_en' => '2026-06-17T00:00:00+00:00',
                'propietario' => ['id' => 1, 'nombre_completo' => 'Iván Chavarría', 'correo_electronico' => 'ganadero@bovweight.com', 'rol' => 'ganadero'],
                'bovinos' => [],
            ],
        ], status: 200,
    )]
    public function show(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('view', $finca);

        return ApiResponse::resource(
            resource: new FincaResource($finca->load(['propietario', 'bovinos'])),
            message: 'Finca obtenida correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: [
            'success' => true, 'message' => 'Finca actualizada correctamente.',
            'data' => [
                'id' => 1, 'propietario_id' => 1, 'nombre_finca' => 'Finca La Esperanza',
                'ubicacion' => 'Liberia, Guanacaste', 'canton' => 'Liberia', 'provincia' => 'Guanacaste',
                'esta_activa' => true, 'creado_en' => '2026-06-17T00:00:00+00:00',
                'propietario' => ['id' => 1, 'nombre_completo' => 'Iván Chavarría', 'correo_electronico' => 'ganadero@bovweight.com', 'rol' => 'ganadero'],
            ],
        ], status: 200,
    )]
    public function update(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('update', $finca);

        $data = $request->validate([
            'nombre_finca' => ['sometimes', 'string', 'max:255'],
            'ubicacion' => ['nullable', 'string', 'max:255'],
            'canton' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'esta_activa' => ['sometimes', 'boolean'],
        ]);

        $finca->update($data);

        BitacoraActividad::registrar(
            accion: 'editar',
            usuario: $request->user(),
            entidadTipo: 'finca',
            entidadId: $finca->id,
            descripcion: "Se editó la finca {$finca->nombre_finca}",
            ip: $request->ip(),
        );

        return ApiResponse::resource(
            resource: new FincaResource($finca->fresh()->load('propietario')),
            message: 'Finca actualizada correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Finca eliminada correctamente.', 'data' => null],
        status: 200,
    )]
    public function destroy(Request $request, Finca $finca): JsonResponse
    {
        $this->authorize('delete', $finca);

        $nombre = $finca->nombre_finca;
        $fincaId = $finca->id;

        $finca->delete();

        BitacoraActividad::registrar(
            accion: 'eliminar',
            usuario: $request->user(),
            entidadTipo: 'finca',
            entidadId: $fincaId,
            descripcion: "Se eliminó la finca {$nombre}",
            ip: $request->ip(),
        );

        return ApiResponse::success(
            message: 'Finca eliminada correctamente.',
        );
    }
}