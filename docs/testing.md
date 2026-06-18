# Pruebas

## Ejecutar tests

```bash
php artisan test
```

O con PHPUnit directamente:

```bash
vendor/bin/phpunit
```

### Tests con cobertura

```bash
vendor/bin/phpunit --coverage-html coverage
```

## Configuración

Los tests usan SQLite en memoria:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
<env name="QUEUE_CONNECTION" value="sync"/>
```

## Estructura

```
tests/
├── Feature/           # Tests de integración (controladores, API)
│   ├── AuthControllerTest.php
│   ├── PasswordChangeTest.php
│   ├── UsuarioControllerTest.php
│   ├── BovinoControllerTest.php
│   ├── FincaControllerTest.php
│   ├── PesajeControllerTest.php
│   ├── WeightEstimationTest.php
│   └── AsignacionVeterinarioTest.php
└── Unit/              # Tests unitarios (servicios, adapters)
    ├── MLServiceAdapterTest.php
    ├── ImageStorageServiceTest.php
    └── EstimationStrategiesTest.php
```

### Convenciones

- Los Feature tests usan `RefreshDatabase` y `Sanctum::actingAs()` para autenticación.
- Los Unit tests no tocan la base de datos ni el router.
- Se prueba el formato de respuesta JSON (`assertJsonStructure`).
- Se prueba el código de estado HTTP (`assertStatus`).

## CI (GitHub Actions)

El workflow `.github/workflows/tests.yml`:

1. Checkout del código
2. Instala PHP 8.4 con extensiones SQLite
3. `composer install`
4. `cp .env.example .env && php artisan key:generate`
5. `php artisan test`

Se ejecuta en cada push a `main` y `develop`, y en cada PR hacia esas ramas.

## Agregar un nuevo test

```php
<?php
declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class NuevoFeatureTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_algo_funciona(): void
    {
        $response = $this->postJson('/api/ruta', [...]);
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data']);
    }
}
```

```php
<?php
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class NuevoUnitTest extends TestCase
{
    public function test_algo_aislado(): void
    {
        $this->assertTrue(true);
    }
}
```

## Cobertura actual

- Autenticación (login, logout, me, credenciales inválidas, usuario inactivo)
- Cambio de contraseña (primer inicio, revocación de tokens, validación)
- CRUD de usuarios (creación, roles válidos/inválidos, auto-eliminación)
- CRUD de fincas y bovinos
- Pesajes manuales, corrección, historial
- Estimación de peso (online y offline)
- Asignación de veterinarios
- Adaptador ML service
- Servicio de almacenamiento de imágenes
