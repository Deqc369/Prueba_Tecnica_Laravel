<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Autor extends Model
{
    use HasFactory;
    protected $table = 'table_autores';
    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'nacionalidad',
        'biografia',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function libros(): BelongsToMany
    {
        return $this->belongsToMany(Libro::class, 'autor_libro')
                    ->using(AutorLibro::class)
                    ->withPivot('orden_autor')
                    ->withTimestamps();
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }
}
