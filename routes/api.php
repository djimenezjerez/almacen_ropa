<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DocumentTypeController;

// Autenticación
Route::post('login', [AuthController::class, 'store']);

// Tiendas
Route::get('store', [StoreController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Cerrar sesión
    Route::post('logout', [AuthController::class, 'destroy']);

    // Ciudades
    Route::get('city', [CityController::class, 'index']);

    // Documentos de identidad
    Route::get('document_type', [DocumentTypeController::class, 'index']);

    // Roles
    Route::get('role', [RoleController::class, 'index'])->middleware('can:USUARIOS');

    // Usuarios
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::patch('user/{user}', [UserController::class, 'update']);
    Route::group(['middleware' => ['can:USUARIOS']], function() {
        // Usuarios
        Route::get('user', [UserController::class, 'index']);
        Route::post('user', [UserController::class, 'store']);
        Route::delete('user/{user}', [UserController::class, 'destroy']);
    });
});
