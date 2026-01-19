<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLibroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'isbn' => 'required|string|max:17|unique:libros,isbn',
            'anio_publicacion' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'numero_paginas' => 'nullable|integer|min:1',
            'descripcion' => 'nullable|string',
            'stock_disponible' => 'required|integer|min:0',
            'autores' => 'nullable|array',
            'autores.*' => 'exists:autores,id',
        ];
    }
}