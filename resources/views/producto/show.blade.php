@extends('layouts.app')

@section('content')
@if($producto->modelo_3d_url)
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>
@endif

<div class="mb-5 max-w-7xl mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center text-[#343c4c]/60 hover:text-[#dc043c] font-black uppercase tracking-widest text-[10px] transition-colors bg-white px-4 py-2 rounded-lg shadow-sm border border-transparent hover:border-[#dc043c]/20">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Volver al catálogo
    </a>
</div>

<div class="bg-white rounded-3xl shadow-xl border border-[#343c4c]/10 overflow-hidden mb-12 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2">
        
        <div class="bg-[#f4f4f4] min-h-[400px] lg:min-h-[600px] flex flex-col justify-between p-6 relative">
            <div class="flex-grow flex items-center justify-center w-full h-full max-h-[500px] relative z-10">
                @if($producto->modelo_3d_url)
                    <model-viewer 
                        src="{{ asset('storage/' . $producto->modelo_3d_url) }}" 
                        auto-rotate camera-controls shadow-intensity="1"
                        class="w-full h-full min-h-[400px] drop-shadow-xl" alt="Modelo 3D Interactivo">
                    </model-viewer>
                    <div class="absolute bottom-6 left-6 bg-[#343c4c] px-4 py-2 rounded-xl shadow-lg border border-white/10 flex items-center">
                        <span class="text-xl mr-2">🖱️</span>
                        <span class="text-[10px] font-black text-white uppercase tracking-widest leading-tight">Arrastra para<br>rotar 3D</span>
                    </div>
                @else
                    @php $fotos = json_decode($producto->imagen_url, true) ?? []; @endphp
                    @if(count($fotos) > 0)
                        <img id="mainDisplayImage" src="{{ asset('storage/' . $fotos[0]) }}" alt="{{ $producto->nombre }}" class="w-full h-full object-contain max-h-[450px] drop-shadow-lg transition-all duration-300">
                    @else
                        <div class="text-[#343c4c]/30 text-center">
                            <svg class="w-24 h-24 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-sm font-bold uppercase tracking-widest">Imagen no disponible</p>
                        </div>
                    @endif
                @endif
            </div>

            @php $fotos = json_decode($producto->imagen_url, true) ?? []; @endphp
            @if(count($fotos) > 1)
                <div class="flex space-x-3 overflow-x-auto pt-6 border-t-2 border-[#343c4c]/5 mt-4 custom-scrollbar pb-2 relative z-20">
                    @foreach($fotos as $index => $foto)
                        <button onclick="cambiarImagen('{{ asset('storage/' . $foto) }}')" class="w-16 h-16 rounded-xl border-2 border-transparent bg-white overflow-hidden flex-shrink-0 hover:border-[#0464a4] focus:outline-none focus:border-[#0464a4] transition-colors shadow-sm">
                            <img src="{{ asset('storage/' . $foto) }}" class="w-full h-full object-contain p-1">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="p-8 lg:p-12 flex flex-col justify-center bg-white">
            
            <div class="mb-6">
                <span class="inline-block px-3 py-1 bg-[#dcb47c]/20 text-[#343c4c] text-[10px] font-black uppercase tracking-widest rounded-md mb-3 border border-[#dcb47c]/50">
                    {{ $producto->categoria->nombre ?? 'General' }}
                </span>
                <h1 class="text-3xl lg:text-4xl font-black text-[#343c4c] leading-tight mb-4 uppercase tracking-tight">{{ $producto->nombre }}</h1>
                
                <div class="flex items-end gap-4 mb-6">
                    <span class="text-4xl font-black text-[#dc043c] drop-shadow-sm">Bs {{ number_format($producto->precio_venta, 2) }}</span>
                </div>
            </div>

            <div class="bg-[#f4f4f4] p-5 rounded-2xl border border-[#343c4c]/5 mb-8">
                <h4 class="text-[10px] font-black text-[#343c4c]/60 uppercase tracking-widest mb-2 flex items-center">
                    <svg class="w-4 h-4 mr-1 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Acerca del producto
                </h4>
                <p class="text-sm text-[#343c4c] leading-relaxed font-medium">
                    {{ $producto->descripcion ?? 'Este insumo no cuenta con una descripción detallada por el momento.' }}
                </p>
            </div>

            @auth
                <form action="{{ route('carrito.agregar') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="producto_variante_id" class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">Seleccionar Combinación (Talla / Color) *</label>
                        <select id="producto_variante_id" name="producto_variante_id" required class="w-full bg-white border-2 border-[#f4f4f4] text-[#343c4c] text-sm font-bold rounded-xl focus:ring-4 focus:ring-[#0464a4]/20 focus:border-[#0464a4] block p-4 shadow-sm cursor-pointer transition-colors">
                            <option value="" disabled selected>Elige la opción que necesitas...</option>
                            @foreach($producto->variantes as $v)
                                @if($v->stock > 0)
                                    <option value="{{ $v->id }}">
                                        @if($v->talla) Talla: {{ $v->talla }} @endif
                                        @if($v->color) | Color: {{ $v->color }} @endif
                                        (Stock: {{ $v->stock }} un.)
                                    </option>
                                @else
                                    <option value="{{ $v->id }}" disabled class="text-[#dc043c] bg-[#dc043c]/10">
                                        @if($v->talla) Talla: {{ $v->talla }} @endif 
                                        @if($v->color) | Color: {{ $v->color }} @endif 
                                        (Agotado)
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-4 rounded-xl shadow-lg flex justify-center items-center transition-all transform hover:-translate-y-1">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Agregar al Carrito
                    </button>
                </form>
            @else
                <div class="bg-[#dcb47c]/10 border-l-4 border-[#dcb47c] text-[#343c4c] p-6 rounded-r-xl shadow-sm mt-4">
                    <p class="text-sm font-black uppercase tracking-wider mb-2">Acceso Requerido</p>
                    <p class="text-xs font-medium text-[#343c4c]/80 mb-4">Debes iniciar sesión con tu cuenta para poder añadir artículos al carrito y realizar tu compra.</p>
                    <a href="{{ route('login') }}" class="inline-block bg-[#343c4c] text-white text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-lg hover:bg-[#0464a4] transition-colors shadow-md">
                        Ir al Login
                    </a>
                </div>
            @endauth
        </div>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-[#343c4c]/10 p-8 md:p-12 mb-10 max-w-7xl mx-auto">
    <div class="flex items-center mb-8 border-b-2 border-[#f4f4f4] pb-4">
        <svg class="w-8 h-8 mr-3 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        <h3 class="text-2xl font-black text-[#343c4c] uppercase tracking-tight">Opiniones de la Comunidad</h3>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        
        <div class="lg:col-span-2 space-y-6">
            @forelse($producto->resenas as $resena)
                <div class="bg-[#f4f4f4]/50 rounded-2xl p-6 border border-[#343c4c]/5 hover:border-[#dcb47c]/50 transition-colors">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-[#343c4c] rounded-full flex items-center justify-center text-white font-black text-xs uppercase shadow-sm">
                                {{ substr($resena->user->persona->nombre, 0, 1) }}{{ substr($resena->user->persona->apellidos, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-black text-[#343c4c] uppercase text-sm block">{{ $resena->user->persona->nombre }} {{ $resena->user->persona->apellidos }}</span>
                                <span class="text-[10px] font-bold text-[#343c4c]/40 uppercase tracking-widest">{{ $resena->fecha_resena->format('d M, Y') }}</span>
                            </div>
                        </div>
                        <div class="flex text-[#dcb47c] text-sm drop-shadow-sm bg-white px-2 py-1 rounded-full border border-[#f4f4f4]">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $resena->calificacion ? 'fill-current' : 'text-gray-200 fill-current' }}" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                    </div>
                    @if($resena->comentario)
                        <p class="text-sm text-[#343c4c]/80 leading-relaxed font-medium pl-13 ml-13">{{ $resena->comentario }}</p>
                    @endif
                </div>
            @empty
                <div class="text-center py-12 text-[#343c4c]/40 bg-[#f4f4f4]/30 rounded-2xl border-2 border-dashed border-[#f4f4f4]">
                    <svg class="w-16 h-16 mx-auto mb-3 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <p class="font-black uppercase tracking-wider text-sm text-[#343c4c]">Aún no hay opiniones.</p>
                    <p class="text-xs mt-1">¡Sé el primero en calificarlo!</p>
                </div>
            @endforelse
        </div>

        <div>
            @auth
                @php
                    $miResena = $producto->resenas->where('user_id', auth()->id())->first();
                @endphp

                @if($miResena)
                    <div class="bg-[#0464a4]/5 rounded-2xl border-2 border-[#0464a4]/20 shadow-inner p-6 sticky top-6">
                        <h4 class="font-black text-[#0464a4] uppercase tracking-widest text-[11px] border-b-2 border-[#0464a4]/20 pb-3 mb-5">✏️ Editar tu Reseña</h4>
                        
                        <form action="{{ route('resenas.update', $miResena->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-5">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">Cambiar Puntuación *</label>
                                <div class="flex flex-row-reverse justify-end items-center star-rating bg-white w-fit px-4 py-1.5 rounded-xl shadow-sm border border-[#f4f4f4]">
                                    <input type="radio" id="edit_star5" name="calificacion" value="5" class="hidden peer" {{ $miResena->calificacion == 5 ? 'checked' : '' }} required/>
                                    <label for="edit_star5" class="cursor-pointer text-gray-300 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="edit_star4" name="calificacion" value="4" class="hidden peer" {{ $miResena->calificacion == 4 ? 'checked' : '' }}/>
                                    <label for="edit_star4" class="cursor-pointer text-gray-300 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition peer-checked:peer-hover:text-[#dcb47c] drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="edit_star3" name="calificacion" value="3" class="hidden peer" {{ $miResena->calificacion == 3 ? 'checked' : '' }}/>
                                    <label for="edit_star3" class="cursor-pointer text-gray-300 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="edit_star2" name="calificacion" value="2" class="hidden peer" {{ $miResena->calificacion == 2 ? 'checked' : '' }}/>
                                    <label for="edit_star2" class="cursor-pointer text-gray-300 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="edit_star1" name="calificacion" value="1" class="hidden peer" {{ $miResena->calificacion == 1 ? 'checked' : '' }}/>
                                    <label for="edit_star1" class="cursor-pointer text-gray-300 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">Actualizar Comentario</label>
                                <textarea name="comentario" rows="3" class="w-full rounded-xl border-none bg-white p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] shadow-sm resize-none">{{ $miResena->comentario }}</textarea>
                            </div>

                            <div class="flex space-x-3">
                                <button type="submit" class="flex-grow bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-colors text-[10px]">
                                    Guardar Cambios
                                </button>
                        </form>
                                <form action="{{ route('resenas.destroy', $miResena->id) }}" method="POST" class="flex-none">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar tu reseña?')" class="h-full px-4 bg-[#dc043c]/10 hover:bg-[#dc043c] text-[#dc043c] hover:text-white font-black uppercase tracking-widest rounded-xl transition-colors shadow-sm text-[10px]">
                                        Borrar
                                    </button>
                                </form>
                            </div>
                    </div>
                @else
                    <div class="bg-[#343c4c] rounded-2xl shadow-xl p-6 sticky top-6 border-t-4 border-[#dcb47c]">
                        <h4 class="font-black text-white uppercase tracking-widest text-[11px] border-b border-[#f4f4f4]/20 pb-3 mb-5">⭐ Evaluar Producto</h4>
                        
                        <form action="{{ route('resenas.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                            
                            <div class="mb-5">
                                <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-2">Puntuación *</label>
                                <div class="flex flex-row-reverse justify-end items-center star-rating bg-[#f4f4f4]/10 w-fit px-4 py-1.5 rounded-xl border border-white/5">
                                    <input type="radio" id="star5" name="calificacion" value="5" class="hidden peer" required/>
                                    <label for="star5" class="cursor-pointer text-[#f4f4f4]/30 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="star4" name="calificacion" value="4" class="hidden peer"/>
                                    <label for="star4" class="cursor-pointer text-[#f4f4f4]/30 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition peer-checked:peer-hover:text-[#dcb47c] drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="star3" name="calificacion" value="3" class="hidden peer"/>
                                    <label for="star3" class="cursor-pointer text-[#f4f4f4]/30 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="star2" name="calificacion" value="2" class="hidden peer"/>
                                    <label for="star2" class="cursor-pointer text-[#f4f4f4]/30 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                    
                                    <input type="radio" id="star1" name="calificacion" value="1" class="hidden peer"/>
                                    <label for="star1" class="cursor-pointer text-[#f4f4f4]/30 peer-checked:text-[#dcb47c] hover:text-[#dcb47c] text-3xl transition drop-shadow-sm">★</label>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-[10px] font-black text-white/80 uppercase tracking-widest mb-2">Comentario (Opcional)</label>
                                <textarea name="comentario" rows="3" placeholder="¿Qué te pareció este artículo?" class="w-full rounded-xl border-none bg-white p-3.5 text-sm focus:ring-2 focus:ring-[#dcb47c] text-[#343c4c] resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#dcb47c] hover:bg-white text-[#343c4c] font-black uppercase tracking-widest py-3.5 rounded-xl shadow-lg transition-colors text-[10px] transform hover:-translate-y-0.5">
                                Publicar Reseña
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <div class="bg-white border-2 border-[#f4f4f4] rounded-2xl p-8 text-center shadow-sm">
                    <svg class="w-12 h-12 text-[#343c4c]/20 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <p class="text-sm font-bold text-[#343c4c] mb-4">Solo usuarios registrados pueden evaluar.</p>
                    <a href="{{ route('login') }}" class="inline-block bg-[#0464a4] text-white text-[10px] font-black uppercase tracking-widest py-2.5 px-6 rounded-lg hover:bg-[#343c4c] transition-colors shadow-md">
                        Iniciar sesión
                    </a>
                </div>
            @endauth
        </div>

    </div>
</div>

<style>
    /* Estilo encapsulado para que el hover funcione en ambos formularios de estrellas */
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label { color: #dcb47c; }
    
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #343c4c20; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0464a4; }
</style>

<script>
    function cambiarImagen(ruta) {
        const img = document.getElementById('mainDisplayImage');
        if(img) img.src = ruta;
    }
</script>
@endsection