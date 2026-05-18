@extends('layouts.app')

@section('content')
<div class="mb-10 text-center">
    <h1 class="text-4xl font-black text-gray-900 tracking-tight">Catálogo E-SPORTS</h1>
    <p class="text-lg text-gray-500 mt-2">Equípate con los mejores insumos deportivos y tecnológicos.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
    @forelse($productos as $prod)
        @php 
            $stockTotal = $prod->variantes->sum('stock'); 
            // Decodificamos el array JSON de imágenes
            $fotos = json_decode($prod->imagen_url, true) ?? [];
            $portada = count($fotos) > 0 ? $fotos[0] : null;
        @endphp
        
        <div class="group bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden flex flex-col">
            
            <div class="relative h-56 bg-gray-50 flex items-center justify-center overflow-hidden">
                @if($portada)
                    <img src="{{ asset('storage/' . $portada) }}" alt="{{ $prod->nombre }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="text-gray-300">
                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="absolute top-3 left-3">
                    @if($prod->agotado || $stockTotal <= 0)
                        <span class="px-2.5 py-1 bg-red-500 text-white text-xs font-bold rounded-lg shadow-sm">Agotado</span>
                    @else
                        <span class="px-2.5 py-1 bg-green-500 text-white text-xs font-bold rounded-lg shadow-sm">Disponible</span>
                    @endif
                </div>

                @if($prod->modelo_3d_url)
                    <div class="absolute top-3 right-3 bg-blue-600 text-white text-xs font-bold px-2.5 py-1 rounded-lg shadow-md flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"></path>
                        </svg>
                        3D Vista
                    </div>
                @endif
            </div>

            <div class="p-5 flex-grow flex flex-col justify-between">
                <div>
                    <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">
                        {{ $prod->categoria->nombre ?? 'General' }}
                    </span>
                    <h3 class="text-lg font-bold text-gray-800 mt-1 line-clamp-2 min-h-[3.5rem]">
                        {{ $prod->nombre }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-1">Marca: {{ $prod->marca ?? 'E-Sports' }}</p>
                </div>
                
                <div class="mt-5 flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-2xl font-black text-gray-900">Bs {{ number_format($prod->precio_venta, 2) }}</span>
                    </div>
                    <a href="{{ route('producto.show', $prod->id) }}" class="bg-gray-900 text-white text-xs px-4 py-2.5 rounded-xl font-bold hover:bg-blue-600 text-center transition-colors shadow">
                        Ver Producto
                    </a>
                </div>
            </div>
            
        </div>
    @empty
        <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <h2 class="text-xl font-bold text-gray-700">No hay productos disponibles</h2>
            <p class="text-gray-400 mt-1">Estamos actualizando nuestro inventario de variantes en este momento.</p>
        </div>
    @endforelse
</div>
@endsection