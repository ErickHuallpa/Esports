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
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PosController; // <--- Nuevo Controlador

// Catálogo Público Principal y Detalle de Producto
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/producto/{id}', [HomeController::class, 'show'])->name('producto.show');

// Configuración Inicial del Administrador
Route::get('/registrar-admin', [AdminController::class, 'create'])->name('admin.register.form');
Route::post('/registrar-admin', [AdminController::class, 'store'])->name('admin.register.store');

// Registro Público de Clientes
Route::get('/registrarse', [ClienteController::class, 'create'])->name('cliente.register.form');
Route::post('/registrarse', [ClienteController::class, 'store'])->name('cliente.register.store');

// Autenticación de Usuarios
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');

// Rutas Protegidas
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
    Route::post('/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
    Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('admin.usuarios.update');
    Route::patch('/usuarios/{id}/estado', [UserController::class, 'toggleStatus'])->name('admin.usuarios.estado');

    Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
    Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
    Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
    Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

    Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    Route::post('/categorias/rapida', [ProductoController::class, 'storeCategoria'])->name('categorias.store.rapida');

    Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::post('/carrito/eliminar', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');

    Route::get('/checkout', [CompraController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout/procesar', [CompraController::class, 'procesarCompra'])->name('checkout.store');
    Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('cliente.pedidos');
    
    Route::get('/gestion/pagos', [CompraController::class, 'listaPagosPendientes'])->name('admin.pagos.index');
    Route::post('/gestion/pagos/{id}/verificar', [CompraController::class, 'verificarPago'])->name('admin.pagos.verificar');

    Route::get('/gestion/envios', [PedidoController::class, 'controlEnvios'])->name('personal.envios.index');
    Route::put('/gestion/envios/{id}/estado', [PedidoController::class, 'actualizarEstadoEnvio'])->name('personal.envios.update');

    Route::post('/producto/resena', [ResenaController::class, 'store'])->name('resenas.store');
    Route::put('/producto/resena/{id}', [ResenaController::class, 'update'])->name('resenas.update');
    Route::delete('/producto/resena/{id}', [ResenaController::class, 'destroy'])->name('resenas.destroy');
    Route::get('/mis-resenas', [ResenaController::class, 'misResenas'])->name('cliente.resenas');

    // MÓDULO CAJERO: Punto de Venta (POS)
    Route::get('/gestion/ventas', [VentaController::class, 'index'])->name('cajero.ventas.index');
    Route::get('/pos', [PosController::class, 'index'])->name('cajero.pos.index');
    Route::post('/pos', [PosController::class, 'store'])->name('cajero.pos.store');
    Route::get('/pos/buscar-cliente', [PosController::class, 'buscarCliente'])->name('cajero.pos.buscarCliente'); // <--- Ruta AJAX
});