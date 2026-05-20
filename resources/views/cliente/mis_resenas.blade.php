@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mis Reseñas</h1>
            <p class="text-gray-500 text-sm mt-1">Historial de las opiniones y calificaciones que has compartido con la comunidad.</p>
        </div>
        <a href="{{ route('home') }}" class="text-blue-600 font-bold hover:underline text-sm">Volver a la tienda</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($resenas as $resena)
            @php 
                $fotos = json_decode($resena->producto->imagen_url, true) ?? [];
                $portada = count($fotos) > 0 ? $fotos[0] : null;
            @endphp

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col transition hover:shadow-md">
                
                <div class="p-5 flex items-start space-x-4">
                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden border">
                        @if($portada)
                            <img src="{{ asset('storage/' . $portada) }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @endif
                    </div>
                    
                    <div class="flex-grow">
                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">{{ $resena->producto->categoria->nombre ?? 'General' }}</span>
                        <h3 class="text-sm font-bold text-gray-900 line-clamp-1">{{ $resena->producto->nombre }}</h3>
                        
                        <div class="flex items-center mt-1">
                            <div class="flex text-yellow-400 text-xs">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-3.5 h-3.5 {{ $i <= $resena->calificacion ? 'fill-current' : 'text-gray-300 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <span class="text-[11px] text-gray-400 ml-2">{{ $resena->fecha_resena->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="px-5 pb-4 flex-grow">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 h-full">
                        <p class="text-sm text-gray-600 italic">
                            @if($resena->comentario)
                                "{{ Str::limit($resena->comentario, 150) }}"
                            @else
                                <span class="text-gray-400 font-medium not-italic">Solo dejaste una calificación por estrellas sin texto.</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="bg-gray-50 border-t p-3 flex space-x-2">
                    <a href="{{ route('producto.show', $resena->producto_id) }}" class="flex-1 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-2 rounded text-xs text-center transition shadow-sm">
                        Ver / Editar
                    </a>
                    
                    <form action="{{ route('resenas.destroy', $resena->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar esta reseña permanentemente?')" class="w-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold py-2 rounded text-xs transition shadow-sm">
                            Eliminar
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border shadow-sm flex flex-col items-center justify-center">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                <h3 class="text-xl font-bold text-gray-800">Aún no has opinado sobre ningún producto</h3>
                <p class="text-gray-500 mt-2">Tus reseñas ayudan a otros clientes a tomar mejores decisiones de compra.</p>
                <a href="{{ route('home') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition">Explorar Catálogo</a>
            </div>
        @endforelse
    </div>
</div>
@endsection