<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fotografia extends Model
{
    use HasFactory;

    protected $fillable = [
        'bovino_id',
        'capturada_por',
        'ruta_archivo',
        'estado_procesamiento',
        'capturada_el',
        'sincronizada_el',
    ];

    protected function casts(): array
    {
        return [
            'capturada_el' => 'datetime',
            'sincronizada_el' => 'datetime',
        ];
    }

    public function bovino(): BelongsTo
    {
        return $this->belongsTo(Bovino::class);
    }

    public function capturadaPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'capturada_por');
    }

    public function pesaje(): HasOne
    {
        return $this->hasOne(RegistroPesaje::class, 'fotografia_id');
    }

    public function marcarComoProcesando(): void
    {
        $this->estado_procesamiento = 'procesando';
        $this->save();
    }

    public function marcarComoCompletado(): void
    {
        $this->estado_procesamiento = 'completado';
        $this->sincronizada_el = now();
        $this->save();
    }

    public function marcarComoFallido(): void
    {
        $this->estado_procesamiento = 'fallido';
        $this->save();
    }
}
