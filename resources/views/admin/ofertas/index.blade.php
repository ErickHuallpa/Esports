@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4 max-w-7xl mx-auto">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Promociones y Descuentos</h1>
        <p class="text-gray-500 text-sm mt-1">Configura rebajas temporales para los productos del catálogo.</p>
    </div>
    <button onclick="document.getElementById('crearModal').classList.remove('hidden')" 
        class="inline-flex justify-center items-center bg-[#0464a4] hover:bg-[#343c4c] text-white text-sm font-black uppercase tracking-wider py-3 px-6 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
        🎯 Lanzar Nueva Oferta
    </button>
</div>

<div class="bg-white rounded-2xl shadow-xl max-w-7xl mx-auto overflow-hidden border border-[#343c4c]/10 z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-xs font-black tracking-widest">
                <tr>
                    <th class="p-4">Producto en Promoción</th>
                    <th class="p-4 text-center">Descuento (%)</th>
                    <th class="p-4 text-center">Vigencia (Inicio - Fin)</th>
                    <th class="p-4 text-center">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                @forelse($ofertas as $of)
                    @php 
                        $activa = now()->between($of->fecha_inicio, $of->fecha_fin);
                    @endphp
                    <tr class="hover:bg-[#f4f4f4]/50 transition-colors">
                        <td class="p-4 font-black text-base text-[#343c4c] tracking-wide uppercase line-clamp-2" title="{{ $of->producto->nombre }}">
                            {{ $of->producto->nombre }}
                        </td>
                        <td class="p-4 text-center font-black text-xl text-[#dc043c]">
                            -{{ $of->porcentaje_descuento }}%
                        </td>
                        <td class="p-4 text-center text-xs font-bold text-[#343c4c]/70 uppercase tracking-wider">
                            {{ \Carbon\Carbon::parse($of->fecha_inicio)->format('d/m/Y') }} <br>
                            <span class="text-[#dcb47c]">AL</span> <br>
                            {{ \Carbon\Carbon::parse($of->fecha_fin)->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-center">
                            @if($activa)
                                <span class="inline-block px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black rounded-md uppercase tracking-wider border border-green-200">
                                    Activa Ahora
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black rounded-md uppercase tracking-wider border border-gray-200">
                                    Expirada / Pendiente
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.ofertas.destroy', $of->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                    onclick="return confirm('¿Estás seguro de finalizar esta promoción inmediatamente? El producto volverá a su precio original en el catálogo.')" 
                                    class="text-[#dc043c] bg-[#dc043c]/10 hover:bg-[#dc043c] hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                                    Cancelar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-400 bg-white">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m11 3v5a2 2 0 01-2 2H7a2 2 0 01-2-2v-5m14 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 11H4"></path>
                            </svg>
                            <span class="block font-bold text-base text-[#343c4c]/60">No existen campañas de descuento registradas actualmente.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="crearModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-[#343c4c]/10 transform transition-all">
        <div class="px-6 py-4 bg-[#343c4c] text-white border-b-4 border-[#dcb47c] flex justify-between items-center">
            <h3 class="font-black uppercase tracking-wider text-sm flex items-center">
                🎯 Registrar Promoción
            </h3>
            <button onclick="document.getElementById('crearModal').classList.add('hidden')" class="text-white hover:text-[#dc043c] text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form action="{{ route('admin.ofertas.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Producto Objetivo *</label>
                    <select name="producto_id" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer">
                        <option value="">Seleccione el producto...</option>
                        @foreach($productosDisponibles as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->nombre }} (Bs {{ $prod->precio_venta }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Porcentaje de Descuento (%) *</label>
                    <div class="relative">
                        <input type="number" name="porcentaje_descuento" min="1" max="99" placeholder="Ej: 20" required
                            class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 pl-10 text-sm focus:ring-2 focus:ring-[#0464a4] font-black text-[#dc043c]">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#dc043c] font-black">%</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Fecha de Inicio *</label>
                        <input type="date" name="fecha_inicio" required 
                            class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Fecha de Cierre *</label>
                        <input type="date" name="fecha_fin" required 
                            class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                    </div>
                </div>

                <div class="p-3 bg-[#0464a4]/10 border border-[#0464a4]/20 rounded-xl text-[#0464a4] flex items-start space-x-2">
                    <span class="text-sm">ℹ️</span>
                    <p class="text-[11px] font-medium leading-tight text-[#343c4c]">
                        <strong>Aviso Automático:</strong> Esta oferta alterará el precio final del catálogo de forma inmediata y se aplicará también dentro del carrito de compras de los usuarios durante las fechas especificadas.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('crearModal').classList.add('hidden')" 
                    class="px-4 py-2.5 bg-gray-200 text-[#343c4c] rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="px-5 py-2.5 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-wider rounded-xl shadow-md transition-colors text-xs">
                    Activar Oferta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection