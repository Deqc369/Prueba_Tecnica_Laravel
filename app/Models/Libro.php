<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Libro extends Model
{
    use HasFactory, SoftDeletes;

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

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_libro')
                    ->using(AutorLibro::class)
                    ->withPivot('orden_autor')
                    ->withTimestamps();
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    // Scopes
    public function scopeDisponibles($query)
    {
        return $query->where('stock_disponible', '>', 0);
    }

    public function scopePorAnio($query, $anio)
    {
        return $query->where('anio_publicacion', $anio);
    }

    public function scopePorAutor($query, $autorId)
    {
        return $query->whereHas('autores', function ($q) use ($autorId) {
            $q->where('autores.id', $autorId);
        });
    }

    public function scopeConFiltros($query, array $filtros)
    {
        return $query->when($filtros['titulo'] ?? null, function ($q, $titulo) {
            $q->where('titulo', 'like', "%{$titulo}%");
        })
        ->when($filtros['autor'] ?? null, function ($q, $autorId) {
            $q->whereHas('autores', function ($q2) use ($autorId) {
                $q2->where('autores.id', $autorId);
            });
        })
        ->when($filtros['anio'] ?? null, function ($q, $anio) {
            $q->where('anio_publicacion', $anio);
        });
    }

    // Accessor
    public function getDisponibleAttribute(): bool
    {
        return $this->stock_disponible > 0;
    }

    // Mutator para ISBN
    public function setIsbnAttribute($value): void
    {
        $this->attributes['isbn'] = strtoupper(str_replace(' ', '', $value));
    }
}
