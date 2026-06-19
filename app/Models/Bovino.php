<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bovino extends Model
{
    use HasFactory;

    protected $fillable = [
        'finca_id',
        'raza_id',
        'numero_arete',
        'nombre_animal',
        'sexo',
        'fecha_nacimiento',
        'estado',
        'motivo_inactividad',
        'fecha_inactividad',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
            'fecha_inactividad' => 'date',
        ];
    }

    public function finca(): BelongsTo
    {
        return $this->belongsTo(Finca::class);
    }

    public function raza(): BelongsTo
    {
        return $this->belongsTo(Raza::class);
    }

    public function fotografias(): HasMany
    {
        return $this->hasMany(Fotografia::class);
    }

    public function pesajes(): HasMany
    {
        return $this->hasMany(RegistroPesaje::class);
    }

    public function recordatorios(): HasMany
    {
        return $this->hasMany(Recordatorio::class);
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function identificacionBitacora(): string
    {
        if ($this->nombre_animal) {
            return "{$this->numero_arete} ({$this->nombre_animal})";
        }

        return $this->numero_arete;
    }

    public function marcarInactivo(string $motivo): void
    {
        $this->estado = 'inactivo';
        $this->motivo_inactividad = $motivo;
        $this->fecha_inactividad = now()->toDateString();
        $this->save();
    }

    public function marcarActivo(): void
    {
        $this->estado = 'activo';
        $this->motivo_inactividad = null;
        $this->fecha_inactividad = null;
        $this->save();
    }
}
