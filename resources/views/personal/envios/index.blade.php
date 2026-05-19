@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Gestión de Logística y Despachos</h2>
    <p class="text-gray-500 text-sm">Controla la salida de almacén de las órdenes previamente confirmadas por caja.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($envios as $envio)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between">
            <div class="p-5 border-b">
                <div class="flex justify-between items-start mb-2">
                    <span class="text-xs font-bold text-gray-500 uppercase">Orden #{{ $envio->orden_id }}</span>
                    @if($envio->estado_envio === 'preparando')
                        <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-full">Preparando</span>
                    @elseif($envio->estado_envio === 'en camino')
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full">En Camino</span>
                    @elseif($envio->estado_envio === 'entregado')
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">Entregado</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-full">Problema</span>
                    @endif
                </div>
                
                <h3 class="text-lg font-bold text-gray-800">{{ $envio->orden->venta->user->persona->nombre }} {{ $envio->orden->venta->user->persona->apellidos }}</h3>
                <p class="text-xs text-gray-500 font-semibold mb-3">Telf: {{ $envio->orden->venta->user->persona->telefono ?? 'Sin número' }}</p>
                
                <div class="bg-gray-50 p-3 rounded-lg border text-sm text-gray-700 space-y-1">
                    <p><strong>Destino:</strong> {{ $envio->ciudad_destino }} ({{ $envio->zona_destino ?? 'Sin zona' }})</p>
                    <p><strong>Dirección:</strong> {{ $envio->direccion_destino }}</p>
                </div>
            </div>

            <form action="{{ route('personal.envios.update', $envio->id) }}" method="POST" class="p-5 bg-gray-50 space-y-3">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase">Estado Operativo</label>
                    <select name="estado_envio" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm bg-white font-semibold focus:ring-purple-500">
                        <option value="preparando" {{ $envio->estado_envio == 'preparando' ? 'selected' : '' }}>Preparando Empaque</option>
                        <option value="en camino" {{ $envio->estado_envio == 'en camino' ? 'selected' : '' }}>En Camino / Despachado</option>
                        <option value="entregado" {{ $envio->estado_envio == 'entregado' ? 'selected' : '' }}>Entregado al Cliente</option>
                        <option value="fallido" {{ $envio->estado_envio == 'fallido' ? 'selected' : '' }}>Fallido / Devolución</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase">Guía / Seguimiento</label>
                    <input type="text" name="codigo_seguimiento" value="{{ $envio->codigo_seguimiento }}" placeholder="Ej: FLX-98234" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm bg-white focus:ring-purple-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase">Chofer / Empresa de Transporte</label>
                    <input type="text" name="responsable_entrega" value="{{ $envio->responsable_entrega }}" placeholder="Ej: Juan Perez o Trans. Potosí" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm bg-white focus:ring-purple-500">
                </div>

                <button type="submit" class="w-full mt-2 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded shadow transition">
                    Guardar Actualización
                </button>
            </form>
        </div>
    @empty
        <div class="col-span-full text-center bg-white p-10 border rounded-xl shadow-sm">
            <p class="text-gray-500 text-lg">No hay paquetes pendientes de envío en cola.</p>
        </div>
    @endforelse
</div>
@endsection