<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UsuarioFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


#[Fillable([
    'nombre_completo',
    'correo_electronico',
    'contrasena_hash',
    'rol',
    'esta_activo',
    'debe_cambiar_contrasena',
])]
#[Hidden(['contrasena_hash', 'remember_token'])]
class Usuario extends Authenticatable
{
    /** @use HasFactory<UsuarioFactory> */
    use HasApiTokens,HasFactory, Notifiable;

    protected $table = 'usuarios';


    protected function casts(): array
    {
        return [
            'correo_verificado_en' => 'datetime',
            'contrasena_hash' => 'hashed',
            'esta_activo' => 'boolean',
            'debe_cambiar_contrasena' => 'boolean',
        ];
    }

    public function getAuthPassword(): string
{
    return $this->contrasena_hash;
}

public function getAuthIdentifierName(): string
{
    return 'correo_electronico';
}
    public function fincas(): HasMany
    {
        return $this->hasMany(Finca::class, 'propietario_id');
    }

    public function asignacionesComoVeterinario(): HasMany
    {
        return $this->hasMany(AsignacionVeterinario::class, 'veterinario_id');
    }

    public function asignacionesCreadas(): HasMany
    {
        return $this->hasMany(AsignacionVeterinario::class, 'asignado_por');
    }

    public function fotografias(): HasMany
    {
        return $this->hasMany(Fotografia::class, 'capturada_por');
    }

    public function pesajes(): HasMany
    {
        return $this->hasMany(RegistroPesaje::class, 'creado_por');
    }

    public function recordatorios(): HasMany
    {
        return $this->hasMany(Recordatorio::class, 'usuario_id');
    }

    public function bitacoras(): HasMany
    {
        return $this->hasMany(BitacoraActividad::class, 'usuario_id');
    }

    public function esGanadero(): bool
    {
        return $this->rol === 'ganadero';
    }

    public function esVeterinario(): bool
    {
        return $this->rol === 'veterinario';
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esAsistente(): bool
    {
        return $this->rol === 'asistente';
    }
}
