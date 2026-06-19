<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\BitacoraActividad;
use App\Models\Usuario;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Usuarios obtenidos correctamente.', 'data' => [
            ['id' => 1, 'nombre_completo' => 'Iván Chavarría', 'correo_electronico' => 'ganadero@bovweight.com', 'rol' => 'ganadero', 'esta_activo' => true, 'debe_cambiar_contrasena' => false, 'correo_verificado_en' => null, 'creado_en' => '2026-06-17T00:00:00+00:00'],
            ['id' => 2, 'nombre_completo' => 'Dr. Roberto Solano', 'correo_electronico' => 'veterinario@bovweight.com', 'rol' => 'veterinario', 'esta_activo' => true, 'debe_cambiar_contrasena' => false, 'correo_verificado_en' => null, 'creado_en' => '2026-06-17T00:00:00+00:00'],
        ], 'meta' => ['current_page' => 1, 'last_page' => 1, 'per_page' => 15, 'total' => 2, 'from' => 1, 'to' => 2]], status: 200,
    )]
    public function index(Request $request): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        $usuarios = Usuario::orderBy('nombre_completo')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated(
            paginator: $usuarios,
            message: 'Usuarios obtenidos correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Usuario creado correctamente.', 'data' => [
            'id' => 1, 'nombre_completo' => 'Asistente de Prueba', 'correo_electronico' => 'asistente@bovweight.cr', 'rol' => 'asistente', 'esta_activo' => true, 'debe_cambiar_contrasena' => true, 'correo_verificado_en' => null, 'creado_en' => '2026-06-17T00:00:00+00:00',
        ]], status: 201,
    )]
    public function store(Request $request): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        $data = $request->validate([
            'nombre_completo' => ['required', 'string', 'max:255'],
            'correo_electronico' => ['required', 'email', 'unique:usuarios,correo_electronico'],
            'contrasena' => ['required', 'string', 'min:8'],
            'rol' => ['required', 'string', 'in:administrador,ganadero,veterinario,asistente'],
            'esta_activo' => ['sometimes', 'boolean'],
        ]);

        $usuario = Usuario::create([
            'nombre_completo' => $data['nombre_completo'],
            'correo_electronico' => $data['correo_electronico'],
            'contrasena_hash' => $data['contrasena'],
            'rol' => $data['rol'],
            'esta_activo' => $data['esta_activo'] ?? true,
            'debe_cambiar_contrasena' => true,
        ]);

        BitacoraActividad::registrar(
            accion: 'crear',
            usuario: $request->user(),
            entidadTipo: 'usuario',
            entidadId: $usuario->id,
            descripcion: "Se creó el usuario {$usuario->nombre_completo}",
            ip: $request->ip(),
        );

        return ApiResponse::resource(
            resource: new UserResource($usuario),
            message: 'Usuario creado correctamente.',
            status: 201,
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Usuario obtenido correctamente.', 'data' => ['id' => 1, 'nombre_completo' => 'Iván Chavarría', 'correo_electronico' => 'ganadero@bovweight.com', 'rol' => 'ganadero', 'esta_activo' => true, 'debe_cambiar_contrasena' => false, 'correo_verificado_en' => null, 'creado_en' => '2026-06-17T00:00:00+00:00']], status: 200,
    )]
    public function show(Usuario $usuario): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        return ApiResponse::resource(
            resource: new UserResource($usuario),
            message: 'Usuario obtenido correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Usuario actualizado correctamente.', 'data' => ['id' => 1, 'nombre_completo' => 'Iván Chavarría', 'correo_electronico' => 'ganadero@bovweight.com', 'rol' => 'ganadero', 'esta_activo' => true, 'debe_cambiar_contrasena' => false, 'correo_verificado_en' => null, 'creado_en' => '2026-06-17T00:00:00+00:00']], status: 200,
    )]
    public function update(Request $request, Usuario $usuario): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        $data = $request->validate([
            'nombre_completo' => ['sometimes', 'string', 'max:255'],
            'correo_electronico' => ['sometimes', 'email', Rule::unique('usuarios', 'correo_electronico')->ignore($usuario->id)],
            'contrasena' => ['sometimes', 'string', 'min:8'],
            'rol' => ['sometimes', 'string', 'in:administrador,ganadero,veterinario,asistente'],
            'esta_activo' => ['sometimes', 'boolean'],
        ]);

        $updateData = [
            'nombre_completo' => $data['nombre_completo'] ?? $usuario->nombre_completo,
            'correo_electronico' => $data['correo_electronico'] ?? $usuario->correo_electronico,
            'rol' => $data['rol'] ?? $usuario->rol,
            'esta_activo' => $data['esta_activo'] ?? $usuario->esta_activo,
        ];

        if (isset($data['contrasena'])) {
            $updateData['contrasena_hash'] = Hash::make($data['contrasena']);
            $updateData['debe_cambiar_contrasena'] = true;
        }

        $estabaActivo = $usuario->esta_activo;
        $usuario->update($updateData);

        if (array_key_exists('esta_activo', $data) && $data['esta_activo'] !== $estabaActivo) {
            BitacoraActividad::registrar(
                accion: $data['esta_activo'] ? 'activar' : 'desactivar',
                usuario: $request->user(),
                entidadTipo: 'usuario',
                entidadId: $usuario->id,
                descripcion: $data['esta_activo']
                    ? "Se activó el usuario {$usuario->nombre_completo}"
                    : "Se desactivó el usuario {$usuario->nombre_completo}",
                ip: $request->ip(),
            );
        } else {
            BitacoraActividad::registrar(
                accion: 'editar',
                usuario: $request->user(),
                entidadTipo: 'usuario',
                entidadId: $usuario->id,
                descripcion: "Se editó el usuario {$usuario->nombre_completo}",
                ip: $request->ip(),
            );
        }

        return ApiResponse::resource(
            resource: new UserResource($usuario->fresh()),
            message: 'Usuario actualizado correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Usuario eliminado correctamente.', 'data' => null], status: 200,
    )]
    public function destroy(Usuario $usuario): JsonResponse
    {
        $this->authorize('administrar-usuarios');

        if ($usuario->id === request()->user()->id) {
            return ApiResponse::error(
                message: 'No podés eliminar tu propio usuario.',
                status: 422,
            );
        }

        $nombre = $usuario->nombre_completo;
        $usuarioId = $usuario->id;

        $usuario->delete();

        BitacoraActividad::registrar(
            accion: 'eliminar',
            usuario: $request->user(),
            entidadTipo: 'usuario',
            entidadId: $usuarioId,
            descripcion: "Se eliminó el usuario {$nombre}",
            ip: $request->ip(),
        );

        return ApiResponse::success(
            message: 'Usuario eliminado correctamente.',
        );
    }
}
