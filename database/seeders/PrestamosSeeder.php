<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Models\Libro;
use Carbon\Carbon;

class PrestamosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = Usuario::all();
        $libros = Libro::all();

        if ($usuarios->isEmpty() || $libros->isEmpty()) {
            throw new \Exception('No hay usuarios o libros para crear préstamos');
        }

        // Crear 20 préstamos
        for ($i = 1; $i <= 20; $i++) {

            $usuario = $usuarios->random();
            $libro = $libros->random();

            $fechaPrestamo = Carbon::now()->subDays(rand(1, 30));
            $fechaEstimada = (clone $fechaPrestamo)->addDays(7);

            // Estados posibles
            $estado = collect(['activo', 'devuelto', 'vencido'])->random();

            Prestamo::create([
                'usuario_id' => $usuario->id,
                'libro_id' => $libro->id,
                'fecha_prestamo' => $fechaPrestamo,
                'fecha_devolucion_estimada' => $fechaEstimada,
                'fecha_devolucion_real' => $estado === 'devuelto'
                    ? (clone $fechaPrestamo)->addDays(rand(1, 7))
                    : null,
                'estado' => $estado,
            ]);
        }
    }
}
