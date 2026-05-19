<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\PedidoController; // <--- Nuevo Controlador

// Catálogo Público Principal y Detalle de Producto
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/producto/{id}', [HomeController::class, 'show'])->name('producto.show');

// Configuración Inicial del Administrador
Route::get('/registrar-admin', [AdminController::class, 'create'])->name('admin.register.form');
Route::post('/registrar-admin', [AdminController::class, 'store'])->name('admin.register.store');

// Registro Público de Clientes
Route::get('/registrarse', [ClienteController::class, 'create'])->name('cliente.register.form');
Route::post('/registrarse', [ClienteController::class, 'store'])->name('cliente.register.store');

// Autenticación de Usuarios (Login / Logout)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');

// Rutas Protegidas (Requieren Autenticación)
Route::middleware(['auth'])->group(function () {
    // Cierre de Sesión Seguro
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Gestión de Proveedores
    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

    // Gestión de Productos e Inventario Base
    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::post('/categorias/rapida', [ProductoController::class, 'storeCategoria'])->name('categorias.store.rapida');

    // Operaciones del Carrito de Compras
    Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::post('/carrito/eliminar', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');

    // MÓDULO CLIENTE: Checkout, Pagos y Mis Pedidos
    Route::get('/checkout', [CompraController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout/procesar', [CompraController::class, 'procesarCompra'])->name('checkout.store');
    Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('cliente.pedidos'); // <--- Nueva Ruta Cliente
    
    // MÓDULO ADMIN/CAJERO: Corroboración de Comprobantes QR
    Route::get('/gestion/pagos', [CompraController::class, 'listaPagosPendientes'])->name('admin.pagos.index');
    Route::post('/gestion/pagos/{id}/verificar', [CompraController::class, 'verificarPago'])->name('admin.pagos.verificar');

    // MÓDULO LOGÍSTICA/PERSONAL: Control de Despachos
    Route::get('/gestion/envios', [PedidoController::class, 'controlEnvios'])->name('personal.envios.index'); // <--- Nueva Ruta Personal
    Route::put('/gestion/envios/{id}/estado', [PedidoController::class, 'actualizarEstadoEnvio'])->name('personal.envios.update'); // <--- Nueva Ruta Personal
});