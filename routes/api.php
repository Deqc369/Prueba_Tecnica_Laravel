<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LibroController;
use App\Http\Controllers\Api\PrestamoController;
use App\Http\Controllers\Api\ReporteController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Listar libros con filtro
    Route::get('/libros ', [LibroController::class, 'index']);
    
    // Obtener libro especifico con autor
    Route::get('/libros/{id}', [LibroController::class, 'show']);
    
    // Crear nuevo libro
    Route::post('/libros ', [LibroController::class, 'store']);

    // Actualizar libro
    Route::put('/libros/{id}', [LibroController::class, 'update']);

    // Eliminar libro
    Route::delete('/libros/{id}', [LibroController::class, 'destroy']);
 
    // Listar Préstamos
    Route::get('/prestamos', [PrestamoController::class, 'index']);

    // Crear nuevo préstamo
    Route::post('/prestamos', [PrestamoController::class, 'store']);

    // Marcar préstamo como devuelto
    Route::put('/prestamos/{id}/devolver ', [PrestamoController::class, 'devolver']);
    
    // Reportes
    Route::get('/reportes/libros-mas-prestados', [ReporteController::class, 'librosMasPrestados']);
    Route::get('/reportes/usuarios-prestamos-vencidos', [ReporteController::class, 'usuariosConPrestamosVencidos']);
    Route::get('/reportes/libros-sin-stock', [ReporteController::class, 'librosSinStock']);
//});