@extends('layouts.app')

@section('content')
<div class="max-w-[1800px] w-full mx-auto my-4 md:my-8 px-2 md:px-8">
    
    <div class="mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 border-b-2 border-[#f4f4f4] pb-4">
        <div>
            <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Mis Pedidos</h1>
            <p class="text-[#343c4c]/60 text-sm mt-1 font-medium">Sigue el estado de tus compras y verifica la confirmación de tus pagos.</p>
        </div>
        <a href="{{ route('home') }}" class="text-[#0464a4] hover:text-[#dc043c] font-black uppercase tracking-widest text-xs transition-colors bg-[#f4f4f4] hover:bg-white px-4 py-2 rounded-lg shadow-sm border border-transparent hover:border-[#dc043c]/20">
            &larr; Volver a la tienda
        </a>
    </div>

    <div class="space-y-10">
        @forelse($ventas as $venta)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#343c4c]/10">
                
                <div class="bg-[#343c4c] px-6 py-5 border-b-4 border-[#dcb47c] flex flex-wrap justify-between items-center gap-6 relative">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-[#dcb47c] uppercase tracking-widest mb-1">Orden de Compra</span>
                        <span class="text-2xl font-black text-white">#{{ $venta->id }}</span>
                        <p class="text-xs font-bold text-[#f4f4f4]/60 mt-1">{{ $venta->fecha_venta->format('d/m/Y h:i A') }}</p>
                    </div>
                    
                    <div class="flex flex-col items-center">
                        <span class="text-[10px] font-black text-[#dcb47c] uppercase tracking-widest mb-1">Total Transacción</span>
                        <p class="text-2xl font-black text-[#dc043c] drop-shadow-md bg-white/10 px-4 py-1 rounded-lg">Bs {{ number_format($venta->precio_total, 2) }}</p>
                        @if($venta->descuento_aplicado > 0)
                            <p class="text-[10px] font-bold text-white bg-green-500/20 border border-green-500/50 px-2 py-0.5 rounded mt-1 shadow-sm">
                                CUPÓN APLICADO: -Bs {{ number_format($venta->descuento_aplicado, 2) }}
                            </p>
                        @endif
                    </div>

                    <div class="flex flex-col items-end space-y-2">
                        <div class="text-right">
                            <span class="text-[10px] font-black text-[#dcb47c] uppercase tracking-widest block mb-1">Estado Financiero</span>
                            @if($venta->pago->estado === 'verificado')
                                <p class="text-[11px] font-black text-[#0464a4] bg-[#f4f4f4] px-4 py-1.5 rounded-md uppercase tracking-wider shadow-sm border border-[#0464a4]/20 inline-block">
                                    ✅ Pago Aprobado
                                </p>
                            @elseif($venta->pago->estado === 'rechazado')
                                <p class="text-[11px] font-black text-white bg-[#dc043c] px-4 py-1.5 rounded-md uppercase tracking-wider shadow-sm inline-block">
                                    ❌ Pago Rechazado
                                </p>
                                <span class="text-[10px] text-white/80 font-medium mt-1.5 max-w-[200px] text-right leading-tight block">Motivo: {{ $venta->pago->motivo_rechazo }}</span>
                            @else
                                <p class="text-[11px] font-black text-[#343c4c] bg-[#dcb47c] px-4 py-1.5 rounded-md uppercase tracking-wider shadow-sm inline-block">
                                    ⏳ En verificación
                                </p>
                            @endif
                        </div>

                        @if($venta->pago->estado === 'verificado')
                            <a href="{{ route('cliente.comprobante', $venta->id) }}" class="inline-flex items-center px-4 py-2 bg-[#dcb47c] hover:bg-white text-[#343c4c] font-black uppercase tracking-widest text-[9px] rounded shadow-md transition-colors border border-transparent hover:border-[#dcb47c]">
                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Descargar Comprobante
                            </a>
                        @endif
                    </div>
                </div>

                <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-10">
                    
                    <div class="md:col-span-2 space-y-5">
                        <h4 class="text-sm font-black text-[#343c4c] uppercase tracking-widest border-b-2 border-[#f4f4f4] pb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                            Artículos Solicitados
                        </h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($venta->detalles as $det)
                                <div class="flex items-center space-x-4 bg-[#f4f4f4] p-3 rounded-xl border border-[#343c4c]/5 hover:border-[#dcb47c] transition-colors">
                                    @php 
                                        $fotos = json_decode($det->variante->producto->imagen_url, true) ?? [];
                                        $portada = count($fotos) > 0 ? $fotos[0] : null;
                                    @endphp
                                    <div class="w-20 h-20 bg-white rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden shadow-sm border border-white">
                                        @if($portada) 
                                            <img src="{{ asset('storage/' . $portada) }}" class="w-full h-full object-contain p-1"> 
                                        @else
                                            <svg class="w-8 h-8 text-[#343c4c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        @endif
                                    </div>
                                    <div class="flex-grow">
                                        <p class="text-xs font-bold text-[#343c4c] line-clamp-2 leading-tight mb-1" title="{{ $det->variante->producto->nombre }}">{{ $det->variante->producto->nombre }}</p>
                                        <p class="text-[10px] font-black text-[#343c4c]/50 uppercase tracking-wider mb-1">
                                            Cant: <span class="text-[#dc043c]">{{ $det->cantidad }}</span>
                                            @if($det->variante->talla) | Talla: <span class="text-[#0464a4]">{{ $det->variante->talla }}</span> @endif
                                            @if($det->variante->color) | Color: <span class="text-[#0464a4]">{{ $det->variante->color }}</span> @endif
                                        </p>
                                        <div class="flex items-center gap-3">
                                            <p class="text-sm font-black text-[#343c4c]">Bs {{ number_format($det->subtotal, 2) }}</p>
                                            @if($det->descuento_unitario > 0)
                                                <span class="text-[10px] font-black text-white bg-[#dc043c] px-2 py-0.5 rounded shadow-sm">
                                                    AHORRO: Bs {{ number_format($det->descuento_unitario * $det->cantidad, 2) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-[#0464a4]/5 border-2 border-[#0464a4]/20 p-6 rounded-2xl flex flex-col justify-center shadow-inner relative overflow-hidden">
                        <svg class="absolute -bottom-4 -right-4 w-32 h-32 text-[#0464a4]/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        
                        <div class="relative z-10">
                            <h4 class="text-[10px] font-black text-[#0464a4] uppercase tracking-widest mb-2">Estado Logístico</h4>
                            
                            @if($venta->orden)
                                <p class="text-xl font-black text-[#343c4c] uppercase tracking-wide mb-4">{{ $venta->orden->estado_orden }}</p>
                                
                                @if(in_array(strtolower($venta->orden->estado_orden), ['entregada', 'completada', 'completada / entregada']))
                                    <div class="bg-white p-4 rounded-xl border border-[#dcb47c]/50 text-center shadow-sm">
                                        <span class="text-2xl mb-2 block">⭐</span>
                                        <p class="text-[11px] text-[#343c4c] font-bold uppercase tracking-wider mb-3">¿Qué te parecieron los productos?</p>
                                        <a href="{{ route('producto.show', $venta->detalles->first()->variante->producto_id ?? 1) }}#resenas" class="inline-block bg-[#0464a4] hover:bg-[#dc043c] text-white font-black uppercase tracking-widest text-[9px] px-4 py-2 rounded transition-colors shadow-md">
                                            Agregar Reseña
                                        </a>
                                    </div>
                                @else
                                    @if($venta->orden->envio)
                                        <div class="text-xs text-[#343c4c]/80 space-y-2 font-medium">
                                            <p><strong class="text-[#343c4c] uppercase tracking-wider text-[10px]">Destino:</strong><br> {{ $venta->orden->envio->ciudad_destino }}</p>
                                            <p><strong class="text-[#343c4c] uppercase tracking-wider text-[10px]">Dirección:</strong><br> {{ $venta->orden->envio->direccion_destino }}</p>
                                            
                                            @if($venta->orden->envio->codigo_seguimiento)
                                                <div class="mt-4 bg-white p-3 rounded-xl border-2 border-dashed border-[#0464a4]/40 text-center shadow-sm">
                                                    <strong class="block text-[9px] font-black uppercase tracking-widest text-[#0464a4] mb-1">Guía / Tracking</strong>
                                                    <span class="text-lg font-black text-[#343c4c]">{{ $venta->orden->envio->codigo_seguimiento }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="bg-white p-4 rounded-xl border border-[#dcb47c]/50 text-center shadow-sm">
                                            <span class="text-2xl mb-1 block">🏪</span>
                                            <p class="text-xs text-[#343c4c] font-bold uppercase tracking-wider">Recojo en Tienda</p>
                                        </div>
                                    @endif

                                    @if(str_contains(strtolower($venta->orden->estado_orden), 'listo') || str_contains(strtolower($venta->orden->estado_orden), 'llegó') || str_contains(strtolower($venta->orden->estado_orden), 'llego'))
                                        <div class="mt-4 border-t-2 border-[#f4f4f4] pt-4">
                                            <form action="{{ route('cliente.pedidos.recibir', $venta->orden->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" onclick="confirmarRecepcion(this.form)" class="w-full bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest text-[10px] px-4 py-3 rounded-lg shadow-md transition-colors flex items-center justify-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                    Marcar como Recibido
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-16 text-center rounded-3xl border-2 border-dashed border-[#dcb47c] shadow-lg relative z-10">
                <svg class="w-20 h-20 mx-auto text-[#dcb47c] mb-4 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c]">Aún no tienes pedidos</h3>
                <p class="text-[#343c4c]/60 mt-2 font-medium">Agrega productos a tu carrito y finaliza el proceso de compra para verlos aquí.</p>
                <a href="{{ route('home') }}#catalogo" class="mt-6 inline-block bg-[#0464a4] hover:bg-[#dc043c] text-white font-black uppercase tracking-widest py-3 px-8 rounded-xl shadow-lg transition-colors text-sm">Explorar Catálogo</a>
            </div>
        @endforelse
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmarRecepcion(form) {
        Swal.fire({
            title: '¿Ya tienes tus productos?',
            text: "Al confirmar, la orden se marcará como Completada y Entregada.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0464a4',
            cancelButtonColor: '#343c4c',
            confirmButtonText: 'Sí, ya lo recibí',
            cancelButtonText: 'Volver'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            title: '¡Excelente!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#0464a4'
        });
    @endif
    @if(session('error'))
        Swal.fire({
            title: 'Error',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#dc043c'
        });
    @endif
</script>
@endsection