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

class AuthController extends Controller
{
    #[\Knuckles\Scribe\Attributes\Response(
        content: [
            'success' => true,
            'message' => 'Sesión iniciada correctamente.',
            'data' => [
                'token' => '1|abc123def456...',
                'usuario' => [
                    'id' => 1,
                    'nombre_completo' => 'Iván Chavarría',
                    'correo_electronico' => 'ganadero@bovweight.com',
                    'rol' => 'ganadero',
                    'esta_activo' => true,
                    'debe_cambiar_contrasena' => false,
                    'correo_verificado_en' => null,
                    'creado_en' => '2026-06-17T00:00:00+00:00',
                ],
            ],
        ],
        status: 200,
    )]
    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => false, 'message' => 'Credenciales incorrectas.', 'error' => null],
        status: 401,
        description: 'Credenciales inválidas',
    )]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'correo_electronico' => ['required', 'email'],
            'contrasena' => ['required', 'string'],
        ]);

        $usuario = Usuario::where('correo_electronico', $request->correo_electronico)->first();

        if (! $usuario || ! Hash::check($request->contrasena, $usuario->contrasena_hash)) {
            BitacoraActividad::registrar(
                accion: 'login_fallido',
                usuario: $usuario,
                entidadTipo: 'sesion',
                descripcion: "Intento de inicio de sesión fallido para {$request->correo_electronico}",
                ip: $request->ip(),
            );

            return ApiResponse::error(
                message: 'Credenciales incorrectas.',
                status: 401,
            );
        }

        if (! $usuario->esta_activo) {
            BitacoraActividad::registrar(
                accion: 'login_fallido',
                usuario: $usuario,
                entidadTipo: 'sesion',
                descripcion: "Intento de inicio de sesión fallido: cuenta desactivada de {$usuario->nombre_completo}",
                ip: $request->ip(),
            );

            return ApiResponse::error(
                message: 'Tu cuenta está desactivada. Contactá al administrador.',
                status: 403,
            );
        }

        $token = $usuario->createToken('api-token')->plainTextToken;

        BitacoraActividad::registrar(
            accion: 'login',
            usuario: $usuario,
            entidadTipo: 'sesion',
            entidadId: $usuario->id,
            descripcion: "Inicio de sesión de {$usuario->nombre_completo}",
            ip: $request->ip(),
        );

        return ApiResponse::success(
            data: [
                'token' => $token,
                'usuario' => new UserResource($usuario),
            ],
            message: 'Sesión iniciada correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Sesión cerrada correctamente.', 'data' => null],
        status: 200,
    )]
    public function logout(Request $request): JsonResponse
    {
        $usuario = $request->user();

        BitacoraActividad::registrar(
            accion: 'logout',
            usuario: $usuario,
            entidadTipo: 'sesion',
            entidadId: $usuario->id,
            descripcion: "Cierre de sesión de {$usuario->nombre_completo}",
            ip: $request->ip(),
        );

        $usuario->currentAccessToken()->delete();

        return ApiResponse::success(
            message: 'Sesión cerrada correctamente.',
        );
    }

    #[\Knuckles\Scribe\Attributes\Response(
        content: [
            'success' => true,
            'message' => 'Usuario autenticado obtenido correctamente.',
            'data' => [
                'id' => 1,
                'nombre_completo' => 'Iván Chavarría',
                'correo_electronico' => 'ganadero@bovweight.com',
                'rol' => 'ganadero',
                'esta_activo' => true,
                'debe_cambiar_contrasena' => false,
                'correo_verificado_en' => null,
                'creado_en' => '2026-06-17T00:00:00+00:00',
            ],
        ],
        status: 200,
    )]
    public function me(Request $request): JsonResponse
    {
        return ApiResponse::resource(
            resource: new UserResource($request->user()),
            message: 'Usuario autenticado obtenido correctamente.',
        );
    }
}