<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Finca extends Model
{
    use HasFactory;

    protected $fillable = [
        'propietario_id',
        'nombre_finca',
        'ubicacion',
        'canton',
        'provincia',
        'esta_activa',
    ];

    protected function casts(): array
    {
        return [
            'esta_activa' => 'boolean',
        ];
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'propietario_id');
    }

    public function bovinos(): HasMany
    {
        return $this->hasMany(Bovino::class);
    }

    public function veterinarios(): HasMany
    {
        return $this->hasMany(AsignacionVeterinario::class);
    }

    public function recordatorios(): HasMany
    {
        return $this->hasMany(Recordatorio::class);
    }

    public function asignaciones(): HasMany
    {
    return $this->hasMany(AsignacionVeterinario::class);    
    }
}
