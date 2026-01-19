<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Autor;
use App\Models\Libro;
use App\Models\Usuario;
use App\Models\Prestamo;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AutoresSeeder::class,
            LibrosSeeder::class,
            UsuariosSeeder::class,
            PrestamosSeeder::class,
        ]);
    }
}
