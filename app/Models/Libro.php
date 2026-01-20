<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Libro extends Model
{
    use HasFactory;

    protected $table = 'table_libros';

    protected $fillable = [
        'titulo',
        'isbn',
        'anio_publicacion',
        'numero_paginas',
        'descripcion',
        'stock_disponible',
    ];

    protected $casts = [
        'anio_publicacion' => 'integer',
        'numero_paginas' => 'integer',
        'stock_disponible' => 'integer',
    ];

    protected $appends = ['disponible'];

    /* ============================
     | Relaciones
     ============================ */

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(
            Autor::class,
            'table_libros_autores',
            'libro_id',
            'autor_id'
        )
        ->withPivot('orden_autor')
        ->withTimestamps();
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    /* ============================
     | Scopes
     ============================ */

    /**
     * Libros con stock disponible
     */
    public function scopeDisponibles($query)
    {
        return $query->where('stock_disponible', '>', 0);
    }

    /**
     * Libros por año de publicación
     */
    public function scopePorAnio($query, int $anio)
    {
        return $query->where('anio_publicacion', $anio);
    }

    /**
     * Libros por autor
     */
    public function scopePorAutor($query, int $autorId)
    {
        return $query->whereHas('autores', function ($q) use ($autorId) {
            $q->where('table_autores.id', $autorId);
        });
    }

    /**
     * Scope combinado reutilizando scopes existentes
     */
    public function scopeConFiltros($query, array $filtros)
    {
        return $query
            ->when($filtros['titulo'] ?? null, fn ($q, $titulo) =>
                $q->where('titulo', 'ILIKE', "%{$titulo}%")
            )
            ->when($filtros['autor_id'] ?? null, fn ($q, $autorId) =>
                $q->porAutor($autorId)
            )
            ->when($filtros['anio'] ?? null, fn ($q, $anio) =>
                $q->porAnio($anio)
            )
            ->when($filtros['disponibles'] ?? false, fn ($q) =>
                $q->disponibles()
            );
    }

    /* ============================
     | Accessors & Mutators
     ============================ */

    public function getDisponibleAttribute(): bool
    {
        return $this->stock_disponible > 0;
    }

    public function setIsbnAttribute($value): void
    {
        $this->attributes['isbn'] = strtoupper(str_replace(' ', '', $value));
    }
}
