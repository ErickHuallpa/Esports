@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Promociones y Descuentos</h1>
        <p class="text-gray-500 text-sm">Configura rebajas temporales para los productos del catálogo.</p>
    </div>
    <button onclick="document.getElementById('crearModal').classList.remove('hidden')" class="inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm transition">
        🎯 Lanzar Nueva Oferta
    </button>
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="p-4">Producto en Promoción</th>
                    <th class="p-4 text-center">Descuento (%)</th>
                    <th class="p-4">Vigencia (Inicio - Fin)</th>
                    <th class="p-4 text-center">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @forelse($ofertas as $of)
                    @php 
                        $activa = now()->between($of->fecha_inicio, $of->fecha_fin);
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-900 line-clamp-2" title="{{ $of->producto->nombre }}">{{ $of->producto->nombre }}</td>
                        <td class="p-4 text-center font-black text-red-600">-{{ $of->porcentaje_descuento }}%</td>
                        <td class="p-4 text-xs font-medium">
                            {{ \Carbon\Carbon::parse($of->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($of->fecha_fin)->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-center">
                            @if($activa)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-[10px] font-bold rounded-full uppercase">Activa Ahora</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-[10px] font-bold rounded-full uppercase">Expirada / Pendiente</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.ofertas.destroy', $of->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('¿Finalizar esta promoción inmediatamente?')" class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1.5 rounded-lg text-xs font-bold transition">Cancelar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-400">No existen campañas de descuento registradas actualmente.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="crearModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-base">Registrar Promoción</h3>
            <button onclick="document.getElementById('crearModal').classList.add('hidden')" class="text-gray-400 text-2xl font-bold">&times;</button>
        </div>
        <form action="{{ route('admin.ofertas.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Producto Objetivo *</label>
                    <select name="producto_id" required class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500 bg-gray-50">
                        <option value="">Seleccione el producto...</option>
                        @foreach($productosDisponibles as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->nombre }} (Bs {{ $prod->precio_venta }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Porcentaje de Descuento (%) *</label>
                    <input type="number" name="porcentaje_descuento" min="1" max="99" required placeholder="Ej: 20" class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha de Inicio *</label>
                        <input type="date" name="fecha_inicio" required class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha de Cierre *</label>
                        <input type="date" name="fecha_fin" required class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm">Activar Oferta</button>
            </div>
        </form>
    </div>
</div>
@endsection