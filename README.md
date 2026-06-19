# BovWeightCR Backend

API REST para la gestión de ganadería bovina en Costa Rica. Permite administrar fincas, bovinos, pesajes manuales y estimación de peso mediante inteligencia artificial.

## Stack

| Componente | Tecnología |
|---|---|
| Framework | Laravel 13 |
| PHP | ^8.3 |
| Base de datos | MySQL (producción), SQLite (desarrollo/tests) |
| Autenticación | Laravel Sanctum (tokens bearer) |
| Documentación API | Swagger UI en `/docs` + Scribe |
| Almacenamiento | Cloudflare R2 (S3-compatible) |
| ML Service | Servicio externo para estimación de peso por foto |
| Colas | Base de datos (jobs/tabla) |
| CI/CD | GitHub Actions + Railway |

## Roles

| Rol | Permisos |
|---|---|
| `administrador` | Acceso total a todo el sistema |
| `ganadero` | CRUD de sus fincas, bovinos, pesajes; asigna veterinarios |
| `asistente` | Mismos permisos que ganadero sobre recursos propios |
| `veterinario` | Lectura y creación en fincas/bovinos/pesajes asignados |

## Requisitos

- PHP 8.3+
- Composer
- SQLite (local) o MySQL (producción)
- Extensiones PHP: `sqlite`, `pdo_mysql`, `gd`, `xml`, `curl`, `fileinfo`, `mbstring`

## Inicio rápido

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Prueba que funcione:

```bash
php artisan test
```

## Variables de entorno

Ver `.env.example`. Las más importantes:

### Base de datos
| Variable | Local | Producción (Railway) |
|---|---|---|
| `DB_CONNECTION` | `sqlite` | `mysql` |
| `DB_HOST` | — | `mysql.railway.internal` |
| `DB_PORT` | — | `3306` |
| `DB_DATABASE` | — | `railway` |
| `DB_USERNAME` | — | `root` |
| `DB_PASSWORD` | — | *(de Railway)* |

### Almacenamiento (Cloudflare R2)
```env
FILESYSTEM_DISK=r2
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_BUCKET=
R2_ENDPOINT=
R2_URL=
R2_REGION=auto
R2_USE_PATH_STYLE_ENDPOINT=true
```

### ML Service (estimación de peso por IA)
```env
ML_SERVICE_BASE_URL=https://unranked-mystified-thesaurus.ngrok-free.dev
ML_SERVICE_ENDPOINT=/api/v1/predict-weight
ML_SERVICE_TIMEOUT=15
```

## Documentación de la API

- **Swagger UI interactiva:** visita [`/docs`](http://localhost:8000/docs) en el servidor.
- **Esquema OpenAPI:** `/docs.openapi` (YAML).
- **Guía detallada por recurso:** [`docs/api.md`](docs/api.md).

## Desarrollo

```bash
# Iniciar servidor + colas + logs (recomendado)
composer dev

# Solo servidor
php artisan serve

# Solo colas
php artisan queue:listen --tries=1 --timeout=0

# Tests
php artisan test
```

## Pruebas

```bash
php artisan test
```

Las pruebas usan SQLite en memoria y `RefreshDatabase`. Ver [`docs/testing.md`](docs/testing.md) para más detalles.

## Despliegue

La aplicación está configurada para Railway con Nixpacks. Ver [`docs/despliegue.md`](docs/despliegue.md).

```bash
# Post-deploy (Railway)
php artisan migrate --force --seed
php artisan scribe:generate
php artisan queue:work --tries=3 &
```

## Estructura del proyecto

```
app/
├── Adapters/           # Adaptadores externos (MLServiceAdapter)
├── Console/            # Comandos Artisan
├── Enums/              # Enumeraciones PHP
├── Http/
│   ├── Controllers/    # Controladores
│   ├── Middleware/      # Middleware
│   ├── Resources/      # API Resources
│   └── Requests/       # Form Requests (validación)
├── Jobs/               # Jobs para colas
├── Models/             # Modelos Eloquent
├── Policies/           # Policies de autorización
├── Services/           # Lógica de negocio
└── Support/            # Helpers (ApiResponse)
```

## Licencia

MIT
