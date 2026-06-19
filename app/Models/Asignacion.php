<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asignacion extends Model
{
    use HasFactory;

    protected $table = 'asignaciones';

    protected $fillable = [
        'usuario_id',
        'finca_id',
        'asignado_por',
        'rol',
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

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
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
