<?php

namespace Database\Seeders;

use App\Models\Libro;
use App\Models\Autor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LibrosSeeder extends Seeder
{
    public function run(): void
    {
        $libros = [
            ['Cien años de soledad', '978-0307474728', 1967, 417, 'Novela del realismo mágico', 5],
            ['La ciudad y los perros', '978-8420471836', 1963, 408, 'Primera novela de Vargas Llosa', 3],
            ['La casa de los espíritus', '978-9500714957', 1982, 499, 'Novela familiar épica', 4],
            ['Ficciones', '978-9500755578', 1944, 155, 'Colección de cuentos', 2],
            ['Rayuela', '978-8432213780', 1963, 736, 'Novela experimental', 3],
            ['Veinte poemas de amor', '978-9500705848', 1924, 86, 'Poesía romántica', 6],
            ['La región más transparente', '978-6074530326', 1958, 425, 'Novela sobre Ciudad de México', 2],
            ['El laberinto de la soledad', '978-6074530739', 1950, 235, 'Ensayo sobre identidad mexicana', 4],
            ['La noche de Tlatelolco', '978-6074530821', 1971, 336, 'Testimonio histórico', 3],
            ['Pedro Páramo', '978-6074530500', 1955, 124, 'Novela rural mexicana', 5],
        ];

        $autores = Autor::all();

        foreach ($libros as $index => $libro) {
            $nuevoLibro = Libro::create([
                'titulo' => $libro[0],
                'isbn' => $libro[1],
                'anio_publicacion' => $libro[2],
                'numero_paginas' => $libro[3],
                'descripcion' => $libro[4],
                'stock_disponible' => $libro[5],
            ]);

            // Asignar 1-3 autores aleatorios a cada libro
            $autoresRandom = $autores->random(rand(1, 3));
            $orden = 1;
            foreach ($autoresRandom as $autor) {
                $nuevoLibro->autores()->attach($autor->id, ['orden_autor' => $orden++]);
            }
        }

        // Crear 10 libros adicionales
        for ($i = 11; $i <= 20; $i++) {
            $libro = Libro::create([
                'titulo' => 'Libro ' . Str::random(10),
                'isbn' => '978-' . rand(1000000000, 9999999999),
                'anio_publicacion' => rand(1900, 2023),
                'numero_paginas' => rand(100, 800),
                'descripcion' => 'Descripción del libro ' . $i,
                'stock_disponible' => rand(0, 10),
            ]);

            $autoresRandom = $autores->random(rand(1, 2));
            $orden = 1;
            foreach ($autoresRandom as $autor) {
                $libro->autores()->attach($autor->id, ['orden_autor' => $orden++]);
            }
        }
    }
}