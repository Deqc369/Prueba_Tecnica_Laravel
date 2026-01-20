<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsuariosSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'nombre' => 'Juan Pérez',
                'email' => 'juan.perez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-1234',
                'fecha_registro' => now(),
                'estado' => 'activo',
                
            ],
            [
                'nombre' => 'María García',
                'email' => 'maria.garcia@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-5678',
                'fecha_registro' => now(),
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Carlos López',
                'email' => 'carlos.lopez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-9012',
                'fecha_registro' => now(),
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Ana Rodríguez',
                'email' => 'ana.rodriguez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-3456',
                'fecha_registro' => now(),
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Pedro Sánchez',
                'email' => 'pedro.sanchez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-7890',
                'fecha_registro' => now(),
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Laura Martínez',
                'email' => 'laura.martinez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-2345',
                'fecha_registro' => now(),
                'estado' => 'inactivo',
            ],
            [
                'nombre' => 'Roberto Fernández',
                'email' => 'roberto.fernandez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-6789',
                'fecha_registro' => now(),
                'estado' => 'activo',
            ],
        ];

        foreach ($usuarios as $usuario) {
            Usuario::create($usuario);
        }

        // Crear 15 usuarios adicionales con datos aleatorios
        // Usuario::factory()->count(15)->create();
    }
}