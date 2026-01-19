<?php

namespace App\Services;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrestamoService
{
    public function puedePrestar(Usuario $usuario, Libro $libro): array
    {
        $errores = [];
        
        // Verificar stock
        if ($libro->stock_disponible <= 0) {
            $errores[] = 'No hay stock disponible';
        }
        
        // Verificar préstamos activos
        $prestamosActivos = $usuario->prestamosActivos()->count();
        if ($prestamosActivos >= 3) {
            $errores[] = 'Máximo de préstamos alcanzado';
        }
        
        // Verificar usuario activo
        if (!$usuario->esta_activo) {
            $errores[] = 'Usuario inactivo';
        }
        
        return [
            'puede' => empty($errores),
            'errores' => $errores,
        ];
    }
    
    public function marcarPrestamosVencidos(): int
    {
        $vencidos = Prestamo::where('estado', 'activo')
            ->whereDate('fecha_devolucion_estimada', '<', Carbon::now())
            ->update(['estado' => 'vencido']);
            
        return $vencidos;
    }
}