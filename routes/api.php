<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LibroController;
use App\Http\Controllers\Api\PrestamoController;
use App\Http\Controllers\Api\ReporteController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Libros
    Route::apiResource('libros', LibroController::class);
    
    // Préstamos
    Route::apiResource('prestamos', PrestamoController::class)->except(['update']);
    Route::put('/prestamos/{prestamo}/devolver', [PrestamoController::class, 'devolver']);
    
    // Reportes
    Route::get('/reportes/libros-mas-prestados', [ReporteController::class, 'librosMasPrestados']);
    Route::get('/reportes/usuarios-prestamos-vencidos', [ReporteController::class, 'usuariosConPrestamosVencidos']);
    Route::get('/reportes/libros-sin-stock', [ReporteController::class, 'librosSinStock']);
});