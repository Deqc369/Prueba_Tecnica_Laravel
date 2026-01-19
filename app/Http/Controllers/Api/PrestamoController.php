<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StorePrestamoRequest;
use App\Http\Requests\DevolverPrestamoRequest;
use App\Models\Prestamo;
use App\Models\Libro;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrestamoController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $estado = $request->input('estado');
            $usuario_id = $request->input('usuario_id');
            $libro_id = $request->input('libro_id');
            
            $query = Prestamo::with(['usuario', 'libro.autores'])
                ->orderBy('created_at', 'desc');
            
            // Aplicar filtros
            if ($estado) {
                $query->where('estado', $estado);
            }
            
            if ($usuario_id) {
                $query->where('usuario_id', $usuario_id);
            }
            
            if ($libro_id) {
                $query->where('libro_id', $libro_id);
            }
            
            // Filtrar préstamos vencidos
            if ($request->has('vencidos')) {
                $query->where('estado', 'activo')
                    ->where('fecha_devolucion_esperada', '<', Carbon::now());
            }
            
            $prestamos = $query->paginate($perPage);
            
            return $this->sendResponse($prestamos, 'Préstamos obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener préstamos', [], 500);
        }
    }

    public function store(StorePrestamoRequest $request)
    {
        try {
            $validated = $request->validated();
            $libro = Libro::findOrFail($validated['libro_id']);
            $usuario = User::findOrFail($validated['usuario_id']);
            
            // Regla 1: Verificar stock disponible
            if ($libro->stock_disponible <= 0) {
                return $this->sendError('No hay stock disponible para este libro', [], 400);
            }
            
            // Regla 2: Verificar préstamos activos del usuario
            $prestamosActivos = Prestamo::where('usuario_id', $usuario->id)
                ->where('estado', 'activo')
                ->count();
            if ($prestamosActivos >= 3) {
                return $this->sendError('El usuario tiene el máximo de préstamos activos (3)', [], 400);
            }
            
            // Regla 3: Verificar si el usuario está activo
            if ($usuario->estado !== 'activo') {
                return $this->sendError('El usuario está inactivo', [], 400);
            }
            
            // Crear el préstamo
            $prestamo = Prestamo::create([
                'usuario_id' => $usuario->id,
                'libro_id' => $libro->id,
                'fecha_prestamo' => $validated['fecha_prestamo'] ?? Carbon::now(),
                'fecha_devolucion_esperada' => $validated['fecha_devolucion_esperada'] ?? Carbon::now()->addDays(15),
                'observaciones' => $validated['observaciones'] ?? null,
                'estado' => 'activo',
            ]);
            
            // Actualizar stock del libro
            $libro->decrement('stock_disponible');
            
            $prestamo->load(['usuario', 'libro.autores']);
            
            return $this->sendResponse($prestamo, 'Préstamo creado exitosamente', 201);
        } catch (\Exception $e) {
            return $this->sendError('Error al crear préstamo: ' . $e->getMessage(), [], 500);
        }
    }

    public function devolver(DevolverPrestamoRequest $request, $id)
    {
        try {
            $prestamo = Prestamo::findOrFail($id);
            
            if ($prestamo->estado === 'devuelto') {
                return $this->sendError('El préstamo ya fue devuelto', [], 400);
            }
            
            $validated = $request->validated();
            $fechaDevolucionReal = Carbon::now();
            
            // Calcular días de retraso y multa si corresponde
            $diasRetraso = 0;
            $multa = 0;
            
            if ($fechaDevolucionReal->greaterThan($prestamo->fecha_devolucion_esperada)) {
                $diasRetraso = $fechaDevolucionReal->diffInDays($prestamo->fecha_devolucion_esperada);
                $multa = $diasRetraso * 5.00; // $5 por día de retraso
            }
            
            $prestamo->update([
                'fecha_devolucion_real' => $fechaDevolucionReal,
                'estado' => 'devuelto',
                'multa' => $multa,
                'observaciones' => $validated['observaciones'] ?? $prestamo->observaciones,
                'estado_libro' => $validated['estado_libro'] ?? null,
            ]);
            
            // Restaurar stock del libro
            $prestamo->libro->increment('stock_disponible');
            
            $prestamo->load(['usuario', 'libro']);
            
            $response = [
                'prestamo' => $prestamo,
                'dias_retraso' => $diasRetraso,
                'multa_aplicada' => $multa,
            ];
            
            return $this->sendResponse($response, 'Préstamo devuelto exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al devolver préstamo: ' . $e->getMessage(), [], 500);
        }
    }

    public function show($id)
    {
        try {
            $prestamo = Prestamo::with(['usuario', 'libro.autores'])->findOrFail($id);
            return $this->sendResponse($prestamo, 'Préstamo obtenido exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Préstamo no encontrado', [], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $prestamo = Prestamo::findOrFail($id);
            
            if ($prestamo->estado === 'activo') {
                return $this->sendError('No se puede eliminar un préstamo activo', [], 400);
            }
            
            // Si el préstamo no está devuelto, restaurar el stock
            if ($prestamo->estado !== 'devuelto') {
                $prestamo->libro->increment('stock_disponible');
            }
            
            $prestamo->delete();
            return $this->sendResponse(null, 'Préstamo eliminado exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al eliminar préstamo', [], 500);
        }
    }

    /**
     * Renovar un préstamo activo
     */
    public function renovar(Request $request, $id)
    {
        try {
            $prestamo = Prestamo::findOrFail($id);
            
            if ($prestamo->estado !== 'activo') {
                return $this->sendError('Solo se pueden renovar préstamos activos', [], 400);
            }
            
            // Verificar que no tenga multas pendientes
            if ($prestamo->multa > 0) {
                return $this->sendError('No se puede renovar un préstamo con multa pendiente', [], 400);
            }
            
            // Verificar que el libro no esté reservado por otro usuario
            $reservasPendientes = Prestamo::where('libro_id', $prestamo->libro_id)
                ->where('estado', 'reservado')
                ->exists();
                
            if ($reservasPendientes) {
                return $this->sendError('El libro tiene reservas pendientes, no se puede renovar', [], 400);
            }
            
            // Calcular nueva fecha de devolución (15 días más)
            $nuevaFechaDevolucion = Carbon::now()->addDays(15);
            
            $prestamo->update([
                'fecha_devolucion_esperada' => $nuevaFechaDevolucion,
                'renovaciones' => $prestamo->renovaciones + 1,
            ]);
            
            return $this->sendResponse($prestamo, 'Préstamo renovado exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al renovar préstamo', [], 500);
        }
    }

    /**
     * Obtener préstamos de un usuario específico
     */
    public function prestamosPorUsuario($usuarioId)
    {
        try {
            $prestamos = Prestamo::where('usuario_id', $usuarioId)
                ->with(['libro.autores'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return $this->sendResponse($prestamos, 'Préstamos del usuario obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener préstamos del usuario', [], 500);
        }
    }
}