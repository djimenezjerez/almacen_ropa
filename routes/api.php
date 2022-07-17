<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DocumentTypeController;

// Autenticación
Route::post('login', [AuthController::class, 'store']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Cerrar sesión
    Route::post('logout', [AuthController::class, 'destroy']);

    // Ciudades
    Route::get('city', [CityController::class, 'index']);

    // Documentos de identidad
    Route::get('document_type', [DocumentTypeController::class, 'index']);

    // Usuarios
    Route::get('role/{role}', [RoleController::class, 'show'])->middleware('can:USUARIOS');
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::patch('user/{user}', [UserController::class, 'update']);
    Route::group(['middleware' => ['can:USUARIOS']], function() {
        // Roles
        Route::get('role', [RoleController::class, 'index'])->middleware('can:USUARIOS');
        // Usuarios
        Route::post('user', [UserController::class, 'store']);
        Route::delete('user/{user}', [UserController::class, 'destroy']);
        Route::patch('deleted/user/{user}', [UserController::class, 'restore']);
    });
});
