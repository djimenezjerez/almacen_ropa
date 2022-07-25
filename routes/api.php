<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClientController;

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
    Route::get('user', [UserController::class, 'index']);
    Route::get('user/{user}', [UserController::class, 'show']);
    Route::patch('user/{user}', [UserController::class, 'update']);
    Route::group(['middleware' => ['can:USUARIOS']], function() {
        Route::post('user', [UserController::class, 'store']);
        Route::delete('user/{user}', [UserController::class, 'destroy']);
    });

    // Tiendas
    Route::group(['middleware' => ['can:TIENDAS']], function() {
        Route::post('store', [StoreController::class, 'store']);
        Route::get('store/{store}', [StoreController::class, 'show']);
        Route::patch('store/{store}', [StoreController::class, 'update']);
        Route::delete('store/{store}', [StoreController::class, 'destroy']);
        // Empleados
        Route::get('store/{store}/employee', [EmployeeController::class, 'index']);
        Route::post('store/{store}/employee', [EmployeeController::class, 'store']);
        Route::delete('store/{store_id}/employee/{user_id}', [EmployeeController::class, 'destroy']);
    });

    // Almacenes
    Route::group(['middleware' => ['can:ALMACENES']], function() {
        Route::get('warehouse', [WarehouseController::class, 'index']);
        Route::post('warehouse', [WarehouseController::class, 'store']);
        Route::patch('warehouse/{warehouse}', [WarehouseController::class, 'update']);
        Route::delete('warehouse/{warehouse}', [WarehouseController::class, 'destroy']);
    });

    // Proveedores
    Route::group(['middleware' => ['can:PROVEEDORES']], function() {
        Route::get('supplier', [SupplierController::class, 'index']);
        Route::post('supplier', [SupplierController::class, 'store']);
        Route::patch('supplier/{supplier}', [SupplierController::class, 'update']);
        Route::delete('supplier/{supplier}', [SupplierController::class, 'destroy']);
    });

    // Clientes
    Route::group(['middleware' => ['can:CLIENTES']], function() {
        Route::get('client', [ClientController::class, 'index']);
        Route::post('client', [ClientController::class, 'store']);
        Route::patch('client/{client}', [ClientController::class, 'update']);
        Route::delete('client/{client}', [ClientController::class, 'destroy']);
    });
});
