<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\BitacoraActividad;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    #[\Knuckles\Scribe\Attributes\Response(
        content: ['success' => true, 'message' => 'Contraseña actualizada correctamente.', 'data' => [
            'token' => '1|abc123def456...',
            'usuario' => ['id' => 1, 'nombre_completo' => 'Iván Chavarría', 'correo_electronico' => 'ganadero@bovweight.com', 'rol' => 'ganadero', 'esta_activo' => true, 'debe_cambiar_contrasena' => false],
        ]], status: 200,
    )]
    public function cambiarContrasena(Request $request): JsonResponse
    {
        $data = $request->validate([
            'contrasena_actual' => ['required', 'string'],
            'contrasena_nueva' => ['required', 'string', 'confirmed', Password::min(8)],
            'contrasena_nueva_confirmation' => ['required', 'string'],
        ]);

        $usuario = $request->user();

        if (! Hash::check($data['contrasena_actual'], $usuario->contrasena_hash)) {
            throw ValidationException::withMessages([
                'contrasena_actual' => ['La contraseña actual no es correcta.'],
            ]);
        }

        $usuario->update([
            'contrasena_hash' => Hash::make($data['contrasena_nueva']),
            'debe_cambiar_contrasena' => false,
        ]);

        BitacoraActividad::registrar(
            accion: 'cambiar_contrasena',
            usuario: $usuario,
            entidadTipo: 'usuario',
            entidadId: $usuario->id,
            descripcion: "Se cambió la contraseña de {$usuario->nombre_completo}",
            ip: $request->ip(),
        );

        $usuario->tokens()->delete();
        $token = $usuario->createToken('api-token')->plainTextToken;

        return ApiResponse::success(
            data: [
                'token' => $token,
                'usuario' => new UserResource($usuario->fresh()),
            ],
            message: 'Contraseña actualizada correctamente.',
        );
    }
}
