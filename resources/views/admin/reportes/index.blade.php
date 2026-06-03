@extends('layouts.app')

@section('title', 'Generador de Reportes - E-Sports Store')

@section('content')
<div class="bg-gray-100 min-h-screen py-8">
    <div class="container mx-auto px-6">

        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-black text-gray-800">📊 Generador de Reportes</h1>
            <p class="text-gray-600 mt-1">Selecciona el tipo de reporte y el rango de fechas para visualizar y exportar
            </p>
        </div>

        <!-- Formulario Principal -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden max-w-6xl mx-auto">

            <!-- Cabecera del formulario -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 text-white">
                <h2 class="text-xl font-bold flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Configuración del Reporte
                </h2>
                <p class="text-blue-100 text-sm mt-1">Complete los campos para generar el reporte deseado</p>
            </div>

            <!-- Filtros -->
            <form method="GET" action="{{ route('reportes.index') }}" class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">📋 Tipo de Reporte</label>
                        <select name="tipo" id="tipoReporte"
                            class="w-full rounded-lg border-gray-300 p-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            <option value="ventas" {{ request('tipo', 'ventas') == 'ventas' ? 'selected' : '' }}>📊
                                Reporte de Ventas</option>
                            <option value="productos" {{ request('tipo') == 'productos' ? 'selected' : '' }}>🏆
                                Productos Más Vendidos</option>
                            <option value="clientes" {{ request('tipo') == 'clientes' ? 'selected' : '' }}>👥 Clientes
                                Frecuentes</option>
                            <option value="categorias" {{ request('tipo') == 'categorias' ? 'selected' : '' }}>📈 Ventas
                                por Categoría</option>
                            <option value="inventario" {{ request('tipo') == 'inventario' ? 'selected' : '' }}>⚠️
                                Inventario - Stock Bajo</option>
                            <option value="inventario_general" {{ request('tipo') == 'inventario_general' ? 'selected' : '' }}>📦
                                Inventario General</option>
                            <option value="usuarios" {{ request('tipo') == 'usuarios' ? 'selected' : '' }}>👤 Usuarios
                                del Sistema</option>
                            <option value="proveedores" {{ request('tipo') == 'proveedores' ? 'selected' : '' }}>🏢
                                Listado de Proveedores</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">↕️ Ordenar por</label>
                        <select name="orden" id="ordenReporte"
                            class="w-full rounded-lg border-gray-300 p-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            <!-- Opciones generadas por JS -->
                        </select>
                    </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">📅 Rango de Fechas</label>
                        <div class="flex gap-2">
                            <input type="date" name="fecha_inicio" id="f_inicio"
                                value="{{ request('fecha_inicio', $fechaInicio ?? date('Y-m-01')) }}"
                                class="w-full rounded-lg border-gray-300 p-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <input type="date" name="fecha_fin" id="f_fin"
                                value="{{ request('fecha_fin', $fechaFin ?? date('Y-m-d')) }}"
                                class="w-full rounded-lg border-gray-300 p-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        </div>
                        <div class="flex justify-between gap-1 mt-2 text-xs font-bold">
                            <button type="button" onclick="setRango(7)" class="text-blue-600 hover:text-blue-800 hover:underline transition">7 días</button>
                            <button type="button" onclick="setRango(15)" class="text-blue-600 hover:text-blue-800 hover:underline transition">15 días</button>
                            <button type="button" onclick="setRango(30)" class="text-blue-600 hover:text-blue-800 hover:underline transition">Mes</button>
                            <button type="button" onclick="setRango(365)" class="text-blue-600 hover:text-blue-800 hover:underline transition">Año</button>
                        </div>
                    </div>
                    <div class="flex items-start mt-7">
                        <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition">
                            🔍 Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>

            <!-- Botón Exportar y Resumen -->
            <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    Mostrando datos del período:
                    <strong>{{ date('d/m/Y', strtotime($fechaInicio ?? date('Y-m-01'))) }}</strong>
                    al <strong>{{ date('d/m/Y', strtotime($fechaFin ?? date('Y-m-d'))) }}</strong>
                </div>
                <a href="{{ route('reportes.exportar-pdf', request()->all()) }}" target="_blank"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z">
                        </path>
                    </svg>
                    EXPORTAR A PDF
                </a>
            </div>

            <!-- Contenido de Datos según el tipo de reporte -->
            <div class="p-6">
                @php
                $tipoActual = request('tipo', 'ventas');
                @endphp

                <!-- TABLA DE VENTAS -->
                @if($tipoActual == 'ventas')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vendedor
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pago</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ventas ?? [] as $venta)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">{{ $venta->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $venta->user->persona->nombre ?? 'N/A' }}
                                    {{ $venta->user->persona->apellidos ?? '' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $venta->pago->verificador->username ?? 'Sistema Web' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $venta->pago->tipoPago->nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-green-600">Bs
                                    {{ number_format($venta->precio_total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay ventas en el período
                                    seleccionado</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-bold">Total:</td>
                                <td class="px-4 py-3 text-right font-bold text-green-600">Bs
                                    {{ number_format($totalIngresos ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right font-bold">Total Ventas:</td>
                                <td class="px-4 py-3 text-right font-bold">{{ $totalVentas ?? 0 }}</td>
                            </tr>
                        </tfoot>
                    </table>
                    @if(isset($ventas) && method_exists($ventas, 'links'))
                    <div class="mt-4">{{ $ventas->withQueryString()->links() }}</div>
                    @endif
                </div>

                <!-- TABLA DE PRODUCTOS MÁS VENDIDOS -->
                @elseif($tipoActual == 'productos')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unidades
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ingresos
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" title="Porcentaje de ingresos que este producto generó respecto al total del período">% Aporte Ventas <span class="text-blue-500 cursor-help">(?)</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productosMasVendidos ?? [] as $producto)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $producto->nombre }}</td>
                                <td class="px-4 py-3 text-right text-sm">{{ $producto->total_vendidos }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-green-600">Bs
                                    {{ number_format($producto->total_ingresos, 2) }}</td>
                                <td class="px-4 py-3 text-center text-sm">
                                    {{ number_format(($producto->total_ingresos / ($totalIngresos ?? 1)) * 100, 1) }}%
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay productos vendidos en
                                    el período</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE CLIENTES FRECUENTES -->
                @elseif($tipoActual == 'clientes')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Compras
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total
                                    Gastado</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="Gasto promedio por cada compra realizada por este cliente">Ticket
                                    Promedio <span class="text-blue-500 cursor-help">(?)</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($clientesFrecuentes ?? [] as $cliente)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $cliente->nombre }}</td>
                                <td class="px-4 py-3 text-center text-sm">{{ $cliente->total_compras }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-green-600">Bs
                                    {{ number_format($cliente->total_gastado, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm">Bs
                                    {{ number_format($cliente->promedio_compra, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay clientes en el
                                    período</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE VENTAS POR CATEGORÍA -->
                @elseif($tipoActual == 'categorias')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unidades
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ingresos
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" title="Porcentaje de ingresos que esta categoría generó">% Aporte Ventas <span class="text-blue-500 cursor-help">(?)</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($ventasPorCategoria ?? [] as $categoria)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $categoria->categoria }}</td>
                                <td class="px-4 py-3 text-right text-sm">{{ $categoria->total_vendidos }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-green-600">Bs
                                    {{ number_format($categoria->total_ingresos, 2) }}</td>
                                <td class="px-4 py-3 text-center text-sm">
                                    {{ number_format(($categoria->total_ingresos / ($totalIngresos ?? 1)) * 100, 1) }}%
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay ventas por categoría
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE INVENTARIO BAJO STOCK -->
                @elseif($tipoActual == 'inventario')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unitario</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="Stock Total * Precio Unitario">Valorización <span class="text-blue-500 cursor-help">(?)</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($productosBajoStock ?? [] as $producto)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $producto->nombre }}</td>
                                <td class="px-4 py-3 text-sm">{{ $producto->categoria }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-bold {{ $producto->stock_total <= 3 ? 'bg-red-100 text-red-700' : ($producto->stock_total <= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                        {{ $producto->stock_total }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">Bs
                                    {{ number_format($producto->precio_venta, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-700">Bs
                                    {{ number_format($producto->valorizacion, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay productos que coincidan con este reporte</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE INVENTARIO GENERAL (Reutiliza la estructura) -->
                @elseif($tipoActual == 'inventario_general')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unitario</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase" title="Stock Total * Precio Unitario">Valorización <span class="text-blue-500 cursor-help">(?)</span></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($inventarioGeneral ?? [] as $producto)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $producto->nombre }}</td>
                                <td class="px-4 py-3 text-sm">{{ $producto->categoria }}</td>
                                <td class="px-4 py-3 text-right text-sm">
                                    <span
                                        class="px-2 py-1 rounded-full text-xs font-bold {{ $producto->stock_total <= 3 ? 'bg-red-100 text-red-700' : ($producto->stock_total <= 10 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                                        {{ $producto->stock_total }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">Bs
                                    {{ number_format($producto->precio_venta, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-700">Bs
                                    {{ number_format($producto->valorizacion, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">El inventario está vacío</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE USUARIOS -->
                @elseif($tipoActual == 'usuarios')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Rol</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($usuariosSistema ?? [] as $usuario)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm">{{ $usuario->username }}</td>
                                <td class="px-4 py-3 text-sm">{{ $usuario->nombre_completo }}</td>
                                <td class="px-4 py-3 text-sm">{{ $usuario->email }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $usuario->rol == 'admin' ? 'bg-red-100 text-red-700' : ($usuario->rol == 'cajero' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700') }}">
                                        {{ ucfirst($usuario->rol) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $usuario->activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay usuarios registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- TABLA DE PROVEEDORES -->
                @elseif($tipoActual == 'proveedores')
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Proveedor
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contacto
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Productos
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($proveedores ?? [] as $proveedor)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium">{{ $proveedor->nombre_empresa }}</td>
                                <td class="px-4 py-3 text-sm">{{ $proveedor->contacto_nombre ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm">{{ $proveedor->telefono ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-center text-sm">{{ $proveedor->total_productos }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No hay proveedores
                                    registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Mantener el tipo de reporte en el select
const selectTipo = document.getElementById('tipoReporte');
const selectOrden = document.getElementById('ordenReporte');

const opcionesOrden = {
    'ventas': [
        {val: 'fecha_desc', text: 'Fecha (Más recientes primero)'},
        {val: 'fecha_asc', text: 'Fecha (Más antiguas primero)'},
        {val: 'monto_desc', text: 'Monto (Mayor a menor)'},
        {val: 'monto_asc', text: 'Monto (Menor a mayor)'}
    ],
    'productos': [
        {val: 'ingresos_desc', text: 'Ingresos (Mayor a menor)'},
        {val: 'ingresos_asc', text: 'Ingresos (Menor a mayor)'},
        {val: 'unidades_desc', text: 'Unidades Vendidas (Mayor a menor)'},
        {val: 'unidades_asc', text: 'Unidades Vendidas (Menor a mayor)'}
    ],
    'clientes': [
        {val: 'gasto_desc', text: 'Total Gastado (Mayor a menor)'},
        {val: 'compras_desc', text: 'Cantidad de Compras (Mayor a menor)'},
        {val: 'ticket_desc', text: 'Ticket Promedio (Mayor a menor)'}
    ],
    'categorias': [
        {val: 'ingresos_desc', text: 'Ingresos (Mayor a menor)'},
        {val: 'unidades_desc', text: 'Unidades Vendidas (Mayor a menor)'}
    ],
    'inventario': [
        {val: 'stock_asc', text: 'Stock (Menor a mayor)'},
        {val: 'stock_desc', text: 'Stock (Mayor a menor)'},
        {val: 'valor_desc', text: 'Valorización (Mayor a menor)'}
    ],
    'inventario_general': [
        {val: 'stock_asc', text: 'Stock (Menor a mayor)'},
        {val: 'stock_desc', text: 'Stock (Mayor a menor)'},
        {val: 'valor_desc', text: 'Valorización (Mayor a menor)'}
    ],
    'usuarios': [
        {val: 'fecha_desc', text: 'Registro (Más recientes)'}
    ],
    'proveedores': [
        {val: 'fecha_desc', text: 'Registro (Más recientes)'}
    ]
};

function actualizarOpcionesOrden() {
    const tipo = selectTipo.value;
    const opciones = opcionesOrden[tipo] || [{val: 'defecto', text: 'Por Defecto'}];
    
    // Guardar selección actual si existe
    const currentVal = "{{ request('orden', '') }}";
    
    selectOrden.innerHTML = '';
    opciones.forEach(opt => {
        const optionElement = document.createElement('option');
        optionElement.value = opt.val;
        optionElement.text = opt.text;
        if(opt.val === currentVal) {
            optionElement.selected = true;
        }
        selectOrden.appendChild(optionElement);
    });
}

selectTipo.addEventListener('change', function() {
    actualizarOpcionesOrden();
    this.form.submit();
});

// Inicializar opciones de orden al cargar
actualizarOpcionesOrden();

function setRango(dias) {
    const hoy = new Date();
    const finStr = hoy.toISOString().split('T')[0];
    
    hoy.setDate(hoy.getDate() - dias);
    const inicioStr = hoy.toISOString().split('T')[0];
    
    document.getElementById('f_inicio').value = inicioStr;
    document.getElementById('f_fin').value = finStr;
    
    // Auto submit form
    document.getElementById('f_inicio').form.submit();
}
</script>

<style>
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    padding: 5px;
}

select,
input {
    transition: all 0.2s ease;
}

select:hover,
input:hover {
    border-color: #3b82f6;
}
</style>
@endsection