<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistroPesaje extends Model
{
    use HasFactory;

    protected $table = 'registros_pesaje';

    protected $fillable = [
        'bovino_id',
        'fotografia_id',
        'creado_por',
        'peso_estimado',
        'peso_registrado',
        'es_correccion_manual',
        'tipo_pesaje',
        'notas_correccion',
        'confianza_ia',
        'registrado_el',
    ];

    protected function casts(): array
    {
        return [
            'peso_estimado' => 'decimal:2',
            'peso_registrado' => 'decimal:2',
            'confianza_ia' => 'decimal:4',
            'es_correccion_manual' => 'boolean',
            'registrado_el' => 'datetime',
        ];
    }

    public function bovino(): BelongsTo
    {
        return $this->belongsTo(Bovino::class);
    }

    public function fotografia(): BelongsTo
    {
        return $this->belongsTo(Fotografia::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'creado_por');
    }

    public function esCorreccion(): bool
    {
        return $this->es_correccion_manual;
    }

    public function pesoFinal(): ?float
    {
        return $this->peso_registrado !== null
            ? (float) $this->peso_registrado
            : ($this->peso_estimado !== null ? (float) $this->peso_estimado : null);
    }
}
