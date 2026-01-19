<?php

namespace Database\Seeders;

use App\Models\Prestamo;
use App\Models\User;
use App\Models\Libro;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PrestamosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = User::where('tipo_usuario', 'usuario')
                        ->where('estado', 'activo')
                        ->get();
        
        $libros = Libro::all();

        // Crear préstamos activos
        for ($i = 0; $i < 8; $i++) {
            $usuario = $usuarios->random();
            $libro = $libros->where('stock_disponible', '>', 0)->random();
            
            if ($libro) {
                $fechaPrestamo = Carbon::now()->subDays(rand(1, 14));
                $fechaDevolucionEsperada = $fechaPrestamo->copy()->addDays(15);
                
                Prestamo::create([
                    'usuario_id' => $usuario->id,
                    'libro_id' => $libro->id,
                    'fecha_prestamo' => $fechaPrestamo,
                    'fecha_devolucion_esperada' => $fechaDevolucionEsperada,
                    'estado' => 'activo',
                    'observaciones' => null,
                ]);

                // Actualizar stock del libro
                $libro->decrement('stock_disponible');
            }
        }

        // Crear préstamos devueltos
        for ($i = 0; $i < 25; $i++) {
            $usuario = $usuarios->random();
            $libro = $libros->random();
            
            $fechaPrestamo = Carbon::now()->subDays(rand(30, 90));
            $fechaDevolucionEsperada = $fechaPrestamo->copy()->addDays(15);
            $fechaDevolucionReal = $fechaPrestamo->copy()->addDays(rand(7, 21));
            
            // Determinar si tuvo multa
            $diasRetraso = max(0, $fechaDevolucionReal->diffInDays($fechaDevolucionEsperada, false));
            $multa = $diasRetraso > 0 ? $diasRetraso * 5.00 : 0.00;
            $observaciones = $diasRetraso > 0 ? 
                "Devolución con retraso de {$diasRetraso} días. Multa aplicada: $" . number_format($multa, 2) : 
                "Devolución a tiempo";

            Prestamo::create([
                'usuario_id' => $usuario->id,
                'libro_id' => $libro->id,
                'fecha_prestamo' => $fechaPrestamo,
                'fecha_devolucion_esperada' => $fechaDevolucionEsperada,
                'fecha_devolucion_real' => $fechaDevolucionReal,
                'estado' => 'devuelto',
                'multa' => $multa,
                'observaciones' => $observaciones,
            ]);
        }

        // Crear préstamos vencidos (sin devolver)
        for ($i = 0; $i < 5; $i++) {
            $usuario = $usuarios->random();
            $libro = $libros->where('stock_disponible', '>', 0)->random();
            
            if ($libro) {
                $fechaPrestamo = Carbon::now()->subDays(rand(16, 30));
                $fechaDevolucionEsperada = $fechaPrestamo->copy()->addDays(15);
                
                // Calcular días de retraso y multa
                $diasRetraso = Carbon::now()->diffInDays($fechaDevolucionEsperada, false);
                $multa = max(0, $diasRetraso) * 5.00;
                
                Prestamo::create([
                    'usuario_id' => $usuario->id,
                    'libro_id' => $libro->id,
                    'fecha_prestamo' => $fechaPrestamo,
                    'fecha_devolucion_esperada' => $fechaDevolucionEsperada,
                    'estado' => 'vencido',
                    'multa' => $multa,
                    'observaciones' => "Préstamo vencido. Retraso de {$diasRetraso} días. Multa pendiente: $" . number_format($multa, 2),
                ]);

                // Actualizar stock del libro
                $libro->decrement('stock_disponible');
            }
        }

        // Crear préstamos reservados
        for ($i = 0; $i < 4; $i++) {
            $usuario = $usuarios->random();
            $libro = $libros->where('stock_disponible', '>', 0)->random();
            
            if ($libro) {
                $fechaReserva = Carbon::now()->addDays(rand(1, 7));
                
                Prestamo::create([
                    'usuario_id' => $usuario->id,
                    'libro_id' => $libro->id,
                    'fecha_prestamo' => $fechaReserva,
                    'fecha_devolucion_esperada' => $fechaReserva->copy()->addDays(15),
                    'estado' => 'reservado',
                    'observaciones' => 'Libro reservado para préstamo futuro',
                ]);
            }
        }
    }
}