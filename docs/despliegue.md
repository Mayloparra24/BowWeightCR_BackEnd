# Despliegue

## Railway

La app está configurada para Railway con Nixpacks.

### Archivos de configuración

**`railway.json`:**
```json
{
  "build": {
    "builder": "NIXPACKS",
    "buildCommand": "composer install --no-dev --optimize-autoloader && php artisan optimize && php artisan scribe:generate"
  },
  "deploy": {
    "startCommand": "bash start.sh",
    "healthcheckPath": "/",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

**`start.sh`:**
```bash
# Escribe .env desde variables de Railway
printenv | grep -v "DEPLOYMENT\|RAILWAY_" > .env
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
```

### Variables de entorno requeridas en Railway

| Variable | Ejemplo | Notas |
|---|---|---|
| `APP_KEY` | `base64:...` | Generar con `php artisan key:generate` |
| `APP_ENV` | `production` | |
| `APP_DEBUG` | `false` | |
| `APP_URL` | `https://tu-app.up.railway.app` | |
| `DB_CONNECTION` | `mysql` | |
| `DB_HOST` | `mysql.railway.internal` | |
| `DB_PORT` | `3306` | |
| `DB_DATABASE` | `railway` | |
| `DB_USERNAME` | `root` | |
| `DB_PASSWORD` | *(de Railway)* | |
| `FILESYSTEM_DISK` | `r2` | |
| `R2_ACCESS_KEY_ID` | *(de Cloudflare)* | |
| `R2_SECRET_ACCESS_KEY` | *(de Cloudflare)* | |
| `R2_BUCKET` | *(nombre del bucket)* | |
| `R2_ENDPOINT` | `https://...` | |
| `R2_URL` | *(URL pública)* | |
| `ML_SERVICE_BASE_URL` | `https://...ngrok-free.dev` | |
| `ML_SERVICE_ENDPOINT` | `/api/v1/predict-weight` | |
| `QUEUE_CONNECTION` | `database` | |

### Post-deploy manual (Railway console)

Después del primer despliegue, ejecutar en Railway Console:

```bash
php artisan migrate --force --seed
php artisan scribe:generate
php artisan queue:work --tries=3 &
```

### Notas importantes

- `openapi.yaml` **no está** en el repositorio. Se genera en el build (via `php artisan scribe:generate`), pero si el build falla, se puede regenerar manualmente después del deploy.
- Si se usa el modo offline de estimación de peso, **debe ejecutarse** `php artisan queue:work` en Railway.
- Railway asigna el puerto automáticamente via `$PORT`.

## Local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## CI/CD

### Tests (GitHub Actions)

`.github/workflows/tests.yml` ejecuta `php artisan test` en PHP 8.4 sobre Ubuntu en cada push a `main` y `develop`.

### Build sin dev dependencies

```bash
composer install --no-dev --optimize-autoloader
php artisan optimize
php artisan scribe:generate
```

## Migraciones

```bash
# Desarrollo
php artisan migrate

# Producción (Railway)
php artisan migrate --force --seed
```

### Seeders disponibles

| Seeder | Descripción |
|---|---|
| `RazaSeeder` | 16 razas bovinas costarricenses con constantes de peso |
| `UsuarioSeeder` | Usuarios demo: ganadero, veterinario, admin, asistente |
| `FincaSeeder` | Fincas demo |
| `BovinoSeeder` | Bovinos demo |

### Usuarios demo

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@bovweight.com` | `password` |
| Ganadero | `ganadero@bovweight.com` | `password` |
| Veterinario | `veterinario@bovweight.com` | `password` |
| Asistente | `asistente@bovweight.com` | `password` |
