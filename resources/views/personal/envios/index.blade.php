@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-6">
    
    <div class="mb-8 flex flex-col gap-2 border-b-2 border-[#f4f4f4] pb-5">
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Logística y Despachos</h1>
        <p class="text-[#343c4c]/60 text-sm font-medium">Gestiona la salida de almacén de todas las órdenes confirmadas, incluyendo envíos a domicilio y recojos en tienda local.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($ordenes as $orden)
            <div class="bg-white rounded-3xl border border-[#343c4c]/10 shadow-xl overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-1 hover:shadow-2xl">
                
                <div class="bg-[#343c4c] px-6 py-4 border-b-4 {{ $orden->envio ? 'border-[#0464a4]' : 'border-[#dcb47c]' }} flex justify-between items-center">
                    <span class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2 {{ $orden->envio ? 'text-[#0464a4]' : 'text-[#dcb47c]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        Orden #{{ $orden->id }}
                    </span>
                    
                    @php $estado = $orden->estado_orden; @endphp
                    @if(str_contains($estado, 'Preparando'))
                        <span class="px-3 py-1.5 bg-[#dcb47c] text-[#343c4c] text-[9px] font-black rounded-md uppercase tracking-wider shadow-sm">Preparando</span>
                    @elseif(str_contains($estado, 'Tránsito'))
                        <span class="px-3 py-1.5 bg-[#0464a4] text-white text-[9px] font-black rounded-md uppercase tracking-wider shadow-sm">En Camino</span>
                    @elseif(str_contains($estado, 'Listo para Recojo'))
                        <span class="px-3 py-1.5 bg-purple-100 text-purple-800 text-[9px] font-black rounded-md uppercase tracking-wider border border-purple-200">Listo (Tienda)</span>
                    @elseif(str_contains($estado, 'Llegó'))
                        <span class="px-3 py-1.5 bg-[#0464a4]/10 text-[#0464a4] text-[9px] font-black rounded-md uppercase tracking-wider border border-[#0464a4]/20">En Agencia</span>
                    @elseif(str_contains($estado, 'Completada'))
                        <span class="px-3 py-1.5 bg-green-50 text-green-700 text-[9px] font-black rounded-md uppercase tracking-wider border border-green-200">Entregado</span>
                    @elseif(str_contains($estado, 'Problema'))
                        <span class="px-3 py-1.5 bg-[#dc043c] text-white text-[9px] font-black rounded-md uppercase tracking-wider shadow-sm">Problema</span>
                    @else
                        <span class="px-3 py-1.5 bg-[#f4f4f4] text-[#343c4c] text-[9px] font-black rounded-md uppercase tracking-wider border border-[#343c4c]/20">{{ $estado ?? 'Pendiente' }}</span>
                    @endif
                </div>
                
                <div class="p-6 relative">
                    @if($orden->envio)
                        <svg class="absolute top-4 right-4 w-12 h-12 text-[#0464a4]/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    @else
                        <svg class="absolute top-4 right-4 w-12 h-12 text-[#dcb47c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    @endif

                    <h3 class="text-xl font-black text-[#343c4c] uppercase tracking-wide leading-tight relative z-10">{{ $orden->venta->user->persona->nombre }} {{ $orden->venta->user->persona->apellidos }}</h3>
                    
                    <p class="text-[11px] text-[#343c4c]/60 font-bold mb-5 uppercase tracking-widest flex items-center mt-1 relative z-10">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Telf: <span class="ml-1 text-[#343c4c]">{{ $orden->venta->user->persona->telefono ?? 'No especificado' }}</span>
                    </p>
                    
                    @if($orden->envio)
                        <div class="bg-[#f4f4f4] p-4 rounded-2xl border border-[#343c4c]/5 text-sm space-y-3">
                            <span class="inline-block px-2 py-1 bg-[#0464a4] text-white text-[9px] font-black uppercase tracking-widest rounded mb-1">🚚 Requiere Envío</span>
                            <div>
                                <strong class="text-[9px] uppercase tracking-widest text-[#0464a4] block mb-1">📍 Destino</strong>
                                <p class="font-bold text-[#343c4c] leading-tight">{{ $orden->envio->ciudad_destino }} <span class="text-[#343c4c]/50 text-xs ml-1 font-medium">({{ $orden->envio->zona_destino ?? 'Sin zona' }})</span></p>
                            </div>
                            <div>
                                <strong class="text-[9px] uppercase tracking-widest text-[#0464a4] block mb-1">🏠 Dirección Exacta</strong>
                                <p class="font-bold text-[#343c4c] leading-tight">{{ $orden->envio->direccion_destino }}</p>
                            </div>
                        </div>
                    @else
                        <div class="bg-[#dcb47c]/10 p-4 rounded-2xl border border-[#dcb47c]/30 text-sm space-y-2 text-center h-[130px] flex flex-col justify-center items-center">
                            <span class="inline-block px-2 py-1 bg-[#343c4c] text-[#dcb47c] text-[9px] font-black uppercase tracking-widest rounded mb-2 shadow-sm">🏪 Retiro Local</span>
                            <p class="font-black text-[#343c4c] leading-tight uppercase">El cliente pasará a recoger su pedido por la Tienda en Potosí.</p>
                        </div>
                    @endif
                </div>

                <form action="{{ route('personal.envios.update', $orden->id) }}" method="POST" class="px-6 py-6 bg-[#f4f4f4]/50 border-t border-[#f4f4f4] space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Actualizar Estado</label>
                        <select name="estado_logistico" required class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer shadow-sm">
                            <option value="preparando" {{ str_contains($estado, 'Preparando') ? 'selected' : '' }}>📦 Preparando Empaque</option>
                            
                            @if($orden->envio)
                                <option value="en_camino" {{ str_contains($estado, 'Tránsito') ? 'selected' : '' }}>🚚 En Camino / Despachado</option>
                                <option value="llego_destino" {{ str_contains($estado, 'Llegó') ? 'selected' : '' }}>🏢 Llegó al Destino (Agencia)</option>
                            @else
                                <option value="listo_tienda" {{ str_contains($estado, 'Listo') ? 'selected' : '' }}>🏪 Listo para Recojo en Tienda</option>
                            @endif
                            
                            <option value="entregado" {{ str_contains($estado, 'Entregada') ? 'selected' : '' }}>✅ Completado / Entregado</option>
                            <option value="fallido" {{ str_contains($estado, 'Problema') ? 'selected' : '' }}>❌ Fallido / Retenido</option>
                        </select>
                    </div>

                    @if($orden->envio)
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Guía / Seguimiento</label>
                            <input type="text" name="codigo_seguimiento" value="{{ $orden->envio->codigo_seguimiento }}" placeholder="Ej: FLX-98234" 
                                class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] shadow-sm">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Chofer / Transporte</label>
                            <input type="text" name="responsable_entrega" value="{{ $orden->envio->responsable_entrega }}" placeholder="Ej: Trans. Potosí o Chofer Carlos" 
                                class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] shadow-sm">
                        </div>
                    @endif

                    <button type="submit" class="w-full mt-2 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3.5 rounded-xl shadow-md transition-all text-xs transform hover:-translate-y-0.5">
                        Guardar Actualización
                    </button>
                </form>
            </div>
        @empty
            <div class="col-span-full bg-white p-16 text-center rounded-3xl border-2 border-dashed border-[#dcb47c] shadow-lg relative z-10">
                <svg class="w-24 h-24 mx-auto text-[#dcb47c] mb-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c] uppercase tracking-wide">Órdenes al Día</h3>
                <p class="text-[#343c4c]/60 mt-2 font-medium">No hay pedidos confirmados que requieran tu atención en este momento.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection