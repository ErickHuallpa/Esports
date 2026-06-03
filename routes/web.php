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
use App\Http\Controllers\PosController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OfertaController;
use App\Http\Controllers\CuponController;
use App\Http\Controllers\ReporteController;

// Catálogo Público Principal y Detalle de Producto
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/producto/{id}', [HomeController::class, 'show'])->name('producto.show');

// Páginas estáticas (Footer)
Route::view('/terminos-y-condiciones', 'pages.terminos')->name('pages.terminos');
Route::view('/politica-de-privacidad', 'pages.privacidad')->name('pages.privacidad');

// Configuración Inicial del Administrador
Route::get('/registrar-admin', [AdminController::class, 'create'])->name('admin.register.form');
Route::post('/registrar-admin', [AdminController::class, 'store'])->name('admin.register.store');

// Registro Público de Clientes
Route::get('/registrarse', [ClienteController::class, 'create'])->name('cliente.register.form');
Route::post('/registrarse', [ClienteController::class, 'store'])->name('cliente.register.store');
Route::post('/validar-registro-unico', [ClienteController::class, 'validarUnico'])->name('cliente.register.validar');

// Autenticación de Usuarios
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');

// Rutas Protegidas
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // MÓDULO MI CUENTA (Perfil) - ACCESIBLE POR TODOS LOS AUTENTICADOS
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('perfil.update');
    Route::put('/perfil/password', [ProfileController::class, 'updatePassword'])->name('perfil.password');

    Route::post('/carrito/agregar', [CarritoController::class, 'agregar'])->name('carrito.agregar');
    Route::post('/carrito/eliminar', [CarritoController::class, 'eliminar'])->name('carrito.eliminar');

    Route::get('/checkout', [CompraController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout/procesar', [CompraController::class, 'procesarCompra'])->name('checkout.store');

    Route::get('/mis-pedidos', [PedidoController::class, 'misPedidos'])->name('cliente.pedidos');
    Route::put('/mis-pedidos/{id}/recibir', [PedidoController::class, 'marcarComoRecibido'])->name('cliente.pedidos.recibir');
    Route::get('/mis-resenas', [ResenaController::class, 'misResenas'])->name('cliente.resenas');
    Route::get('/mis-pedidos/{id}/comprobante', [PedidoController::class, 'descargarComprobante'])->name('cliente.comprobante');

    Route::post('/producto/resena', [ResenaController::class, 'store'])->name('resenas.store');
    Route::put('/producto/resena/{id}', [ResenaController::class, 'update'])->name('resenas.update');
    Route::delete('/producto/resena/{id}', [ResenaController::class, 'destroy'])->name('resenas.destroy');
    Route::post('/validar-cupon', [CuponController::class, 'validarCupon'])->name('cliente.validarCupon');

    // =======================================================
    // RUTAS EXCLUSIVAS DE ADMINISTRADOR
    // =======================================================
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/usuarios', [UserController::class, 'index'])->name('admin.usuarios.index');
        Route::post('/usuarios', [UserController::class, 'store'])->name('admin.usuarios.store');
        Route::put('/usuarios/{id}', [UserController::class, 'update'])->name('admin.usuarios.update');
        Route::patch('/usuarios/{id}/estado', [UserController::class, 'toggleStatus'])->name('admin.usuarios.estado');
        Route::post('/usuarios/validar', [UserController::class, 'validarUnico'])->name('admin.usuarios.validar');

        Route::get('/proveedores', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::post('/proveedores', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::put('/proveedores/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('/proveedores/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');

        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->name('productos.destroy');
        Route::post('/categorias/rapida', [ProductoController::class, 'storeCategoria'])->name('categorias.store.rapida');

        Route::get('/categorias', [CategoriaController::class, 'index'])->name('admin.categorias.index');
        Route::post('/categorias', [CategoriaController::class, 'store'])->name('admin.categorias.store');
        Route::put('/categorias/{id}', [CategoriaController::class, 'update'])->name('admin.categorias.update');
        Route::patch('/categorias/{id}/estado', [CategoriaController::class, 'toggleStatus'])->name('admin.categorias.estado');

        Route::get('/ofertas', [OfertaController::class, 'index'])->name('admin.ofertas.index');
        Route::post('/ofertas', [OfertaController::class, 'store'])->name('admin.ofertas.store');
        Route::put('/ofertas/{id}', [OfertaController::class, 'update'])->name('admin.ofertas.update');
        Route::delete('/ofertas/{id}', [OfertaController::class, 'destroy'])->name('admin.ofertas.destroy');

        Route::get('/cupones', [CuponController::class, 'index'])->name('admin.cupones.index');
        Route::post('/cupones', [CuponController::class, 'store'])->name('admin.cupones.store');
        Route::put('/cupones/{id}', [CuponController::class, 'update'])->name('admin.cupones.update');
        Route::patch('/cupones/{id}/reactivar', [CuponController::class, 'reactivar'])->name('admin.cupones.reactivar');
        Route::delete('/cupones/{id}', [CuponController::class, 'destroy'])->name('admin.cupones.destroy');

        // Reportes
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/ventas', [ReporteController::class, 'ventas'])->name('reportes.ventas');
        Route::get('/reportes/productos-mas-vendidos', [ReporteController::class, 'productosMasVendidos'])->name('reportes.productos-mas-vendidos');
        Route::get('/reportes/clientes-frecuentes', [ReporteController::class, 'clientesFrecuentes'])->name('reportes.clientes-frecuentes');
        Route::get('/reportes/ventas-por-categoria', [ReporteController::class, 'ventasPorCategoria'])->name('reportes.ventas-por-categoria');
        Route::get('/reportes/inventario-bajo-stock', [ReporteController::class, 'inventarioBajoStock'])->name('reportes.inventario-bajo-stock');
        Route::get('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
        Route::get('/reportes/exportar-pdf', [ReporteController::class, 'exportarPDF'])->name('reportes.exportar-pdf');

        Route::get('/gestion/manual', [AdminController::class, 'manual'])->name('admin.manual');
        Route::get('/gestion/manual/descargar', [AdminController::class, 'descargarManual'])->name('admin.manual.descargar');
    });

    // =======================================================
    // RUTAS DE CAJA (Admin y Cajero)
    // =======================================================
    Route::middleware(['role:admin,cajero'])->group(function () {
        Route::get('/gestion/pagos', [CompraController::class, 'listaPagosPendientes'])->name('admin.pagos.index');
        Route::get('/api/pagos/pendientes/count', [CompraController::class, 'countPagosPendientes'])->name('api.pagos.pendientes.count');
        Route::post('/gestion/pagos/{id}/verificar', [CompraController::class, 'verificarPago'])->name('admin.pagos.verificar');

        Route::get('/gestion/ventas', [VentaController::class, 'index'])->name('cajero.ventas.index');
        Route::get('/pos', [PosController::class, 'index'])->name('cajero.pos.index');
        Route::post('/pos', [PosController::class, 'store'])->name('cajero.pos.store');
        Route::get('/pos/buscar-cliente', [PosController::class, 'buscarCliente'])->name('cajero.pos.buscarCliente');
    });

    // =======================================================
    // RUTAS DE LOGÍSTICA (Admin y Personal)
    // =======================================================
    Route::middleware(['role:admin,personal'])->group(function () {
        Route::get('/gestion/envios', [PedidoController::class, 'controlEnvios'])->name('personal.envios.index');
        Route::put('/gestion/envios/{id}/estado', [PedidoController::class, 'actualizarEstadoEnvio'])->name('personal.envios.update');

        Route::get('/inventario', [InventarioController::class, 'index'])->name('personal.inventario.index');
        Route::get('/inventario/kardex', [InventarioController::class, 'kardexApi'])->name('personal.inventario.kardex');
        Route::post('/inventario', [InventarioController::class, 'store'])->name('personal.inventario.store');
    });
});