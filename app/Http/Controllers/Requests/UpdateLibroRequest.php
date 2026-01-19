<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLibroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Cambia según tu lógica de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Obtener el ID del libro de la ruta
        $libroId = $this->route('libro') ?? $this->route('id');
        
        return [
            'titulo' => 'sometimes|string|max:255',
            'isbn' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('libros', 'isbn')->ignore($libroId),
            ],
            'anio_publicacion' => 'sometimes|integer|min:1800|max:' . date('Y'),
            'numero_paginas' => 'sometimes|integer|min:1',
            'descripcion' => 'nullable|string',
            'stock_disponible' => 'sometimes|integer|min:0',
            'autores' => 'sometimes|array|min:1',
            'autores.*' => 'exists:autores,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'titulo.string' => 'El título debe ser texto.',
            'titulo.max' => 'El título no puede exceder 255 caracteres.',
            'isbn.string' => 'El ISBN debe ser texto.',
            'isbn.max' => 'El ISBN no puede exceder 20 caracteres.',
            'isbn.unique' => 'Este ISBN ya está registrado.',
            'anio_publicacion.integer' => 'El año de publicación debe ser un número.',
            'anio_publicacion.min' => 'El año debe ser mayor a 1800.',
            'anio_publicacion.max' => 'El año no puede ser futuro.',
            'numero_paginas.integer' => 'El número de páginas debe ser un número.',
            'numero_paginas.min' => 'Debe tener al menos 1 página.',
            'stock_disponible.integer' => 'El stock disponible debe ser un número.',
            'stock_disponible.min' => 'El stock no puede ser negativo.',
            'autores.array' => 'Los autores deben ser enviados como lista.',
            'autores.min' => 'Debe seleccionar al menos un autor.',
            'autores.*.exists' => 'Uno de los autores seleccionados no existe.',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     */
    public function attributes(): array
    {
        return [
            'titulo' => 'título',
            'isbn' => 'ISBN',
            'anio_publicacion' => 'año de publicación',
            'numero_paginas' => 'número de páginas',
            'stock_disponible' => 'stock disponible',
            'autores' => 'autores',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Si no se envía el array de autores, mantener el valor actual
        if (!$this->has('autores')) {
            $this->merge([
                'autores' => null,
            ]);
        }
    }
}