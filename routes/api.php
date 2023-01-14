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
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GenderController;
use App\Http\Controllers\ProductNameController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SizeTypeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MovementTypeController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductSelectionController;
use App\Http\Controllers\ReportController;

// Autenticación
Route::post('auth', [AuthController::class, 'store']);

// Tiendas
Route::get('store', [StoreController::class, 'index']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    // Autenticación
    Route::get('auth', [AuthController::class, 'index']);
    Route::patch('auth', [AuthController::class, 'update']);

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

    // Configuración
    Route::group(['middleware' => ['can:CONFIGURACION']], function() {
        Route::get('category', [CategoryController::class, 'index']);
        Route::post('category', [CategoryController::class, 'store']);
        Route::patch('category/{category}', [CategoryController::class, 'update']);
        Route::delete('category/{category}', [CategoryController::class, 'destroy']);
    });

    // Reportes
    Route::get('size_type', [SizeTypeController::class, 'index']);
    Route::get('product/{product_name}/stock', [ProductController::class, 'stock']);
    Route::get('product/{product_name}/sells', [ProductController::class, 'sells']);

    // Productos
    Route::group(['middleware' => ['can:PRODUCTOS']], function() {
        Route::get('product', [ProductController::class, 'index']);
        Route::get('product/{product_name}', [ProductController::class, 'show']);
        Route::post('product', [ProductController::class, 'store']);
        Route::patch('product/{product}', [ProductController::class, 'update']);
        Route::delete('product/{product}', [ProductController::class, 'destroy']);
        Route::get('product/{product}/sizes', [ProductController::class, 'sizes']);
        Route::delete('product/{product}/sizes', [ProductController::class, 'destroy_size']);
        Route::get('product/{product}/details', [ProductController::class, 'details']);
        // Nombres de productos
        Route::get('product_name', [ProductNameController::class, 'index']);
        Route::get('product_name/{product_name}', [ProductNameController::class, 'show']);
        // Géneros
        Route::get('gender', [GenderController::class, 'index']);
        Route::get('gender/{gender}', [GenderController::class, 'show']);
        // Marcas
        Route::get('brand', [BrandController::class, 'index']);
        Route::get('brand/{brand}', [BrandController::class, 'show']);
        Route::post('brand', [BrandController::class, 'store']);
        Route::delete('brand/{brand}', [BrandController::class, 'destroy']);
        // Tallas
        Route::get('size', [SizeController::class, 'index']);
        Route::post('size', [SizeController::class, 'store']);
        Route::patch('size', [SizeController::class, 'update']);
        Route::delete('size/{size}', [SizeController::class, 'destroy']);
        // Tipos de tallas
        Route::get('size_type/{size_type}', [SizeTypeController::class, 'show']);
        // Colores
        Route::get('color', [ColorController::class, 'index']);
        Route::get('color/{color}', [ColorController::class, 'show']);
        Route::post('color', [ColorController::class, 'store']);
        Route::delete('color/{color}', [ColorController::class, 'destroy']);
    });

    // Tipos de movimiento de stock
    Route::get('movement_type', [MovementTypeController::class, 'index']);
    Route::get('movement_type/{movement_type}', [MovementTypeController::class, 'show']);

    // Movimientos de stock
    Route::get('movement', [MovementController::class, 'index']);
    Route::get('movement/{movement}', [MovementController::class, 'show']);
    Route::post('movement', [MovementController::class, 'store']);

    // Selección de productos
    Route::get('product_selection/size_types', [ProductSelectionController::class, 'size_types']);
    Route::get('product_selection/product_names', [ProductSelectionController::class, 'product_names']);
    Route::get('product_selection/genders', [ProductSelectionController::class, 'genders']);
    Route::get('product_selection/brands', [ProductSelectionController::class, 'brands']);
    Route::get('product_selection/colors', [ProductSelectionController::class, 'colors']);
    Route::get('product_selection/sizes', [ProductSelectionController::class, 'sizes']);

    // Reportes
    Route::get('report/products', [ReportController::class, 'products']);
    Route::get('report/sells', [ReportController::class, 'sells']);
    Route::get('report/sellsUnitary', [ReportController::class, 'sellsUnitary']);
});
