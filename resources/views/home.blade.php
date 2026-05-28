@extends('layouts.app')

@section('content')

<div class="relative w-full h-[85vh] bg-[#343c4c] overflow-hidden rounded-b-3xl shadow-2xl -mt-8 mb-12">
    
    @foreach($destacados as $index => $dest)
        @php 
            $fotosDest = json_decode($dest->imagen_url, true) ?? [];
            $portadaDest = count($fotosDest) > 0 ? asset('storage/' . $fotosDest[0]) : 'https://via.placeholder.com/1200x800.png?text=Sin+Imagen';
            
            $ofertaDest = $dest->ofertas->first();
            $precioDest = $ofertaDest 
                ? $dest->precio_venta - ($dest->precio_venta * ($ofertaDest->porcentaje_descuento / 100)) 
                : $dest->precio_venta;
        @endphp

        <div id="slide-{{ $index }}" class="hero-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index == 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' }}">
            <img src="{{ $portadaDest }}" alt="{{ $dest->nombre }}" class="absolute inset-0 w-full h-full object-cover object-center opacity-60">
            
            <div class="absolute inset-0 bg-gradient-to-r from-[#343c4c]/95 via-[#343c4c]/70 to-transparent"></div>

            <div class="relative z-20 h-full flex flex-col justify-center px-8 md:px-20 max-w-4xl">
                <span class="inline-block text-[#dcb47c] font-black tracking-widest uppercase text-sm mb-2 drop-shadow-md">
                    @if($ofertaDest)
                        🔥 OFERTA ESTRELLA: -{{ $ofertaDest->porcentaje_descuento }}%
                    @else
                        ⭐ {{ $dest->categoria->nombre ?? 'Novedad' }}
                    @endif
                </span>
                <h2 class="text-4xl md:text-6xl font-black text-[#f4f4f4] leading-tight mb-4 drop-shadow-lg">
                    {{ $dest->nombre }}
                </h2>
                <p class="text-lg text-[#f4f4f4]/80 mb-8 line-clamp-3">
                    {{ $dest->descripcion ?? 'Descubre el rendimiento y la calidad que este artículo aportará a tu configuración deportiva o tecnológica.' }}
                </p>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('producto.show', $dest->id) }}" class="bg-[#0464a4] hover:bg-[#dc043c] text-white px-8 py-3.5 rounded-full font-bold shadow-lg shadow-[#0464a4]/30 transition-all transform hover:-translate-y-1">
                        VER DETALLES
                    </a>
                    <div class="flex flex-col">
                        @if($ofertaDest)
                            <span class="text-sm font-bold text-[#f4f4f4]/50 line-through">Bs {{ number_format($dest->precio_venta, 2) }}</span>
                        @endif
                        <span class="text-3xl font-black text-[#dcb47c] leading-none drop-shadow-md">
                            Bs {{ number_format($precioDest, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div class="absolute bottom-8 right-8 z-30 flex flex-col items-end hidden md:flex">
        <div class="flex space-x-3 mb-4">
            <button onclick="prevSlide()" class="w-10 h-10 rounded-full bg-[#f4f4f4]/20 hover:bg-[#dcb47c] hover:text-[#343c4c] text-[#f4f4f4] flex items-center justify-center backdrop-blur transition border border-[#f4f4f4]/30 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button onclick="nextSlide()" class="w-10 h-10 rounded-full bg-[#f4f4f4]/20 hover:bg-[#dcb47c] hover:text-[#343c4c] text-[#f4f4f4] flex items-center justify-center backdrop-blur transition border border-[#f4f4f4]/30 shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
            </button>
        </div>
        
        <div class="flex space-x-3">
            @foreach($destacados as $index => $dest)
                @php 
                    $fotosThumb = json_decode($dest->imagen_url, true) ?? [];
                    $thumb = count($fotosThumb) > 0 ? asset('storage/' . $fotosThumb[0]) : 'https://via.placeholder.com/150';
                @endphp
                <button onclick="goToSlide({{ $index }})" id="thumb-{{ $index }}" class="hero-thumb w-24 h-16 rounded-lg overflow-hidden border-2 transition-all duration-300 {{ $index == 0 ? 'border-[#dcb47c] scale-110 shadow-lg shadow-[#dcb47c]/40' : 'border-transparent opacity-60 hover:opacity-100' }}">
                    <img src="{{ $thumb }}" class="w-full h-full object-cover">
                </button>
            @endforeach
        </div>
    </div>

    <a href="#catalogo" class="absolute bottom-6 left-1/2 transform -translate-x-1/2 z-20 flex flex-col items-center animate-bounce text-[#dcb47c] hover:text-[#f4f4f4] transition cursor-pointer">
        <span class="text-xs font-bold uppercase tracking-widest mb-1">Catálogo</span>
        <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 14-4-4m4 4 4-4"/>
        </svg>
    </a>
</div>

<div id="catalogo" class="relative w-full bg-[#f4f4f4] py-16 min-h-screen overflow-hidden">
    
    <div class="absolute inset-x-0 bottom-0 w-full h-[250px] md:h-[400px] pointer-events-none z-0" 
         style="background-image: url('{{ asset('img/cesped.png') }}'); background-position: bottom center; background-repeat: repeat-x; background-size: auto 100%; opacity: 1;">
    </div>

    <div class="relative z-10 container mx-auto px-6 max-w-[1400px] pb-32">
        
        <div class="text-center mb-10">
            <h1 class="text-4xl font-black text-[#0464a4] tracking-tight drop-shadow-sm">Todo Nuestro Catálogo</h1>
            <p class="text-lg text-[#343c4c] mt-2 font-medium">Encuentra los mejores productos y ofertas actuales.</p>
        </div>

        <form method="GET" action="{{ route('home') }}#catalogo" class="bg-[#343c4c] p-5 rounded-2xl shadow-xl border-b-4 border-[#dcb47c] flex flex-col md:flex-row gap-4 items-center mb-12">
            
            <div class="w-full md:w-2/5 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o marca..." class="w-full pl-10 pr-4 py-3 rounded-xl border-none focus:ring-4 focus:ring-[#0464a4] text-sm text-[#343c4c] bg-white shadow-inner">
            </div>

            <div class="w-full md:w-1/5">
                <select name="categoria_id" class="w-full py-3 px-4 rounded-xl border-none focus:ring-4 focus:ring-[#0464a4] text-sm text-[#343c4c] bg-white shadow-inner font-semibold cursor-pointer">
                    <option value="">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-1/5">
                <select name="sort" class="w-full py-3 px-4 rounded-xl border-none focus:ring-4 focus:ring-[#0464a4] text-sm text-[#343c4c] bg-white shadow-inner font-semibold cursor-pointer">
                    <option value="nuevos" {{ request('sort') == 'nuevos' ? 'selected' : '' }}>Últimos Agregados</option>
                    <option value="precio_asc" {{ request('sort') == 'precio_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                    <option value="precio_desc" {{ request('sort') == 'precio_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                    <option value="antiguos" {{ request('sort') == 'antiguos' ? 'selected' : '' }}>Más Antiguos</option>
                </select>
            </div>

            <div class="w-full md:w-1/5 flex space-x-2">
                <button type="submit" class="w-full bg-[#0464a4] hover:bg-[#dc043c] text-white font-black tracking-wider uppercase py-3 rounded-xl text-sm transition-colors shadow-lg">
                    Filtrar
                </button>
                @if(request()->hasAny(['search', 'categoria_id', 'sort']))
                    <a href="{{ route('home') }}#catalogo" class="w-1/3 bg-[#dcb47c] hover:bg-white text-[#343c4c] flex items-center justify-center font-bold py-3 rounded-xl text-sm transition-colors shadow-lg" title="Limpiar Filtros">
                        ✖
                    </a>
                @endif
            </div>
        </form>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @forelse($productos as $prod)
                @php 
                    $stockTotal = $prod->variantes->sum('stock'); 
                    $fotos = json_decode($prod->imagen_url, true) ?? [];
                    $portada = count($fotos) > 0 ? asset('storage/' . $fotos[0]) : null;
                    
                    // Lógica de Ofertas
                    $ofertaActiva = $prod->ofertas->first();
                    $precioMostrar = $prod->precio_venta;
                    $precioOriginal = null;

                    if ($ofertaActiva) {
                        $precioOriginal = $prod->precio_venta;
                        $descuentoMonto = $precioOriginal * ($ofertaActiva->porcentaje_descuento / 100);
                        $precioMostrar = $precioOriginal - $descuentoMonto;
                    }
                @endphp
                
                <a href="{{ route('producto.show', $prod->id) }}" class="group relative bg-[#343c4c] rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 h-[380px] flex flex-col justify-end border-b-4 border-transparent hover:border-[#dcb47c] z-10">
                    
                    @if($portada)
                        <img src="{{ $portada }}" alt="{{ $prod->nombre }}" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-80 group-hover:opacity-100 bg-white">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center bg-white opacity-80">
                            <svg class="w-16 h-16 text-[#343c4c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif

                    <div class="absolute inset-0 bg-gradient-to-t from-[#000000] via-[#343c4c]/60 to-transparent opacity-80 group-hover:opacity-95 transition-opacity duration-500"></div>

                    <div class="absolute top-4 left-4 z-20 flex flex-col gap-2 items-start">
                        @if($ofertaActiva)
                            <span class="bg-[#dc043c] text-white text-[10px] font-black px-2.5 py-1 rounded shadow-md uppercase tracking-widest border border-[#dc043c]/50">
                                -{{ $ofertaActiva->porcentaje_descuento }}% OFF
                            </span>
                        @endif
                        @if($prod->agotado || $stockTotal <= 0)
                            <span class="bg-black/70 backdrop-blur text-white text-[9px] font-black px-2.5 py-1 rounded shadow-md uppercase tracking-widest border border-white/20">
                                Agotado
                            </span>
                        @endif
                    </div>

                    <div class="relative z-20 p-5 transform translate-y-6 group-hover:translate-y-0 transition-transform duration-300">
                        <span class="text-[9px] font-black text-[#dcb47c] uppercase tracking-widest drop-shadow-md">
                            {{ $prod->categoria->nombre ?? 'General' }}
                        </span>
                        
                        <h3 class="text-base font-black text-white leading-tight mt-1 line-clamp-2 drop-shadow-md group-hover:text-[#f4f4f4]">
                            {{ $prod->nombre }}
                        </h3>

                        <div class="flex items-end justify-between mt-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 delay-100">
                            <div class="flex flex-col">
                                @if($precioOriginal)
                                    <span class="text-[10px] text-white/60 line-through font-bold">Bs {{ number_format($precioOriginal, 2) }}</span>
                                @endif
                                <span class="text-xl font-black text-[#dcb47c] drop-shadow-md">Bs {{ number_format($precioMostrar, 2) }}</span>
                            </div>
                            
                            <div class="w-10 h-10 bg-[#0464a4] rounded-full flex items-center justify-center text-white shadow-lg border border-white/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-2xl border-2 border-dashed border-[#dcb47c] shadow-lg relative z-10">
                    <svg class="w-20 h-20 mx-auto text-[#dcb47c] mb-4 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <h2 class="text-2xl font-black text-[#343c4c]">No se encontraron resultados</h2>
                    <p class="text-[#343c4c]/70 mt-2 font-medium">Prueba eliminando los filtros de búsqueda actual o explorando otra categoría.</p>
                    <a href="{{ route('home') }}#catalogo" class="mt-6 inline-block bg-[#0464a4] hover:bg-[#dc043c] text-white px-8 py-3 rounded-xl font-black text-sm uppercase tracking-wider shadow-lg transition-colors">Ver Todo el Catálogo</a>
                </div>
            @endforelse
        </div>

        @if($productos->hasPages())
            <div class="mt-16 flex justify-center items-center space-x-6 relative z-10">
                
                @if($productos->onFirstPage())
                    <span class="px-6 py-3.5 bg-white border-2 border-white/50 text-[#343c4c]/40 font-black uppercase tracking-widest rounded-xl text-xs cursor-not-allowed shadow-sm">
                        &larr; Anterior
                    </span>
                @else
                    <a href="{{ $productos->appends(request()->query())->previousPageUrl() }}#catalogo" class="px-6 py-3.5 bg-[#343c4c] hover:bg-[#0464a4] text-white font-black uppercase tracking-widest rounded-xl text-xs shadow-lg transition-colors transform hover:-translate-y-1">
                        &larr; Anterior
                    </a>
                @endif

                <span class="text-[#343c4c] font-black text-sm bg-white px-4 py-2 rounded-lg shadow-sm border border-[#343c4c]/5">
                    Página {{ $productos->currentPage() }} de {{ $productos->lastPage() }}
                </span>

                @if($productos->hasMorePages())
                    <a href="{{ $productos->appends(request()->query())->nextPageUrl() }}#catalogo" class="px-6 py-3.5 bg-[#343c4c] hover:bg-[#0464a4] text-white font-black uppercase tracking-widest rounded-xl text-xs shadow-lg transition-colors transform hover:-translate-y-1">
                        Siguiente &rarr;
                    </a>
                @else
                    <span class="px-6 py-3.5 bg-white border-2 border-white/50 text-[#343c4c]/40 font-black uppercase tracking-widest rounded-xl text-xs cursor-not-allowed shadow-sm">
                        Siguiente &rarr;
                    </span>
                @endif
                
            </div>
        @endif

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const thumbs = document.querySelectorAll('.hero-thumb');
        
        if(slides.length === 0) return;

        window.goToSlide = function(index) {
            slides[currentSlide].classList.remove('opacity-100', 'z-10');
            slides[currentSlide].classList.add('opacity-0', 'z-0');
            
            if(thumbs[currentSlide]) {
                thumbs[currentSlide].classList.remove('border-[#dcb47c]', 'scale-110', 'shadow-lg', 'shadow-[#dcb47c]/40', 'opacity-100');
                thumbs[currentSlide].classList.add('border-transparent', 'opacity-60');
            }

            currentSlide = index;
            if(currentSlide >= slides.length) currentSlide = 0;
            if(currentSlide < 0) currentSlide = slides.length - 1;

            slides[currentSlide].classList.remove('opacity-0', 'z-0');
            slides[currentSlide].classList.add('opacity-100', 'z-10');
            
            if(thumbs[currentSlide]) {
                thumbs[currentSlide].classList.remove('border-transparent', 'opacity-60');
                thumbs[currentSlide].classList.add('border-[#dcb47c]', 'scale-110', 'shadow-lg', 'shadow-[#dcb47c]/40', 'opacity-100');
            }
        };

        window.nextSlide = function() { goToSlide(currentSlide + 1); };
        window.prevSlide = function() { goToSlide(currentSlide - 1); };

        setInterval(nextSlide, 5000);
    });
</script>
@endsection