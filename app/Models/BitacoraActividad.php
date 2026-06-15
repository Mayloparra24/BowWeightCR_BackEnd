<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BitacoraActividad extends Model
{
    use HasFactory;

    protected $table = 'bitacora_actividades';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id',
        'accion',
        'entidad_tipo',
        'entidad_id',
        'descripcion',
        'direccion_ip',
        'creada_el',
    ];

    protected function casts(): array
    {
        return [
            'creada_el' => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public static function registrar(
        string $accion,
        ?Usuario $usuario = null,
        ?string $entidadTipo = null,
        ?int $entidadId = null,
        ?string $descripcion = null,
        ?string $ip = null
    ): self {
        return self::create([
            'usuario_id' => $usuario?->id,
            'accion' => $accion,
            'entidad_tipo' => $entidadTipo,
            'entidad_id' => $entidadId,
            'descripcion' => $descripcion,
            'direccion_ip' => $ip,
            'creada_el' => now(),
        ]);
    }
}
