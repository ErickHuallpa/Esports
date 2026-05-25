<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\User;
use App\Models\Categoria;
use App\Models\ProductoVariante;
use App\Models\Proveedor;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    private function checkAdmin()
    {
        if (!auth()->check() || auth()->user()->rol->nombre !== 'admin') {
            abort(403, 'No autorizado. Solo los administradores pueden acceder a este módulo.');
        }
    }

    /**
     * Dashboard principal de reportes
     */
    public function index(Request $request)
    {
        $this->checkAdmin();
        // Obtener fechas del request o usar valores por defecto (últimos 30 días)
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Validar fechas
        $request->validate([
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
        ]);

        // Query base para ventas (para las tarjetas de resumen)
        $query = Venta::with(['user.persona', 'pago.tipoPago'])
                      ->where('estado_venta', 'confirmada')
                      ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        // Filtrar por vendedor si se selecciona
        if ($request->filled('vendedor_id')) {
            $query->where('user_id', $request->vendedor_id);
        }

        // Datos para Ventas (paginados)
        $ventas = $query->orderBy('created_at', 'desc')->paginate(20);

        // Totales para las tarjetas
        $totalVentas = $ventas->total();
        $totalIngresos = $ventas->sum('precio_total');
        $ticketPromedio = $totalVentas > 0 ? $totalIngresos / $totalVentas : 0;

        // Productos más vendidos
        $productosMasVendidos = DB::table('detalle_ventas')
            ->join('producto_variantes', 'detalle_ventas.producto_variante_id', '=', 'producto_variantes.id')
            ->join('productos', 'producto_variantes.producto_id', '=', 'productos.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.estado_venta', 'confirmada')
            ->whereBetween('ventas.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                'productos.id',
                'productos.nombre',
                'productos.imagen_url',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendidos'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.imagen_url')
            ->orderBy('total_vendidos', 'desc')
            ->limit(10)
            ->get();

        $totalProductosVendidos = $productosMasVendidos->sum('total_vendidos');

        // Clientes frecuentes
        $clientesFrecuentes = Venta::where('estado_venta', 'confirmada')
            ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->with('user.persona')
            ->get()
            ->groupBy('user_id')
            ->map(function($ventasGrupo) {
                $user = $ventasGrupo->first()->user;
                $totalCompras = $ventasGrupo->count();
                $totalGastado = $ventasGrupo->sum('precio_total');
                return (object) [
                    'nombre' => $user->persona->nombre . ' ' . $user->persona->apellidos,
                    'email' => $user->email,
                    'total_compras' => $totalCompras,
                    'total_gastado' => $totalGastado,
                    'promedio_compra' => $totalCompras > 0 ? $totalGastado / $totalCompras : 0
                ];
            })
            ->sortByDesc('total_gastado')
            ->take(10)
            ->values();

        // Ventas por categoría
        $ventasPorCategoria = DB::table('detalle_ventas')
            ->join('producto_variantes', 'detalle_ventas.producto_variante_id', '=', 'producto_variantes.id')
            ->join('productos', 'producto_variantes.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.estado_venta', 'confirmada')
            ->whereBetween('ventas.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->select(
                'categorias.nombre as categoria',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendidos'),
                DB::raw('SUM(detalle_ventas.subtotal) as total_ingresos')
            )
            ->groupBy('categorias.nombre')
            ->orderBy('total_ingresos', 'desc')
            ->get();

        // Productos con bajo stock (SIN filtro de fechas - estado actual)
        $productosBajoStock = Producto::with(['categoria', 'variantes'])
            ->where('visible', true)
            ->get()
            ->filter(function($producto) {
                return $producto->variantes->sum('stock') <= 10;
            })
            ->map(function($producto) {
                $stockTotal = $producto->variantes->sum('stock');
                $stockDetalle = $producto->variantes->map(function($v) {
                    $talla = $v->talla ?? 'Sin talla';
                    $color = $v->color ?? 'Sin color';
                    return "{$talla} - {$color}: {$v->stock}";
                })->implode(', ');

                return (object) [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'imagen_url' => $producto->imagen_url,
                    'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                    'stock_total' => $stockTotal,
                    'stock_detalle' => $stockDetalle ?: 'Sin variantes',
                    'precio_venta' => $producto->precio_venta
                ];
            })
            ->values();

        // REPORTE DE USUARIOS - Todos los usuarios del sistema
        $usuariosSistema = User::with(['rol', 'persona'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($usuario) {
                $totalVentasRealizadas = Venta::where('user_id', $usuario->id)
                    ->where('estado_venta', 'confirmada')
                    ->count();

                $totalMontoVendido = Venta::where('user_id', $usuario->id)
                    ->where('estado_venta', 'confirmada')
                    ->sum('precio_total');

                return (object) [
                    'id' => $usuario->id,
                    'nombre_completo' => $usuario->persona->nombre . ' ' . $usuario->persona->apellidos,
                    'email' => $usuario->email,
                    'username' => $usuario->username,
                    'rol' => $usuario->rol->nombre ?? 'Sin rol',
                    'activo' => $usuario->activo ?? true,
                    'fecha_registro' => $usuario->created_at,
                    'total_ventas' => $totalVentasRealizadas,
                    'total_vendido' => $totalMontoVendido
                ];
            });

        // REPORTE DE PROVEEDORES
        $proveedores = Proveedor::with(['productos'])
            ->get()
            ->map(function($proveedor) {
                $totalProductos = $proveedor->productos->count();
                $productosActivos = $proveedor->productos->where('visible', true)->count();

                return (object) [
                    'id' => $proveedor->id,
                    'nombre_empresa' => $proveedor->nombre_empresa,
                    'contacto_nombre' => $proveedor->contacto_nombre,
                    'telefono' => $proveedor->telefono,
                    'email' => $proveedor->email,
                    'direccion' => $proveedor->direccion,
                    'total_productos' => $totalProductos,
                    'productos_activos' => $productosActivos,
                    'fecha_registro' => $proveedor->created_at
                ];
            });

        // Vendedores para el filtro
        $vendedores = User::whereHas('rol', function($q) {
            $q->whereIn('nombre', ['cajero', 'admin']);
        })->with('persona')->get();

        return view('admin.reportes.index', compact(
            'ventas',
            'totalVentas',
            'totalIngresos',
            'ticketPromedio',
            'productosMasVendidos',
            'totalProductosVendidos',
            'clientesFrecuentes',
            'ventasPorCategoria',
            'productosBajoStock',
            'usuariosSistema',
            'proveedores',
            'vendedores',
            'fechaInicio',
            'fechaFin'
        ));
    }

    /**
     * Exportar reporte a PDF usando una sola plantilla
     */
    public function exportarPDF(Request $request)
    {
        $this->checkAdmin();
        $tipo = $request->get('tipo', 'ventas');

        // Obtener fechas del request
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        $titulo = '';
        $subtitulo = '';
        $contenidoHtml = '';

        switch ($tipo) {
            case 'ventas':
                $query = Venta::with(['user.persona', 'pago.tipoPago'])
                              ->where('estado_venta', 'confirmada')
                              ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

                if ($request->filled('vendedor_id')) {
                    $query->where('user_id', $request->vendedor_id);
                }

                $ventas = $query->orderBy('created_at', 'desc')->get();
                $totalIngresos = $ventas->sum('precio_total');
                $totalVentas = $ventas->count();

                $titulo = '📊 Reporte de Ventas';
                $subtitulo = "Período: " . date('d/m/Y', strtotime($fechaInicio)) . " - " . date('d/m/Y', strtotime($fechaFin));
                $contenidoHtml = $this->generarTablaVentas($ventas, $totalIngresos, $totalVentas);
                break;

            case 'productos':
                $productos = DB::table('detalle_ventas')
                    ->join('producto_variantes', 'detalle_ventas.producto_variante_id', '=', 'producto_variantes.id')
                    ->join('productos', 'producto_variantes.producto_id', '=', 'productos.id')
                    ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
                    ->where('ventas.estado_venta', 'confirmada')
                    ->whereBetween('ventas.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                    ->select(
                        'productos.nombre',
                        DB::raw('SUM(detalle_ventas.cantidad) as total_vendidos'),
                        DB::raw('SUM(detalle_ventas.subtotal) as total_ingresos')
                    )
                    ->groupBy('productos.nombre')
                    ->orderBy('total_vendidos', 'desc')
                    ->get();

                $totalIngresos = $productos->sum('total_ingresos');
                $titulo = '🏆 Productos Más Vendidos';
                $subtitulo = "Período: " . date('d/m/Y', strtotime($fechaInicio)) . " - " . date('d/m/Y', strtotime($fechaFin));
                $contenidoHtml = $this->generarTablaProductos($productos, $totalIngresos);
                break;

            case 'clientes':
                $clientes = Venta::where('estado_venta', 'confirmada')
                    ->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                    ->with('user.persona')
                    ->get()
                    ->groupBy('user_id')
                    ->map(function($ventasGrupo) {
                        $user = $ventasGrupo->first()->user;
                        return (object) [
                            'nombre' => $user->persona->nombre . ' ' . $user->persona->apellidos,
                            'email' => $user->email,
                            'total_compras' => $ventasGrupo->count(),
                            'total_gastado' => $ventasGrupo->sum('precio_total'),
                        ];
                    })
                    ->sortByDesc('total_gastado')
                    ->values();

                $titulo = '👥 Clientes Frecuentes';
                $subtitulo = "Período: " . date('d/m/Y', strtotime($fechaInicio)) . " - " . date('d/m/Y', strtotime($fechaFin));
                $contenidoHtml = $this->generarTablaClientes($clientes);
                break;

            case 'categorias':
                $categorias = DB::table('detalle_ventas')
                    ->join('producto_variantes', 'detalle_ventas.producto_variante_id', '=', 'producto_variantes.id')
                    ->join('productos', 'producto_variantes.producto_id', '=', 'productos.id')
                    ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
                    ->join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
                    ->where('ventas.estado_venta', 'confirmada')
                    ->whereBetween('ventas.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                    ->select(
                        'categorias.nombre as categoria',
                        DB::raw('SUM(detalle_ventas.cantidad) as total_vendidos'),
                        DB::raw('SUM(detalle_ventas.subtotal) as total_ingresos')
                    )
                    ->groupBy('categorias.nombre')
                    ->orderBy('total_ingresos', 'desc')
                    ->get();

                $totalIngresos = $categorias->sum('total_ingresos');
                $titulo = '📊 Ventas por Categoría';
                $subtitulo = "Período: " . date('d/m/Y', strtotime($fechaInicio)) . " - " . date('d/m/Y', strtotime($fechaFin));
                $contenidoHtml = $this->generarTablaCategorias($categorias, $totalIngresos);
                break;

            case 'inventario':
                $productos = Producto::with(['categoria', 'variantes'])
                    ->where('visible', true)
                    ->get()
                    ->filter(function($producto) {
                        return $producto->variantes->sum('stock') <= 10;
                    })
                    ->map(function($producto) {
                        return (object) [
                            'nombre' => $producto->nombre,
                            'categoria' => $producto->categoria->nombre ?? 'Sin categoría',
                            'stock' => $producto->variantes->sum('stock'),
                            'precio' => $producto->precio_venta
                        ];
                    })
                    ->values();

                $titulo = '⚠️ Productos con Stock Bajo';
                $subtitulo = "Reporte al: " . date('d/m/Y');
                $contenidoHtml = $this->generarTablaInventario($productos);
                break;

            case 'usuarios':
                $usuarios = User::with(['rol', 'persona'])
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->map(function($usuario) {
                        $totalVentas = Venta::where('user_id', $usuario->id)
                            ->where('estado_venta', 'confirmada')
                            ->count();

                        $totalVendido = Venta::where('user_id', $usuario->id)
                            ->where('estado_venta', 'confirmada')
                            ->sum('precio_total');

                        return (object) [
                            'username' => $usuario->username,
                            'nombre' => $usuario->persona->nombre . ' ' . $usuario->persona->apellidos,
                            'email' => $usuario->email,
                            'rol' => $usuario->rol->nombre ?? 'Sin rol',
                            'activo' => $usuario->activo ?? true,
                            'total_ventas' => $totalVentas,
                            'total_vendido' => $totalVendido
                        ];
                    });

                $titulo = '👥 Usuarios del Sistema';
                $subtitulo = "Reporte general de usuarios registrados";
                $contenidoHtml = $this->generarTablaUsuarios($usuarios);
                break;

            case 'proveedores':
                $proveedores = Proveedor::with(['productos'])
                    ->get()
                    ->map(function($proveedor) {
                        return (object) [
                            'nombre_empresa' => $proveedor->nombre_empresa ?? 'N/A',
                            'contacto_nombre' => $proveedor->contacto_nombre ?? 'N/A',
                            'telefono' => $proveedor->telefono ?? 'N/A',
                            'email' => $proveedor->email ?? 'N/A',
                            'total_productos' => $proveedor->productos->count(),
                            'productos_activos' => $proveedor->productos->where('visible', true)->count(),
                            'fecha_registro' => $proveedor->created_at
                        ];
                    });

                $titulo = '🏢 Reporte de Proveedores';
                $subtitulo = "Reporte general de proveedores registrados";
                $contenidoHtml = $this->generarTablaProveedores($proveedores);
                break;

            default:
                return redirect()->back()->with('error', 'Tipo de reporte no válido');
        }

        $pdf = Pdf::loadView('admin.reportes.plantilla', compact('titulo', 'subtitulo', 'contenidoHtml'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($tipo . '_' . date('Y-m-d') . '.pdf');
    }

    // ==================== MÉTODOS PRIVADOS PARA GENERAR HTML ====================

    private function generarTablaVentas($ventas, $totalIngresos, $totalVentas)
    {
        $html = '
        <table class="tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>';

        foreach($ventas as $venta) {
            $html .= '
                <tr>
                    <td>' . $venta->created_at->format('d/m/Y H:i') . '</td>
                    <td>' . ($venta->user->persona->nombre ?? 'N/A') . ' ' . ($venta->user->persona->apellidos ?? '') . '</td>
                    <td>' . $venta->user->username . '</td>
                    <td class="text-right">Bs ' . number_format($venta->precio_total, 2) . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>

        <div class="resumen">
            <div class="resumen-item">
                <span>Total de Ventas:</span>
                <span class="text-bold">' . $totalVentas . ' ventas</span>
            </div>
            <div class="resumen-item">
                <span>Total de Ingresos:</span>
                <span class="text-bold text-primary">Bs ' . number_format($totalIngresos, 2) . '</span>
            </div>
            <div class="resumen-total">
                <span>Ticket Promedio:</span>
                <span>Bs ' . number_format($totalVentas > 0 ? $totalIngresos / $totalVentas : 0, 2) . '</span>
            </div>
        </div>';

        return $html;
    }

    private function generarTablaProductos($productos, $totalIngresos)
    {
        $html = '
        <table class="tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th class="text-center">Unidades Vendidas</th>
                    <th class="text-right">Total Ingresos</th>
                    <th class="text-center">% del Total</th>
                </tr>
            </thead>
            <tbody>';

        foreach($productos as $producto) {
            $porcentaje = ($producto->total_ingresos / ($totalIngresos ?? 1)) * 100;
            $html .= '
                <tr>
                    <td>' . $producto->nombre . '</td>
                    <td class="text-center">' . $producto->total_vendidos . '</td>
                    <td class="text-right">Bs ' . number_format($producto->total_ingresos, 2) . '</td>
                    <td class="text-center">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ' . $porcentaje . '%"></div>
                        </div>
                        ' . number_format($porcentaje, 1) . '%
                    </td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>

        <div class="resumen">
            <div class="resumen-item">
                <span>Total de Productos Vendidos:</span>
                <span class="text-bold">' . $productos->sum('total_vendidos') . ' unidades</span>
            </div>
            <div class="resumen-total">
                <span>Total de Ingresos:</span>
                <span>Bs ' . number_format($totalIngresos, 2) . '</span>
            </div>
        </div>';

        return $html;
    }

    private function generarTablaClientes($clientes)
    {
        $html = '
        <table class="tabla">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Email</th>
                    <th class="text-center">Compras</th>
                    <th class="text-right">Total Gastado</th>
                    <th class="text-right">Ticket Promedio</th>
                </tr>
            </thead>
            <tbody>';

        foreach($clientes as $cliente) {
            $html .= '
                <tr>
                    <td>' . $cliente->nombre . '</td>
                    <td>' . $cliente->email . '</td>
                    <td class="text-center">' . $cliente->total_compras . '</td>
                    <td class="text-right">Bs ' . number_format($cliente->total_gastado, 2) . '</td>
                    <td class="text-right">Bs ' . number_format($cliente->total_gastado / $cliente->total_compras, 2) . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>

        <div class="resumen">
            <div class="resumen-item">
                <span>Total de Clientes:</span>
                <span class="text-bold">' . $clientes->count() . '</span>
            </div>
            <div class="resumen-total">
                <span>Total Gastado por Clientes:</span>
                <span>Bs ' . number_format($clientes->sum('total_gastado'), 2) . '</span>
            </div>
        </div>';

        return $html;
    }

    private function generarTablaCategorias($categorias, $totalIngresos)
    {
        $html = '
        <table class="tabla">
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th class="text-center">Unidades Vendidas</th>
                    <th class="text-right">Total Ingresos</th>
                    <th class="text-center">% del Total</th>
                </tr>
            </thead>
            <tbody>';

        foreach($categorias as $categoria) {
            $porcentaje = ($categoria->total_ingresos / ($totalIngresos ?? 1)) * 100;
            $html .= '
                <tr>
                    <td>' . $categoria->categoria . '</td>
                    <td class="text-center">' . $categoria->total_vendidos . '</td>
                    <td class="text-right">Bs ' . number_format($categoria->total_ingresos, 2) . '</td>
                    <td class="text-center">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ' . $porcentaje . '%"></div>
                        </div>
                        ' . number_format($porcentaje, 1) . '%
                    </td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>

        <div class="resumen">
            <div class="resumen-item">
                <span>Total de Categorías:</span>
                <span class="text-bold">' . $categorias->count() . '</span>
            </div>
            <div class="resumen-total">
                <span>Total de Ingresos:</span>
                <span>Bs ' . number_format($totalIngresos, 2) . '</span>
            </div>
        </div>';

        return $html;
    }

    private function generarTablaInventario($productos)
    {
        $html = '
        <table class="tabla">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th class="text-center">Stock Actual</th>
                    <th class="text-right">Precio Venta</th>
                </tr>
            </thead>
            <tbody>';

        foreach($productos as $producto) {
            $badgeClass = $producto->stock <= 3 ? 'badge-danger' : 'badge-warning';
            $html .= '
                <tr>
                    <td>' . $producto->nombre . '</td>
                    <td>' . $producto->categoria . '</td>
                    <td class="text-center">
                        <span class="badge ' . $badgeClass . '">' . $producto->stock . ' unidades</span>
                    </td>
                    <td class="text-right">Bs ' . number_format($producto->precio, 2) . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>

        <div class="resumen">
            <div class="resumen-item">
                <span>Total de Productos Críticos (Stock ≤ 3):</span>
                <span class="text-bold">' . $productos->filter(fn($p) => $p->stock <= 3)->count() . '</span>
            </div>
            <div class="resumen-item">
                <span>Total de Productos con Stock Bajo (4-10):</span>
                <span class="text-bold">' . $productos->filter(fn($p) => $p->stock > 3 && $p->stock <= 10)->count() . '</span>
            </div>
            <div class="resumen-total">
                <span>Total de Unidades por Reponer:</span>
                <span>' . $productos->sum('stock') . ' unidades</span>
            </div>
        </div>';

        return $html;
    }

    private function generarTablaUsuarios($usuarios)
    {
        $html = '
        <table class="tabla">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-center">Estado</th>
                    <th class="text-center">Ventas</th>
                    <th class="text-right">Total Vendido</th>
                </tr>
            </thead>
            <tbody>';

        foreach($usuarios as $usuario) {
            $html .= '
                <tr>
                    <td>' . $usuario->username . '</td>
                    <td>' . $usuario->nombre . '</td>
                    <td>' . $usuario->email . '</td>
                    <td>' . ucfirst($usuario->rol) . '</td>
                    <td class="text-center">' . ($usuario->activo ? 'Activo' : 'Inactivo') . '</td>
                    <td class="text-center">' . $usuario->total_ventas . '</td>
                    <td class="text-right">Bs ' . number_format($usuario->total_vendido, 2) . '</td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>

        <div class="resumen">
            <div class="resumen-item">
                <span>Total de Usuarios:</span>
                <span class="text-bold">' . $usuarios->count() . '</span>
            </div>
            <div class="resumen-item">
                <span>Ventas Totales:</span>
                <span class="text-bold">' . $usuarios->sum('total_ventas') . '</span>
            </div>
            <div class="resumen-total">
                <span>Ingresos Totales:</span>
                <span>Bs ' . number_format($usuarios->sum('total_vendido'), 2) . '</span>
            </div>
        </div>';

        return $html;
    }

    private function generarTablaProveedores($proveedores)
    {
        $html = '
        <table class="tabla">
            <thead>
                <tr>
                    <th>Proveedor</th>
                    <th>Contacto</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th class="text-center">Total Productos</th>
                    <th class="text-center">Activos</th>
                </tr>
            </thead>
            <tbody>';

        foreach($proveedores as $proveedor) {
            $porcentaje = $proveedor->total_productos > 0 ? ($proveedor->productos_activos / $proveedor->total_productos) * 100 : 0;
            $html .= '
                <tr>
                    <td><strong>' . $proveedor->nombre_empresa . '</strong></td>
                    <td>' . $proveedor->contacto_nombre . '</td>
                    <td>' . $proveedor->telefono . '</td>
                    <td>' . $proveedor->email . '</td>
                    <td class="text-center">' . $proveedor->total_productos . '</td>
                    <td class="text-center">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: ' . $porcentaje . '%"></div>
                        </div>
                        ' . $proveedor->productos_activos . '/' . $proveedor->total_productos . '
                    </td>
                </tr>';
        }

        $html .= '
            </tbody>
        </table>

        <div class="resumen">
            <div class="resumen-item">
                <span>Total de Proveedores:</span>
                <span class="text-bold">' . $proveedores->count() . '</span>
            </div>
            <div class="resumen-item">
                <span>Total de Productos:</span>
                <span class="text-bold">' . $proveedores->sum('total_productos') . '</span>
            </div>
            <div class="resumen-total">
                <span>Productos Activos:</span>
                <span>' . $proveedores->sum('productos_activos') . '</span>
            </div>
        </div>';

        return $html;
    }

    // ==================== MÉTODOS EXISTENTES ====================

    /**
     * Reporte de ventas detallado
     */
    public function ventas(Request $request)
    {
        $this->checkAdmin();
        return redirect()->route('reportes.index', array_merge(['tipo' => 'ventas'], $request->all()));
    }

    /**
     * Reporte de productos más vendidos
     */
    public function productosMasVendidos(Request $request)
    {
        $this->checkAdmin();
        return redirect()->route('reportes.index', array_merge(['tipo' => 'productos'], $request->all()));
    }

    /**
     * Reporte de clientes frecuentes
     */
    public function clientesFrecuentes(Request $request)
    {
        $this->checkAdmin();
        return redirect()->route('reportes.index', array_merge(['tipo' => 'clientes'], $request->all()));
    }

    /**
     * Reporte de ventas por categoría
     */
    public function ventasPorCategoria(Request $request)
    {
        $this->checkAdmin();
        return redirect()->route('reportes.index', array_merge(['tipo' => 'categorias'], $request->all()));
    }

    /**
     * Reporte de inventario con bajo stock
     */
    public function inventarioBajoStock()
    {
        $this->checkAdmin();
        return redirect()->route('reportes.index', ['tipo' => 'inventario']);
    }
}
