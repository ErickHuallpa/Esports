@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mis Pedidos</h1>
            <p class="text-gray-500 text-sm mt-1">Sigue el estado de tus compras y verifica la confirmación de tus pagos.</p>
        </div>
        <a href="{{ route('home') }}" class="text-blue-600 font-bold hover:underline text-sm">Volver a la tienda</a>
    </div>

    <div class="space-y-6">
        @forelse($ventas as $venta)
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                
                <div class="bg-gray-50 px-6 py-4 border-b flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Orden #{{ $venta->id }}</span>
                        <p class="text-sm font-semibold text-gray-800">{{ $venta->fecha_venta->format('d/m/Y h:i A') }}</p>
                    </div>
                    
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Transacción</span>
                        <p class="text-sm font-black text-gray-900">Bs {{ number_format($venta->precio_total, 2) }}</p>
                    </div>

                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Estado Financiero</span>
                        @if($venta->pago->estado === 'verificado')
                            <p class="text-sm font-bold text-green-600">✅ Pago Aprobado</p>
                        @elseif($venta->pago->estado === 'rechazado')
                            <p class="text-sm font-bold text-red-600">❌ Pago Rechazado</p>
                            <span class="text-xs text-red-400">Motivo: {{ $venta->pago->motivo_rechazo }}</span>
                        @else
                            <p class="text-sm font-bold text-amber-600">⏳ En verificación (QR)</p>
                        @endif
                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-4">
                        <h4 class="text-sm font-bold text-gray-800 border-b pb-2">Artículos Solicitados</h4>
                        @foreach($venta->detalles as $det)
                            <div class="flex items-center space-x-4">
                                @php 
                                    $fotos = json_decode($det->variante->producto->imagen_url, true) ?? [];
                                    $portada = count($fotos) > 0 ? $fotos[0] : null;
                                @endphp
                                <div class="w-16 h-16 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center overflow-hidden">
                                    @if($portada) <img src="{{ asset('storage/' . $portada) }}" class="w-full h-full object-cover"> @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $det->variante->producto->nombre }}</p>
                                    <p class="text-xs text-gray-500">
                                        Cant: {{ $det->cantidad }} | 
                                        @if($det->variante->talla) Talla: {{ $det->variante->talla }} @endif
                                        @if($det->variante->color) Color: {{ $det->variante->color }} @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-xl flex flex-col justify-center">
                        <h4 class="text-xs font-bold text-blue-800 uppercase mb-2">Estado Logístico</h4>
                        
                        @if($venta->orden)
                            <p class="text-lg font-black text-blue-900 mb-2">{{ $venta->orden->estado_orden }}</p>
                            
                            @if($venta->orden->envio)
                                <div class="text-sm text-blue-800 space-y-1">
                                    <p><strong>Destino:</strong> {{ $venta->orden->envio->ciudad_destino }}</p>
                                    <p><strong>Dirección:</strong> {{ $venta->orden->envio->direccion_destino }}</p>
                                    @if($venta->orden->envio->codigo_seguimiento)
                                        <p class="mt-2 text-xs bg-blue-100 p-2 rounded border border-blue-200">
                                            <strong>Guía / Tracking:</strong> {{ $venta->orden->envio->codigo_seguimiento }}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <p class="text-sm text-blue-800 font-semibold">🛒 Recojo programado en tienda física.</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 text-center rounded-2xl border shadow-sm">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <h3 class="text-xl font-bold text-gray-800">Aún no tienes pedidos</h3>
                <p class="text-gray-500 mt-2">Agrega productos a tu carrito y finaliza el proceso de checkout.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">Ir al Catálogo</a>
            </div>
        @endforelse
    </div>
</div>
@endsection