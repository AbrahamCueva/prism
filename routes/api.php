<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\SubcategoriaController;
use App\Http\Controllers\TransaccionController;
use App\Http\Controllers\AhorroController;
use App\Http\Controllers\UserController;

// Rutas públicas (sin autenticación)
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas protegidas (requieren token)
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

    // User
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::get('/user/configuracion', [UserController::class, 'getConfiguracion']);
    Route::put('/user/configuracion', [UserController::class, 'updateConfiguracion']);

    // Cuentas
    Route::apiResource('cuentas', CuentaController::class);
    Route::get('/cuentas/{id}/saldo', [CuentaController::class, 'getSaldo']);

    // Categorías
    Route::apiResource('categorias', CategoriaController::class);

    // Subcategorías
    Route::apiResource('subcategorias', SubcategoriaController::class);
    Route::get('/categorias/{id}/subcategorias', [SubcategoriaController::class, 'porCategoria']);

    // Transacciones
    Route::apiResource('transacciones', TransaccionController::class);
    Route::get('/transacciones/filtrar', [TransaccionController::class, 'filtrar']);
    Route::get('/transacciones/resumen/mensual', [TransaccionController::class, 'resumenMensual']);

    // Ahorros
    Route::apiResource('ahorros', AhorroController::class);
    Route::get('/ahorros/{id}/progreso', [AhorroController::class, 'getProgreso']);
    Route::post('/ahorros/{id}/depositar', [AhorroController::class, 'depositar']);

});