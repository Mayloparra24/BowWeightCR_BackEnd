# Referencia de la API REST

Base URL: `http://localhost:8000/api` (local) o `https://tu-app.up.railway.app/api` (producción).

## Convenciones

### Formato de respuesta

Todas las respuestas siguen una estructura uniforme:

```json
{
  "success": true|false,
  "message": "Mensaje descriptivo",
  "data": { ... } | [ ... ] | null
}
```

### Autenticación

Las rutas protegidas requieren el header:

```
Authorization: Bearer {token}
```

El token se obtiene de `POST /api/login` (ver `data.token`).

### Errores

| Código | Significado |
|---|---|
| 401 | Credenciales inválidas |
| 403 | Cuenta desactivada o sin permisos |
| 422 | Validación fallida |
| 429 | Demasiadas solicitudes (rate limit) |
| 500 | Error interno del servidor |

---

## Autenticación

### `POST /api/login`

Inicia sesión con correo y contraseña. Rate limit: 5 intentos/minuto.

**Request:**
```json
{
  "correo_electronico": "ganadero@bovweight.com",
  "contrasena": "password"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Sesión iniciada correctamente.",
  "data": {
    "token": "1|abc123def456...",
    "usuario": {
      "id": 1,
      "nombre_completo": "Iván Chavarría",
      "correo_electronico": "ganadero@bovweight.com",
      "rol": "ganadero",
      "esta_activo": true,
      "debe_cambiar_contrasena": false,
      "correo_verificado_en": null,
      "creado_en": "2026-06-17T00:00:00+00:00"
    }
  }
}
```

### `POST /api/logout`

Cierra sesión y revoca el token actual.

**Headers:** `Authorization: Bearer {token}`

### `GET /api/me`

Obtiene el perfil del usuario autenticado.

**Headers:** `Authorization: Bearer {token}`

### `POST /api/cambiar-contrasena`

Cambia la contraseña. Revoca todos los tokens anteriores y devuelve uno nuevo.

**Headers:** `Authorization: Bearer {token}`

**Request:**
```json
{
  "contrasena_actual": "current_password",
  "nueva_contrasena": "new_password"
}
```

---

## Fincas

Todas requieren `Authorization: Bearer {token}`.

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/fincas` | Listar fincas del usuario |
| POST | `/api/fincas` | Crear finca |
| GET | `/api/fincas/{id}` | Ver detalle de finca |
| PUT | `/api/fincas/{id}` | Actualizar finca |
| DELETE | `/api/fincas/{id}` | Eliminar finca (hard delete) |

**POST/PUT request:**
```json
{
  "nombre": "Finca La Esperanza",
  "ubicacion": "San Carlos, Alajuela",
  "canton": "San Carlos",
  "provincia": "Alajuela",
  "area_hectareas": 50.5
}
```

---

## Bovinos

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/bovinos` | Listar bovinos |
| POST | `/api/bovinos` | Registrar bovino |
| GET | `/api/bovinos/{id}` | Ver detalle del bovino |
| PUT | `/api/bovinos/{id}` | Actualizar bovino |
| DELETE | `/api/bovinos/{id}` | Eliminar bovino (hard delete) |
| PATCH | `/api/bovinos/{id}/inactivar` | Marcar como inactivo |
| PATCH | `/api/bovinos/{id}/activar` | Reactivar bovino |

**POST/PUT request:**
```json
{
  "numero_arete": "CR-1234",
  "nombre": "Torito",
  "sexo": "M",
  "fecha_nacimiento": "2022-05-15",
  "finca_id": 1,
  "raza_id": 3,
  "notas": "Comprado en subasta"
}
```

---

## Pesajes

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/bovinos/{bovino}/pesajes` | Historial de pesajes de un bovino |
| POST | `/api/pesajes` | Registrar pesaje manual |
| PUT | `/api/pesajes/{id}/corregir` | Corregir un pesaje existente |
| POST | `/api/pesajes/estimar` | Estimar peso por IA. Rate limit: 10/min |

**POST `/api/pesajes` — request:**
```json
{
  "bovino_id": 1,
  "peso_kg": 450.5,
  "unidad_medida": "kg",
  "observaciones": "Pesaje rutinario"
}
```

**POST `/api/pesajes/estimar` — request (multipart/form-data):**
| Campo | Tipo | Descripción |
|---|---|---|
| `bovino_id` | integer | ID del bovino |
| `raza_id` | integer | ID de la raza (para constante de peso) |
| `foto` | file | Imagen del bovino (JPEG/PNG) |
| `modo_offline` | boolean | Procesar en cola en lugar de síncrono (opcional) |

---

## Veterinarios

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/fincas/{finca}/veterinarios` | Listar veterinarios activos de una finca |
| POST | `/api/fincas/{finca}/veterinarios` | Asignar veterinario a finca |
| DELETE | `/api/fincas/{finca}/veterinarios/{id}` | Remover veterinario (borrado lógico) |

**POST request:**
```json
{
  "veterinario_id": 5
}
```

---

## Razas

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/razas` | Listar todas las razas |

---

## Usuarios (solo administradores)

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/usuarios` | Listar usuarios (paginado) |
| POST | `/api/usuarios` | Crear usuario |
| GET | `/api/usuarios/{id}` | Ver detalle |
| PUT | `/api/usuarios/{id}` | Actualizar usuario |
| DELETE | `/api/usuarios/{id}` | Eliminar usuario (no permite autoeliminarse) |

**POST request:**
```json
{
  "nombre_completo": "Nuevo Usuario",
  "correo_electronico": "nuevo@bovweight.com",
  "contrasena": "securepassword",
  "rol": "ganadero"
}
```

Al crear un usuario, `debe_cambiar_contrasena` se establece en `true`.

---

## Bitácora (solo administradores)

| Método | Ruta | Descripción |
|---|---|---|
| GET | `/api/bitacora` | Listar eventos de auditoría (paginado y filtrable) |
