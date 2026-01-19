<?php

namespace Database\Seeders;

use App\Models\Autor;
use Illuminate\Database\Seeder;

class AutoresSeeder extends Seeder
{
    public function run(): void
    {
        $autores = [
            ['Gabriel', 'García Márquez', '1927-03-06', 'Colombiana', 'Premio Nobel de Literatura 1982'],
            ['Mario', 'Vargas Llosa', '1936-03-28', 'Peruana-Española', 'Premio Nobel de Literatura 2010'],
            ['Isabel', 'Allende', '1942-08-02', 'Chilena', 'Escritora del realismo mágico'],
            ['Jorge Luis', 'Borges', '1899-08-24', 'Argentina', 'Escritor de cuentos y ensayos'],
            ['Julio', 'Cortázar', '1914-08-26', 'Argentina', 'Autor de Rayuela'],
            ['Pablo', 'Neruda', '1904-07-12', 'Chilena', 'Poeta y político'],
            ['Carlos', 'Fuentes', '1928-11-11', 'Mexicana', 'Novelista y ensayista'],
            ['Octavio', 'Paz', '1914-03-31', 'Mexicana', 'Premio Nobel de Literatura 1990'],
            ['Elena', 'Poniatowska', '1932-05-19', 'Mexicana', 'Periodista y escritora'],
            ['Juan', 'Rulfo', '1917-05-16', 'Mexicana', 'Autor de Pedro Páramo'],
        ];

        foreach ($autores as $autor) {
            Autor::create([
                'nombre' => $autor[0],
                'apellido' => $autor[1],
                'fecha_nacimiento' => $autor[2],
                'nacionalidad' => $autor[3],
                'biografia' => $autor[4],
            ]);
        }
    }
}