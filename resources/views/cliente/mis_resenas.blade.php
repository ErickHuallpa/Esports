@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-6">
    
    <!-- ENCABEZADO -->
    <div class="mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 border-b-2 border-[#f4f4f4] pb-4">
        <div>
            <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Mis Reseñas</h1>
            <p class="text-[#343c4c]/60 text-sm mt-1 font-medium">Historial de las opiniones y calificaciones que has compartido con la comunidad.</p>
        </div>
        <a href="{{ route('home') }}" class="text-[#0464a4] hover:text-[#dc043c] font-black uppercase tracking-widest text-xs transition-colors bg-[#f4f4f4] hover:bg-white px-4 py-2 rounded-lg shadow-sm border border-transparent hover:border-[#dc043c]/20">
            &larr; Volver a la tienda
        </a>
    </div>

    <!-- CUADRÍCULA DE RESEÑAS -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        @forelse($resenas as $resena)
            @php 
                $fotos = json_decode($resena->producto->imagen_url, true) ?? [];
                $portada = count($fotos) > 0 ? $fotos[0] : null;
            @endphp

            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#343c4c]/10 flex flex-col transition-all hover:-translate-y-1 hover:shadow-2xl group">
                
                <!-- Cabecera de la Tarjeta (Info Producto) -->
                <div class="p-6 flex items-start space-x-5 bg-gradient-to-br from-white to-[#f4f4f4]/50 border-b border-[#f4f4f4]">
                    
                    <div class="w-24 h-24 bg-white rounded-xl flex-shrink-0 flex items-center justify-center overflow-hidden border-2 border-[#f4f4f4] shadow-sm p-1 relative">
                        @if($portada)
                            <img src="{{ asset('storage/' . $portada) }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500">
                        @else
                            <svg class="w-10 h-10 text-[#343c4c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @endif
                    </div>
                    
                    <div class="flex-grow">
                        <span class="text-[10px] font-black text-[#dcb47c] uppercase tracking-widest">{{ $resena->producto->categoria->nombre ?? 'General' }}</span>
                        <h3 class="text-base font-black text-[#343c4c] line-clamp-2 leading-tight mt-1 group-hover:text-[#0464a4] transition-colors">{{ $resena->producto->nombre }}</h3>
                        
                        <div class="flex flex-col sm:flex-row sm:items-center mt-3 gap-2 sm:gap-0">
                            <!-- Estrellas -->
                            <div class="flex text-[#dcb47c] text-sm drop-shadow-sm">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $resena->calificacion ? 'fill-current' : 'text-[#343c4c]/10 fill-current' }}" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-[10px] font-bold text-[#343c4c]/40 sm:ml-3 uppercase tracking-wider">{{ $resena->fecha_resena->format('d M, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cuerpo de la Tarjeta (Comentario) -->
                <div class="px-6 py-5 flex-grow">
                    <div class="bg-[#f4f4f4]/60 p-4 rounded-xl border border-[#343c4c]/5 h-full relative">
                        <!-- Icono de comillas decorativo -->
                        <svg class="absolute top-2 left-2 w-6 h-6 text-[#343c4c]/5" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                        
                        <p class="text-sm text-[#343c4c]/80 italic relative z-10 pl-6 leading-relaxed font-medium">
                            @if($resena->comentario)
                                "{{ Str::limit($resena->comentario, 180) }}"
                            @else
                                <span class="text-[#343c4c]/40 font-bold not-italic">Solo dejaste una calificación por estrellas sin texto.</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Footer (Acciones) -->
                <div class="bg-[#343c4c] p-4 flex space-x-3 items-center">
                    <a href="{{ route('producto.show', $resena->producto_id) }}" class="flex-1 bg-[#0464a4] hover:bg-white text-white hover:text-[#0464a4] font-black uppercase tracking-widest py-3 rounded-lg text-[10px] sm:text-xs text-center transition-colors shadow-md border border-transparent hover:border-[#0464a4]">
                        Ver / Editar
                    </a>
                    
                    <form action="{{ route('resenas.destroy', $resena->id) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar esta reseña permanentemente?')" class="w-full bg-[#dc043c] hover:bg-white text-white hover:text-[#dc043c] font-black uppercase tracking-widest py-3 rounded-lg text-[10px] sm:text-xs text-center transition-colors shadow-md border border-transparent hover:border-[#dc043c]">
                            Eliminar
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="col-span-full bg-white p-16 text-center rounded-3xl border-2 border-dashed border-[#dcb47c] shadow-lg relative z-10">
                <svg class="w-20 h-20 mx-auto text-[#dcb47c] mb-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c]">Aún no has opinado sobre ningún artículo</h3>
                <p class="text-[#343c4c]/60 mt-2 font-medium">Tus reseñas ayudan a otros miembros de la comunidad a tomar mejores decisiones de compra.</p>
                <a href="{{ route('home') }}#catalogo" class="mt-8 inline-block bg-[#0464a4] hover:bg-[#dc043c] text-white font-black uppercase tracking-widest py-3 px-8 rounded-xl shadow-lg transition-colors text-sm">Explorar Catálogo</a>
            </div>
        @endforelse
    </div>
</div>
@endsection