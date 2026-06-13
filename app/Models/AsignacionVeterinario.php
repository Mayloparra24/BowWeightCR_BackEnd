<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsignacionVeterinario extends Model
{
    use HasFactory;

    protected $fillable = [
        'veterinario_id',
        'finca_id',
        'asignado_por',
        'esta_activa',
        'asignado_el',
    ];

    protected function casts(): array
    {
        return [
            'esta_activa' => 'boolean',
            'asignado_el' => 'datetime',
        ];
    }

    public function veterinario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'veterinario_id');
    }

    public function finca(): BelongsTo
    {
        return $this->belongsTo(Finca::class);
    }

    public function asignadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'asignado_por');
    }
}
