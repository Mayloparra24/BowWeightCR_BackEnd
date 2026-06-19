<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Finca;
use App\Models\Usuario;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    public function usuariosDisponibles(Request $request, string $rol): JsonResponse
    {
        $usuario = $request->user();

        if (! $usuario->esGanadero() && ! $usuario->esAsistente() && ! $usuario->esAdministrador()) {
            return ApiResponse::error(message: 'No autorizado.', status: 403);
        }

        if (! in_array($rol, ['veterinario', 'asistente'])) {
            return ApiResponse::error(message: 'Rol no válido.', status: 422);
        }

        $usuarios = Usuario::where('rol', $rol)
            ->where('esta_activo', true)
            ->orderBy('nombre_completo')
            ->get(['id', 'nombre_completo', 'correo_electronico']);

        return ApiResponse::success(data: $usuarios);
    }

    public function index(Request $request, int $fincaId, string $rol): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $this->authorize('viewAny', [Asignacion::class, $finca]);

        $asignaciones = $finca->asignaciones()
            ->with('usuario')
            ->where('esta_activa', true)
            ->where('rol', $rol)
            ->get();

        return ApiResponse::success(
            data: $asignaciones,
            message: 'Usuarios asignados obtenidos correctamente.',
        );
    }

    public function store(Request $request, int $fincaId, string $rol): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $this->authorize('create', [Asignacion::class, $finca]);

        $data = $request->validate([
            'usuario_id' => ['required', 'exists:usuarios,id'],
        ]);

        $usuario = Usuario::findOrFail($data['usuario_id']);

        $rolValido = $rol === 'veterinario' ? $usuario->esVeterinario() : ($rol === 'asistente' && $usuario->esAsistente());
        if (! $rolValido) {
            return ApiResponse::error(
                message: 'El usuario seleccionado no tiene el rol solicitado.',
                status: 422,
            );
        }

        $yaAsignado = Asignacion::where('usuario_id', $data['usuario_id'])
            ->where('finca_id', $fincaId)
            ->where('rol', $rol)
            ->where('esta_activa', true)
            ->exists();

        if ($yaAsignado) {
            return ApiResponse::error(
                message: 'Este usuario ya está asignado a esta finca con ese rol.',
                status: 422,
            );
        }

        $asignacion = Asignacion::create([
            'usuario_id' => $data['usuario_id'],
            'finca_id' => $fincaId,
            'rol' => $rol,
            'asignado_por' => $request->user()->id,
            'esta_activa' => true,
        ]);

        return ApiResponse::success(
            data: $asignacion->load('usuario'),
            message: 'Usuario asignado correctamente.',
            status: 201,
        );
    }

    public function destroy(Request $request, int $fincaId, int $asignacionId): JsonResponse
    {
        $finca = Finca::findOrFail($fincaId);

        $asignacion = Asignacion::where('id', $asignacionId)
            ->where('finca_id', $fincaId)
            ->firstOrFail();

        $this->authorize('delete', $asignacion);

        $asignacion->update(['esta_activa' => false]);

        return ApiResponse::success(
            message: 'Usuario removido de la finca correctamente.',
        );
    }
}
