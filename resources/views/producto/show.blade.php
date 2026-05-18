@extends('layouts.app')

@section('content')
@if($producto->modelo_3d_url)
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>
@endif

<div class="mb-4">
    <a href="{{ route('home') }}" class="text-blue-600 hover:text-blue-800 flex items-center font-medium text-sm transition">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Volver al catálogo
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2">
        
        <div class="bg-gray-50 min-h-[400px] md:min-h-[500px] flex flex-col justify-between p-6 relative">
            <div class="flex-grow flex items-center justify-center w-full h-full max-h-[450px]">
                @if($producto->modelo_3d_url)
                    <model-viewer 
                        src="{{ asset('storage/' . $producto->modelo_3d_url) }}" 
                        auto-rotate camera-controls shadow-intensity="1"
                        class="w-full h-full min-h-[350px]" alt="Modelo 3D Interactivo">
                    </model-viewer>
                    <div class="absolute bottom-16 left-6 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full text-[11px] font-semibold text-gray-700 shadow">
                        🖱️ Arrastra para rotar el modelo en 3D
                    </div>
                @else
                    @php $fotos = json_decode($producto->imagen_url, true) ?? []; @endphp
                    @if(count($fotos) > 0)
                        <img id="mainDisplayImage" src="{{ asset('storage/' . $fotos[0]) }}" alt="" class="w-full h-full object-contain max-h-[380px] rounded-lg shadow-sm">
                    @else
                        <div class="text-gray-400 text-center">
                            <svg class="w-20 h-20 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-sm">Imagen de muestra no disponible</p>
                        </div>
                    @endif
                @endif
            </div>

            @php $fotos = json_decode($producto->imagen_url, true) ?? []; @endphp
            @if(count($fotos) > 1)
                <div class="flex space-x-2 overflow-x-auto pt-4 border-t border-gray-200/60">
                    @foreach($fotos as $index => $foto)
                        <button onclick="cambiarImagen('{{ asset('storage/' . $foto) }}')" class="w-14 h-14 rounded-md border bg-white overflow-hidden flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <img src="{{ asset('storage/' . $foto) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="p-8 lg:p-12 flex flex-col justify-center bg-white border-l border-gray-50">
            <span class="text-xs font-bold text-blue-600 tracking-widest uppercase">{{ $producto->categoria->nombre ?? 'General' }}</span>
            <h1 class="text-3xl font-extrabold text-gray-900 mt-1 mb-3 leading-tight">{{ $producto->nombre }}</h1>
            
            <div class="mb-4">
                <span class="text-3xl font-black text-gray-900">Bs {{ number_format($producto->precio_venta, 2) }}</span>
            </div>

            <p class="text-sm text-gray-600 mb-6 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                {{ $producto->descripcion ?? 'Este insumo deportivo no cuenta con una descripción detallada por el momento.' }}
            </p>

            @auth
                <form action="{{ route('carrito.agregar') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="producto_variante_id" class="block text-xs font-bold text-gray-700 uppercase mb-2">Seleccionar Combinación (Talla / Color) *</label>
                        <select id="producto_variante_id" name="producto_variante_id" required class="w-full bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-3 bg-white">
                            <option value="" disabled selected>Elige la opción que necesitas...</option>
                            @foreach($producto->variantes as $v)
                                @if($v->stock > 0)
                                    <option value="{{ $v->id }}">
                                        @if($v->talla) Talla: {{ $v->talla }} @endif
                                        @if($v->color) | Color: {{ $v->color }} @endif
                                        (Disponibles: {{ $v->stock }} un.)
                                    </option>
                                @else
                                    <option value="{{ $v->id }}" disabled class="text-red-400 bg-red-50">
                                        @if($v->talla) Talla: {{ $v->talla }} @endif 
                                        @if($v->color) | Color: {{ $v->color }} @endif 
                                        (Agotado)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 px-8 rounded-xl shadow-lg flex justify-center items-center transition transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Agregar al Carrito de Compras
                        </button>
                    </div>
                </form>
            @else
                <div class="bg-amber-50 border-l-4 border-amber-500 text-amber-800 p-4 rounded-r-xl shadow-sm">
                    <p class="text-sm font-semibold">Debes iniciar sesión para poder comprar este artículo.</p>
                    <a href="{{ route('login') }}" class="mt-2 inline-block bg-amber-600 text-white text-xs font-bold py-2 px-4 rounded-lg hover:bg-amber-700 transition">
                        Ir al Login ahora
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<script>
    // Cambia la foto principal del visor al interactuar con el carrusel secundario
    function cambiarImagen(ruta) {
        const img = document.getElementById('mainDisplayImage');
        if(img) img.src = ruta;
    }
</script>
@endsection