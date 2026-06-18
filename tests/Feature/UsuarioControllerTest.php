<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Bovino;
use App\Models\Finca;
use App\Models\Raza;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UsuarioControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_create_an_assistant_user(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/usuarios', [
            'nombre_completo' => 'Asistente de Prueba',
            'correo_electronico' => 'asistente@test.cr',
            'contrasena' => 'Password123!',
            'rol' => 'asistente',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.rol', 'asistente');

        $this->assertDatabaseHas('usuarios', [
            'correo_electronico' => 'asistente@test.cr',
            'rol' => 'asistente',
        ]);
    }

    #[Test]
    public function admin_can_update_user_role_to_assistant(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        $user = Usuario::factory()->ganadero()->create();
        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/usuarios/{$user->id}", [
            'rol' => 'asistente',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.rol', 'asistente');
    }

    #[Test]
    public function assistant_is_rejected_by_validation_when_role_is_invalid(): void
    {
        $admin = Usuario::factory()->administrador()->create();
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/usuarios', [
            'nombre_completo' => 'Usuario Inválido',
            'correo_electronico' => 'invalido@test.cr',
            'contrasena' => 'Password123!',
            'rol' => 'superadmin',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['rol']);
    }

    #[Test]
    public function assistant_can_create_farm(): void
    {
        $asistente = Usuario::factory()->asistente()->create();
        Sanctum::actingAs($asistente);

        $response = $this->postJson('/api/fincas', [
            'nombre_finca' => 'Finca del Asistente',
            'ubicacion' => 'Guanacaste',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.propietario_id', $asistente->id);
    }

    #[Test]
    public function assistant_can_create_bovine_in_owned_farm(): void
    {
        $asistente = Usuario::factory()->asistente()->create();
        $finca = Finca::factory()->create(['propietario_id' => $asistente->id]);
        $raza = Raza::factory()->create();
        Sanctum::actingAs($asistente);

        $response = $this->postJson('/api/bovinos', [
            'finca_id' => $finca->id,
            'raza_id' => $raza->id,
            'numero_arete' => 'ARETE-001',
            'nombre_animal' => 'Bovino Test',
            'sexo' => 'macho',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.finca_id', $finca->id);
    }

    #[Test]
    public function assistant_cannot_access_foreign_farm(): void
    {
        $asistente = Usuario::factory()->asistente()->create();
        $otro = Usuario::factory()->ganadero()->create();
        $fincaAjena = Finca::factory()->create(['propietario_id' => $otro->id]);
        Sanctum::actingAs($asistente);

        $response = $this->getJson("/api/fincas/{$fincaAjena->id}");

        $response->assertForbidden();
    }

    #[Test]
    public function assistant_can_list_only_owned_farms(): void
    {
        $asistente = Usuario::factory()->asistente()->create();
        $fincaPropia = Finca::factory()->create(['propietario_id' => $asistente->id]);
        $otro = Usuario::factory()->ganadero()->create();
        Finca::factory()->create(['propietario_id' => $otro->id]);
        Sanctum::actingAs($asistente);

        $response = $this->getJson('/api/fincas');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $fincaPropia->id);
    }
}
