@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Ventas Confirmadas</h2>
    <p class="text-gray-500 text-sm">Registro histórico de transacciones validadas y completadas.</p>
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="p-4">ID Venta</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Pago</th>
                    <th class="p-4 text-right">Total</th>
                    <th class="p-4">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @forelse($ventas as $venta)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-900">#{{ $venta->id }}</td>
                        <td class="p-4">
                            <p class="font-bold text-gray-800">{{ $venta->user->persona->nombre }} {{ $venta->user->persona->apellidos }}</p>
                            <span class="text-xs text-gray-400">{{ $venta->user->email }}</span>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded">{{ $venta->pago->tipoPago->nombre }}</span>
                        </td>
                        <td class="p-4 text-right font-black text-gray-900">Bs {{ number_format($venta->precio_total, 2) }}</td>
                        <td class="p-4 text-xs">{{ $venta->fecha_venta->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-2 bg-gray-50">
                            <div class="text-xs text-gray-500 font-bold uppercase mb-2">Detalle de productos:</div>
                            <ul class="flex flex-wrap gap-2">
                                @foreach($venta->detalles as $det)
                                    <li class="bg-white px-2 py-1 rounded border border-gray-200">
                                        {{ $det->variante->producto->nombre }} (x{{ $det->cantidad }}) - Bs {{ number_format($det->subtotal, 2) }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="p-8 text-center text-gray-400">No hay ventas registradas aún.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection