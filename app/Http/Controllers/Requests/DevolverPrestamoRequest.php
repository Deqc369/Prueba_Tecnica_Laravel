<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DevolverPrestamoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // O cambia según tu lógica de autorización
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'observaciones' => 'nullable|string|max:500',
            'estado_libro' => 'required|in:bueno,regular,dañado',
            'fecha_devolucion_real' => 'nullable|date|before_or_equal:today',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'observaciones.string' => 'Las observaciones deben ser texto.',
            'observaciones.max' => 'Las observaciones no pueden exceder 500 caracteres.',
            'estado_libro.required' => 'El estado del libro es obligatorio.',
            'estado_libro.in' => 'El estado del libro debe ser: bueno, regular o dañado.',
            'fecha_devolucion_real.date' => 'La fecha de devolución real debe ser una fecha válida.',
            'fecha_devolucion_real.before_or_equal' => 'La fecha de devolución real no puede ser futura.',
        ];
    }

    /**
     * Get custom attributes for validation errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'observaciones' => 'observaciones',
            'estado_libro' => 'estado del libro',
            'fecha_devolucion_real' => 'fecha de devolución real',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Si no se envía fecha de devolución real, usar fecha actual
        if (!$this->has('fecha_devolucion_real')) {
            $this->merge([
                'fecha_devolucion_real' => now()->toDateString(),
            ]);
        }

        // Asegurar que estado_libro tenga un valor por defecto si no se envía
        if (!$this->has('estado_libro')) {
            $this->merge([
                'estado_libro' => 'bueno',
            ]);
        }
    }
}