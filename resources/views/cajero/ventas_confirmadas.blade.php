@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4 max-w-7xl mx-auto">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Ventas Históricas</h1>
        <p class="text-[#343c4c]/60 text-sm mt-1 font-medium">Registro histórico de transacciones validadas y completadas en el sistema.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-xl max-w-7xl mx-auto overflow-hidden border border-[#343c4c]/10 z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-[10px] font-black tracking-widest">
                <tr>
                    <th class="p-4">ID Venta</th>
                    <th class="p-4">Datos del Cliente</th>
                    <th class="p-4 text-center">Método de Pago</th>
                    <th class="p-4 text-right">Total Cobrado</th>
                    <th class="p-4 text-right">Fecha de Operación</th>
                </tr>
            </thead>
            <tbody class="text-[#343c4c] font-medium">
                @forelse($ventas as $venta)
                    <tr class="hover:bg-[#f4f4f4]/50 transition-colors bg-white">
                        <td class="p-4">
                            <span class="font-black text-xl text-[#343c4c] tracking-wide">#{{ $venta->id }}</span>
                        </td>
                        <td class="p-4">
                            <p class="font-bold text-[#343c4c] uppercase">{{ $venta->user->persona->nombre }} {{ $venta->user->persona->apellidos }}</p>
                            <span class="text-[11px] font-semibold text-[#343c4c]/50">{{ $venta->user->email }}</span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-block px-3 py-1 bg-[#0464a4]/10 text-[#0464a4] text-[10px] font-black rounded-md uppercase tracking-wider border border-[#0464a4]/20 shadow-sm">
                                {{ $venta->pago->tipoPago->nombre }}
                            </span>
                        </td>
                        <td class="p-4 text-right font-black text-xl text-[#dc043c] drop-shadow-sm">
                            Bs {{ number_format($venta->precio_total, 2) }}
                        </td>
                        <td class="p-4 text-right">
                            <span class="text-xs font-bold text-[#343c4c]/70">{{ $venta->fecha_venta->format('d/m/Y') }}</span>
                            <span class="block text-[10px] text-[#dcb47c] font-black mt-0.5">{{ $venta->fecha_venta->format('H:i') }}</span>
                        </td>
                    </tr>

                    <tr class="border-b-2 border-[#f4f4f4] bg-[#f4f4f4]/30">
                        <td colspan="5" class="px-6 py-4">
                            <div class="text-[9px] text-[#343c4c]/50 font-black uppercase tracking-widest mb-2.5 flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                                Detalle de Artículos:
                            </div>
                            <ul class="flex flex-wrap gap-2.5">
                                @foreach($venta->detalles as $det)
                                    <li class="bg-white px-3 py-1.5 rounded-lg border border-[#343c4c]/10 shadow-sm flex items-center space-x-2 group">
                                        <span class="bg-[#343c4c] text-[#dcb47c] text-[10px] font-black px-1.5 py-0.5 rounded">x{{ $det->cantidad }}</span>
                                        
                                        <span class="text-xs font-bold text-[#343c4c]">{{ $det->variante->producto->nombre }}</span>
                                        
                                        <span class="text-[#343c4c]/20 mx-1">|</span>
                                        
                                        <span class="text-[11px] font-black text-[#0464a4]">Bs {{ number_format($det->subtotal, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-[#343c4c]/40 bg-white">
                            <svg class="w-16 h-16 mx-auto text-[#343c4c]/20 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="block font-bold text-base text-[#343c4c]">No hay ventas registradas aún.</span>
                            <p class="text-xs mt-1">Las transacciones confirmadas aparecerán en este historial.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection