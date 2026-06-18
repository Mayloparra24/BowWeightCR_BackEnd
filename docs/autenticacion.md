# Autenticación y roles

## Sanctum

El API usa **Laravel Sanctum** con tokens bearer. No se requiere CSRF ni cookies para consumo desde la APK/mobile.

### Flujo de autenticación

1. El cliente envía `correo_electronico` + `contrasena` a `POST /api/login`.
2. El servidor valida las credenciales contra `contrasena_hash`.
3. Si las credenciales son correctas y el usuario está activo, genera un token con `createToken('api-token')`.
4. El cliente almacena el token y lo envía en todas las solicitudes posteriores como `Authorization: Bearer {token}`.
5. Para cerrar sesión: `POST /api/logout` (revoca el token actual).

### Rate limiting

| Endpoint | Límite | Período |
|---|---|---|
| `POST /api/login` | 5 intentos | 1 minuto por IP |
| `POST /api/pesajes/estimar` | 10 solicitudes | 1 minuto por usuario/IP |

## Roles

| Rol | Descripción |
|---|---|
| `administrador` | Acceso total. Puede CRUD de usuarios, ver bitácora de auditoría, gestionar cualquier finca/bovino/pesaje. |
| `ganadero` | Dueño de fincas. CRUD completo de sus fincas, bovinos, pesajes. Puede asignar veterinarios a sus fincas. |
| `asistente` | Mismos permisos que `ganadero`. Puede crear y gestionar sus propias fincas, bovinos y pesajes. |
| `veterinario` | Solo puede acceder a las fincas donde está asignado. Puede ver bovinos y registrar pesajes en esas fincas. |

### Asignación de veterinarios

Un `ganadero` o `administrador` puede asignar veterinarios a una finca mediante:

```
POST /api/fincas/{finca}/veterinarios
{ "veterinario_id": 5 }
```

El veterinario obtiene acceso de lectura/escritura a esa finca y sus bovinos. Si se remueve la asignación, el veterinario pierde el acceso.

## Cambio de contraseña en primer inicio

Los usuarios creados por un administrador se crean con `debe_cambiar_contrasena = true`.

El flujo esperado:

1. Admin crea usuario via `POST /api/usuarios`.
2. El usuario inicia sesión. La respuesta incluye `debe_cambiar_contrasena: true`.
3. El frontend detecta la bandera y redirige a la pantalla de cambio de contraseña.
4. El usuario envía `POST /api/cambiar-contrasena` con `contrasena_actual` y `nueva_contrasena`.
5. El servidor valida la contraseña actual (`Hash::check`), revoca **todos** los tokens existentes, y emite uno nuevo.
6. El cliente debe almacenar el nuevo token y descartar el anterior.

### Endpoints

| Método | Ruta | Descripción |
|---|---|---|
| POST | `/api/login` | Iniciar sesión |
| POST | `/api/logout` | Cerrar sesión |
| GET | `/api/me` | Perfil del usuario autenticado |
| POST | `/api/cambiar-contrasena` | Cambiar contraseña |

## Creación de usuarios

Solo `administrador` puede crear usuarios via `POST /api/usuarios`.

```json
{
  "nombre_completo": "Nuevo Usuario",
  "correo_electronico": "nuevo@bovweight.com",
  "contrasena": "securepassword",
  "rol": "ganadero"
}
```

El sistema valida que el rol sea uno de: `administrador`, `ganadero`, `veterinario`, `asistente`.

La contraseña debe cumplir con: mínimo 8 caracteres (regla `Password::min(8)` de Laravel).
