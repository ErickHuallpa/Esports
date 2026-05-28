@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-6">
    
    <!-- ENCABEZADO -->
    <div class="mb-8 flex flex-col gap-2 border-b-2 border-[#f4f4f4] pb-5">
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Logística y Despachos</h1>
        <p class="text-[#343c4c]/60 text-sm font-medium">Controla la salida de almacén de las órdenes previamente confirmadas y prepara su distribución.</p>
    </div>

    <!-- CUADRÍCULA DE ENVÍOS -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($envios as $envio)
            <div class="bg-white rounded-3xl border border-[#343c4c]/10 shadow-xl overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-1 hover:shadow-2xl">
                
                <!-- Cabecera de la Tarjeta (Ticket Style) -->
                <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#dcb47c] flex justify-between items-center">
                    <span class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-4 h-4 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        Orden #{{ $envio->orden_id }}
                    </span>
                    
                    @if($envio->estado_envio === 'preparando')
                        <span class="px-3 py-1.5 bg-[#dcb47c] text-[#343c4c] text-[9px] font-black rounded-md uppercase tracking-wider shadow-sm">Preparando</span>
                    @elseif($envio->estado_envio === 'en camino')
                        <span class="px-3 py-1.5 bg-[#0464a4] text-white text-[9px] font-black rounded-md uppercase tracking-wider shadow-sm">En Camino</span>
                    @elseif($envio->estado_envio === 'llego al destino')
                        <span class="px-3 py-1.5 bg-[#0464a4]/10 text-[#0464a4] text-[9px] font-black rounded-md uppercase tracking-wider border border-[#0464a4]/20">Llegó a Destino</span>
                    @elseif($envio->estado_envio === 'entregado')
                        <span class="px-3 py-1.5 bg-green-50 text-green-700 text-[9px] font-black rounded-md uppercase tracking-wider border border-green-200">Entregado</span>
                    @else
                        <span class="px-3 py-1.5 bg-[#dc043c] text-white text-[9px] font-black rounded-md uppercase tracking-wider shadow-sm">Problema</span>
                    @endif
                </div>
                
                <!-- Información del Cliente y Destino -->
                <div class="p-6">
                    <h3 class="text-xl font-black text-[#343c4c] uppercase tracking-wide leading-tight">{{ $envio->orden->venta->user->persona->nombre }} {{ $envio->orden->venta->user->persona->apellidos }}</h3>
                    
                    <p class="text-[11px] text-[#343c4c]/60 font-bold mb-5 uppercase tracking-widest flex items-center mt-1">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Telf: <span class="ml-1 text-[#343c4c]">{{ $envio->orden->venta->user->persona->telefono ?? 'No especificado' }}</span>
                    </p>
                    
                    <div class="bg-[#f4f4f4] p-4 rounded-2xl border border-[#343c4c]/5 text-sm space-y-3 relative overflow-hidden">
                        <!-- Icono decorativo de fondo -->
                        <svg class="absolute -bottom-2 -right-2 w-16 h-16 text-[#343c4c]/5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        
                        <div class="relative z-10">
                            <strong class="text-[9px] uppercase tracking-widest text-[#0464a4] block mb-1">📍 Destino</strong>
                            <p class="font-bold text-[#343c4c] leading-tight">{{ $envio->ciudad_destino }} <span class="text-[#343c4c]/50 text-xs ml-1 font-medium">({{ $envio->zona_destino ?? 'Sin zona' }})</span></p>
                        </div>
                        <div class="relative z-10">
                            <strong class="text-[9px] uppercase tracking-widest text-[#0464a4] block mb-1">🏠 Dirección</strong>
                            <p class="font-bold text-[#343c4c] leading-tight">{{ $envio->direccion_destino }}</p>
                        </div>
                    </div>
                </div>

                <!-- Formulario de Control Logístico -->
                <form action="{{ route('personal.envios.update', $envio->id) }}" method="POST" class="px-6 py-6 bg-[#f4f4f4]/50 border-t border-[#f4f4f4] space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Estado Operativo</label>
                        <select name="estado_envio" class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer shadow-sm">
                            <option value="preparando" {{ $envio->estado_envio == 'preparando' ? 'selected' : '' }}>📦 Preparando Empaque</option>
                            <option value="en camino" {{ $envio->estado_envio == 'en camino' ? 'selected' : '' }}>🚚 En Camino / Despachado</option>
                            <option value="llego al destino" {{ $envio->estado_envio == 'llego al destino' ? 'selected' : '' }}>🏢 Llegó al Destino (Agencia)</option>
                            <option value="entregado" {{ $envio->estado_envio == 'entregado' ? 'selected' : '' }}>✅ Entregado al Cliente Final</option>
                            <option value="fallido" {{ $envio->estado_envio == 'fallido' ? 'selected' : '' }}>❌ Fallido / Devolución</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Guía / Seguimiento</label>
                        <input type="text" name="codigo_seguimiento" value="{{ $envio->codigo_seguimiento }}" placeholder="Ej: FLX-98234" 
                            class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Chofer / Empresa de Transporte</label>
                        <input type="text" name="responsable_entrega" value="{{ $envio->responsable_entrega }}" placeholder="Ej: Trans. Potosí o Chofer Carlos" 
                            class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] shadow-sm">
                    </div>

                    <button type="submit" class="w-full mt-2 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3.5 rounded-xl shadow-md transition-all text-xs transform hover:-translate-y-0.5">
                        Actualizar Envío
                    </button>
                </form>
            </div>
        @empty
            <div class="col-span-full bg-white p-16 text-center rounded-3xl border-2 border-dashed border-[#dcb47c] shadow-lg relative z-10">
                <svg class="w-24 h-24 mx-auto text-[#dcb47c] mb-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c] uppercase tracking-wide">Despachos al Día</h3>
                <p class="text-[#343c4c]/60 mt-2 font-medium">No hay paquetes pendientes de logística en este momento.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection