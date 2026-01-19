<?php

namespace App\Console\Commands;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerarReportes extends Command
{
    protected $signature = 'reportes:generar';
    protected $description = 'Genera reportes de la biblioteca';

    public function handle()
    {
        $this->info('Generando reportes...');
        
        $this->reporteLibrosMasPrestados();
        $this->reporteUsuariosPrestamosVencidos();
        $this->reporteLibrosSinStock();
        
        $this->info('Reportes generados exitosamente.');
    }
    
    private function reporteLibrosMasPrestados()
    {
        $this->info('=== Libros más prestados ===');
        
        $libros = Libro::withCount(['prestamos' => function ($query) {
            $query->where('estado', 'devuelto');
        }])
        ->orderBy('prestamos_count', 'desc')
        ->limit(10)
        ->get();
        
        $headers = ['ID', 'Título', 'Préstamos', 'Stock'];
        $rows = [];
        
        foreach ($libros as $libro) {
            $rows[] = [
                $libro->id,
                $libro->titulo,
                $libro->prestamos_count,
                $libro->stock_disponible,
            ];
        }
        
        $this->table($headers, $rows);
    }
    
    private function reporteUsuariosPrestamosVencidos()
    {
        $this->info('=== Usuarios con préstamos vencidos ===');
        
        $usuarios = Usuario::whereHas('prestamos', function ($query) {
            $query->where('estado', 'vencido')
                  ->orWhere(function ($q) {
                      $q->where('estado', 'activo')
                        ->whereDate('fecha_devolucion_estimada', '<', Carbon::now());
                  });
        })
        ->withCount(['prestamos' => function ($query) {
            $query->where('estado', 'vencido')
                  ->orWhere(function ($q) {
                      $q->where('estado', 'activo')
                        ->whereDate('fecha_devolucion_estimada', '<', Carbon::now());
                  });
        }])
        ->get();
        
        $headers = ['ID', 'Nombre', 'Email', 'Préstamos Vencidos'];
        $rows = [];
        
        foreach ($usuarios as $usuario) {
            $rows[] = [
                $usuario->id,
                $usuario->nombre,
                $usuario->email,
                $usuario->prestamos_count,
            ];
        }
        
        $this->table($headers, $rows);
    }
    
    private function reporteLibrosSinStock()
    {
        $this->info('=== Libros sin stock ===');
        
        $libros = Libro::where('stock_disponible', 0)->get();
        
        $headers = ['ID', 'Título', 'ISBN', 'Stock'];
        $rows = [];
        
        foreach ($libros as $libro) {
            $rows[] = [
                $libro->id,
                $libro->titulo,
                $libro->isbn,
                $libro->stock_disponible,
            ];
        }
        
        $this->table($headers, $rows);
    }
}