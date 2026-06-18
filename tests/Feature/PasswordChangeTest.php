<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function new_user_has_must_change_password_flag(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/usuarios', [
            'nombre_completo' => 'Usuario Nuevo',
            'correo_electronico' => 'nuevo@test.cr',
            'contrasena' => 'Password123!',
            'rol' => 'ganadero',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.debe_cambiar_contrasena', true);

        $this->assertDatabaseHas('usuarios', [
            'correo_electronico' => 'nuevo@test.cr',
            'debe_cambiar_contrasena' => true,
        ]);
    }

    #[Test]
    public function login_response_includes_must_change_password_flag(): void
    {
        $usuario = Usuario::factory()->create([
            'correo_electronico' => 'test@example.com',
            'contrasena_hash' => 'secret123',
            'debe_cambiar_contrasena' => true,
            'esta_activo' => true,
        ]);

        $response = $this->postJson('/api/login', [
            'correo_electronico' => 'test@example.com',
            'contrasena' => 'secret123',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.usuario.debe_cambiar_contrasena', true);
    }

    #[Test]
    public function me_response_includes_must_change_password_flag(): void
    {
        $usuario = Usuario::factory()->create([
            'debe_cambiar_contrasena' => true,
        ]);
        Sanctum::actingAs($usuario);

        $response = $this->getJson('/api/me');

        $response->assertOk()
            ->assertJsonPath('data.debe_cambiar_contrasena', true);
    }

    #[Test]
    public function user_can_change_password_with_valid_current_password(): void
    {
        $usuario = Usuario::factory()->create([
            'contrasena_hash' => 'oldpassword123',
            'debe_cambiar_contrasena' => true,
        ]);
        Sanctum::actingAs($usuario);

        $response = $this->postJson('/api/cambiar-contrasena', [
            'contrasena_actual' => 'oldpassword123',
            'contrasena_nueva' => 'newpassword456',
            'contrasena_nueva_confirmation' => 'newpassword456',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.usuario.debe_cambiar_contrasena', false)
            ->assertJsonStructure(['data' => ['token', 'usuario']]);

        $this->assertFalse($usuario->fresh()->debe_cambiar_contrasena);
    }

    #[Test]
    public function password_change_fails_with_invalid_current_password(): void
    {
        $usuario = Usuario::factory()->create([
            'contrasena_hash' => 'oldpassword123',
        ]);
        Sanctum::actingAs($usuario);

        $response = $this->postJson('/api/cambiar-contrasena', [
            'contrasena_actual' => 'wrongpassword',
            'contrasena_nueva' => 'newpassword456',
            'contrasena_nueva_confirmation' => 'newpassword456',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['contrasena_actual']);
    }

    #[Test]
    public function password_change_revokes_all_tokens_and_returns_new_one(): void
    {
        $usuario = Usuario::factory()->create([
            'contrasena_hash' => 'oldpassword123',
            'debe_cambiar_contrasena' => true,
        ]);
        Sanctum::actingAs($usuario);

        $response = $this->postJson('/api/cambiar-contrasena', [
            'contrasena_actual' => 'oldpassword123',
            'contrasena_nueva' => 'newpassword456',
            'contrasena_nueva_confirmation' => 'newpassword456',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    #[Test]
    public function admin_password_reset_sets_must_change_password_flag(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $usuario = Usuario::factory()->create([
            'debe_cambiar_contrasena' => false,
        ]);
        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/usuarios/{$usuario->id}", [
            'contrasena' => 'ResetPassword123!',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.debe_cambiar_contrasena', true);

        $this->assertTrue($usuario->fresh()->debe_cambiar_contrasena);
    }
}
