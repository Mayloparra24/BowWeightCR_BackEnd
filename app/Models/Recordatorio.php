<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recordatorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'bovino_id',
        'finca_id',
        'titulo',
        'tipo_frecuencia',
        'proximo_recordatorio_el',
        'esta_activo',
    ];

    protected function casts(): array
    {
        return [
            'proximo_recordatorio_el' => 'datetime',
            'esta_activo' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function bovino(): BelongsTo
    {
        return $this->belongsTo(Bovino::class);
    }

    public function finca(): BelongsTo
    {
        return $this->belongsTo(Finca::class);
    }
}
