<?php

namespace Database\Seeders;

use App\Models\User;
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
                'direccion' => 'Calle Principal 123',
                'tipo_usuario' => 'admin',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'María García',
                'email' => 'maria.garcia@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-5678',
                'direccion' => 'Avenida Central 456',
                'tipo_usuario' => 'usuario',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Carlos López',
                'email' => 'carlos.lopez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-9012',
                'direccion' => 'Plaza Mayor 789',
                'tipo_usuario' => 'usuario',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Ana Rodríguez',
                'email' => 'ana.rodriguez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-3456',
                'direccion' => 'Boulevard Norte 101',
                'tipo_usuario' => 'usuario',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Pedro Sánchez',
                'email' => 'pedro.sanchez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-7890',
                'direccion' => 'Calle Sur 202',
                'tipo_usuario' => 'usuario',
                'estado' => 'activo',
            ],
            [
                'nombre' => 'Laura Martínez',
                'email' => 'laura.martinez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-2345',
                'direccion' => 'Avenida Este 303',
                'tipo_usuario' => 'usuario',
                'estado' => 'inactivo',
            ],
            [
                'nombre' => 'Roberto Fernández',
                'email' => 'roberto.fernandez@example.com',
                'password' => Hash::make('password123'),
                'telefono' => '555-6789',
                'direccion' => 'Calle Oeste 404',
                'tipo_usuario' => 'usuario',
                'estado' => 'activo',
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::create($usuario);
        }

        // Crear 15 usuarios adicionales con datos aleatorios
        User::factory()->count(15)->create();
    }
}