<?php

namespace App\Http\Controllers\Api;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReporteController extends BaseController
{
    public function librosMasPrestados(Request $request)
    {
        try {
            $limite = $request->input('limite', 10);
            $fechaInicio = $request->input('fecha_inicio');
            $fechaFin = $request->input('fecha_fin');

            $query = Prestamo::selectRaw('libro_id, count(*) as total_prestamos')
                ->with('libro')
                ->groupBy('libro_id')
                ->orderBy('total_prestamos', 'desc');

            // Filtrar por rango de fechas si se proporciona
            if ($fechaInicio) {
                $query->whereDate('fecha_prestamo', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->whereDate('fecha_prestamo', '<=', $fechaFin);
            }

            $librosMasPrestados = $query->take($limite)->get();

            return $this->sendResponse($librosMasPrestados, 'Libros más prestados obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener el reporte de libros más prestados: ' . $e->getMessage(), [], 500);
        }
    }

    public function usuariosConPrestamosVencidos(Request $request)
    {
        try {
            $usuariosConPrestamosVencidos = User::whereHas('prestamos', function ($query) {
                $query->where('estado', 'vencido')
                      ->orWhere(function ($q) {
                          $q->where('estado', 'activo')
                            ->where('fecha_devolucion_estimada', '<', Carbon::now());
                      });
            })
            ->with(['prestamos' => function ($query) {
                $query->where('estado', 'vencido')
                      ->orWhere(function ($q) {
                          $q->where('estado', 'activo')
                            ->where('fecha_devolucion_estimada', '<', Carbon::now());
                      })
                      ->with('libro');
            }])
            ->get();

            // Transformar la respuesta para incluir información detallada
            $usuariosConPrestamosVencidos->transform(function ($usuario) {
                $usuario->prestamos_vencidos = $usuario->prestamos->map(function ($prestamo) {
                    return [
                        'id' => $prestamo->id,
                        'libro' => $prestamo->libro->titulo,
                        'fecha_prestamo' => $prestamo->fecha_prestamo,
                        'fecha_devolucion_estimada' => $prestamo->fecha_devolucion_estimada,
                        'dias_retraso' => Carbon::now()->diffInDays($prestamo->fecha_devolucion_estimada, false) * -1,
                    ];
                });
                unset($usuario->prestamos);
                return $usuario;
            });

            return $this->sendResponse($usuariosConPrestamosVencidos, 'Usuarios con préstamos vencidos obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener el reporte de usuarios con préstamos vencidos: ' . $e->getMessage(), [], 500);
        }
    }

    public function librosSinStock(Request $request)
    {
        try {
            $librosSinStock = Libro::where('stock_disponible', 0)
                ->with('autores')
                ->get();

            return $this->sendResponse($librosSinStock, 'Libros sin stock obtenidos exitosamente');
        } catch (\Exception $e) {
            return $this->sendError('Error al obtener el reporte de libros sin stock: ' . $e->getMessage(), [], 500);
        }
    }
}