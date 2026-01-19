<?php

namespace App\Services;

use App\Models\Autor;

class AutorService
{
    public function puedeEliminar(Autor $autor): bool
    {
        return !$autor->libros()->exists();
    }
}