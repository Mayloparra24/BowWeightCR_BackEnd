# Guía para Persona A - BovWeight Backend

## Estado actual

La Persona B (Pesajes + IA + Colas + Almacenamiento R2) ya terminó su dominio. Este README resume lo que debés hacer vos para completar el backend.

## Tareas pendientes

### 1. Autenticación con Sanctum
- Implementar registro de usuarios (`POST /api/register`).
- Implementar login (`POST /api/login`).
- Implementar logout (`POST /api/logout`).
- Asignar roles: `ganadero`, `veterinario`, `administrador`.

### 2. CRUD de Fincas
- `GET /api/fincas`
- `POST /api/fincas`
- `PUT /api/fincas/{id}`
- `DELETE /api/fincas/{id}`

### 3. CRUD de Bovinos
- `GET /api/bovinos`
- `POST /api/bovinos`
- `GET /api/bovinos/{id}`
- `PUT /api/bovinos/{id}`
- `DELETE /api/bovinos/{id}` (marcar como inactivo en vez de eliminar)

### 4. Asignación de Veterinarios
- `POST /api/fincas/{finca}/veterinarios/{veterinario}`
- `GET /api/veterinarios/fincas`

### 5. Proteger endpoints existentes
Agregar middleware `auth:sanctum` a las rutas en `routes/api.php`:
- `/api/pesajes/*`
- `/api/bovinos/{bovino}/pesajes`
- `/api/pesajes/estimar`

## Modelos ya creados

| Modelo | Tabla |
|---|---|
| `Usuario` | `usuarios` |
| `Finca` | `fincas` |
| `Bovino` | `bovinos` |
| `Raza` | `razas` |
| `AsignacionVeterinario` | `asignaciones_veterinarios` |
| `Fotografia` | `fotografias` |
| `RegistroPesaje` | `registros_pesaje` |
| `Recordatorio` | `recordatorios` |
| `BitacoraActividad` | `bitacora_actividades` |

## Variables de entorno necesarias

Ver `.env.example`. Las más importantes para producción:

```env
DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

FILESYSTEM_DISK=r2
R2_ACCESS_KEY_ID=
R2_SECRET_ACCESS_KEY=
R2_REGION=auto
R2_BUCKET=
R2_ENDPOINT=
R2_URL=
R2_USE_PATH_STYLE_ENDPOINT=true

ML_SERVICE_BASE_URL=https://unranked-mystified-thesaurus.ngrok-free.dev
ML_SERVICE_ENDPOINT=/api/v1/predict-weight
ML_SERVICE_TIMEOUT=15

QUEUE_CONNECTION=database
```

## Cómo probar localmente

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Notas importantes

- Los modelos `Finca` y `Bovino` ya tienen sus relaciones definidas.
- El modelo `Bovino` tiene métodos helper `estaActivo()`, `marcarInactivo(motivo)`, `marcarActivo()`.
- Los endpoints de Persona B están funcionando sin autenticación por ahora, listos para que les agregues el middleware.
- Las credenciales reales de la base de datos y R2 se pasan por aparte (no van en Git).
