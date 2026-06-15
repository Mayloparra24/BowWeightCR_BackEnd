<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Raza extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre_raza',
        'enfoque',
        'constante_peso',
        'descripcion',
    ];

    protected function casts(): array
    {
        return [
            'constante_peso' => 'decimal:2',
        ];
    }

    public function bovinos(): HasMany
    {
        return $this->hasMany(Bovino::class);
    }
}
