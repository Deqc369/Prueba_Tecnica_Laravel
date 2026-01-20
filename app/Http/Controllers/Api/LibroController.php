<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreLibroRequest;
use App\Http\Requests\UpdateLibroRequest;
use App\Models\Libro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LibroController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $filtros = $request->only(['titulo', 'anio_publicacion']);

            $libros = Libro::with('autores')
                ->conFiltros($filtros)
                ->paginate($perPage);

            return $this->sendResponse($libros, 'Libros obtenidos exitosamente', 200);
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener libros', [], 500);
        }
    }

    public function show($id)
    {
        try {
            $libro = Libro::with('autores')->findOrFail($id);
            return $this->sendResponse($libro, 'Libro obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Libro no encontrado', [], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            // $validated = $request->validated([
            //     'titulo' => 'required|string|max:255',
            //     'isbn' => 'nullable|string|max:17',                
            //     'anio_publicacion' => 'nullable|integer',
            // ]);
            // if(!$validated){
            //     return $this->sendError('F', $request->errors(), 422);
            // }
            $validador = Validator::make(
                $request->all(),
                [
                    'titulo' => 'required|string|max:255',
                    'isbn' => 'nullable|string|max:17',
                    'anio_publicacion' => 'nullable|integer',
                ],
                [
                    'titulo.required' => 'El título es obligatorio.',
                    'isbn.required' => 'El Numero ISBN es obligatorio.',
                    'anio_publicacion.required' => 'El año de publicación es obligatorio.',
                ]
            );
            if ($validador->fails()) {
                return $this->sendError('Error de validación', $validador->errors(), 422);
            }
            $libro = Libro::create($validador->validated());

            if ($request->has('autores')) {
                $autoresConOrden = collect($request->autores)->mapWithKeys(function ($autorId, $index) {
                    return [$autorId => ['orden_autor' => $index + 1]];
                });
                $libro->autores()->sync($autoresConOrden);
            }

            $libro->load('autores');
            return $this->sendResponse($libro, 'Libro creado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->sendError('Error al crear libro', [], 500);
        }
    }

    public function update(Request $request, $id)
    {
        //try {
        $libro = Libro::findOrFail($id);
        // $validated = $request->validated([
        //     'titulo' => 'required|string|max:255',
        //     'isbn' => 'nullable|string|max:17',                
        //     'anio_publicacion' => 'nullable|integer',
        // ]);
        // $libro->update($validated);
        $validator = Validator::make($request->all(), [
            'titulo' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:17',
            'anio_publicacion' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError(
                'Error de validación',
                $validator->errors(),
                422
            );
        }

        $validated = $validator->validated();

        if ($request->has('autores')) {
            $autoresConOrden = collect($request->autores)->mapWithKeys(function ($autorId, $index) {
                return [$autorId => ['orden_autor' => $index + 1]];
            });
            $libro->autores()->sync($autoresConOrden);
        }

        $libro->load('autores');
        return $this->sendResponse($libro, 'Libro actualizado exitosamente');
        //} 
        // catch (\Exception $e) {
        //     return $this->sendError('Error al actualizar libro', [], 500);
        // }
    }

    public function destroy($id)
    {
        try {
            $libro = Libro::findOrFail($id);

            // Verificar si tiene préstamos activos
            if ($libro->prestamos()->where('estado', 'activo')->exists()) {
                return $this->sendError('No se puede eliminar un libro con préstamos activos', [], 400);
            }

            $libro->delete();
            return $this->sendResponse(null, 'Libro eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al eliminar libro', [], 500);
        }
    }
}