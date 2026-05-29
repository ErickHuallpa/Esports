@extends('layouts.app')

@section('content')
@if($producto->modelo_3d_url)
    <script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>
@endif

<div class="relative w-full min-h-screen pb-20 overflow-hidden">
    
    <div class="absolute inset-x-0 bottom-0 w-full h-[250px] md:h-[400px] pointer-events-none z-0" 
         style="background-image: url('{{ asset('img/cesped.png') }}'); background-position: bottom center; background-repeat: repeat-x; background-size: auto 100%; opacity: 1;">
    </div>

    <div class="absolute inset-0 bg-gradient-to-b from-[#f4f4f4] via-[#f4f4f4]/80 to-transparent pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-7xl mx-auto pt-6 px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6">
            <a href="{{ route('home') }}#catalogo" class="inline-flex items-center text-[#343c4c]/60 hover:text-[#dc043c] font-black uppercase tracking-widest text-[10px] transition-colors bg-white px-5 py-2.5 rounded-xl shadow-sm border border-[#343c4c]/5 hover:border-[#dc043c]/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver al catálogo
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-[#343c4c]/10 overflow-hidden mb-10">
            <div class="flex flex-col lg:flex-row">
                
                <div class="w-full lg:w-5/12 xl:w-1/2 p-8 lg:p-12 flex flex-col bg-white relative z-10 order-2 lg:order-1">
                    
                    <div class="mb-8">
                        <span class="inline-block px-3 py-1 bg-[#dcb47c]/20 text-[#343c4c] text-[10px] font-black uppercase tracking-widest rounded-md mb-3 border border-[#dcb47c]/50">
                            {{ $producto->categoria->nombre ?? 'General' }}
                        </span>
                        <h1 class="text-3xl lg:text-5xl font-black text-[#343c4c] leading-none mb-6 uppercase tracking-tight">{{ $producto->nombre }}</h1>
                        
                        <div class="flex items-end gap-4 pb-6 border-b-2 border-[#f4f4f4]">
                            @php
                                $ofertaActiva = $producto->ofertas->where('fecha_inicio', '<=', now())->where('fecha_fin', '>=', now())->where('activa', true)->first();
                                $precioFinal = $producto->precio_venta;
                            @endphp
                            
                            @if($ofertaActiva)
                                @php $precioFinal = $producto->precio_venta - ($producto->precio_venta * ($ofertaActiva->porcentaje_descuento / 100)); @endphp
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-[#343c4c]/40 line-through">Bs {{ number_format($producto->precio_venta, 2) }}</span>
                                    <span class="text-5xl font-black text-[#dc043c] drop-shadow-sm">Bs {{ number_format($precioFinal, 2) }}</span>
                                </div>
                                <span class="bg-[#dc043c] text-white text-xs font-black px-3 py-1.5 rounded uppercase tracking-widest mb-2 ml-2 shadow-sm">-{{ $ofertaActiva->porcentaje_descuento }}%</span>
                            @else
                                <span class="text-5xl font-black text-[#dc043c] drop-shadow-sm">Bs {{ number_format($precioFinal, 2) }}</span>
                            @endif
                        </div>
                    </div>

                    @auth
                        <form action="{{ route('carrito.agregar') }}" method="POST" class="space-y-6 mt-2 flex-grow flex flex-col justify-center">
                            @csrf
                            <div>
                                <label for="producto_variante_id" class="block text-xs font-black text-[#343c4c] uppercase tracking-widest mb-3">Seleccionar Variante *</label>
                                <select id="producto_variante_id" name="producto_variante_id" required class="w-full bg-[#f4f4f4] border-none text-[#343c4c] text-base font-bold rounded-xl focus:ring-4 focus:ring-[#0464a4]/20 focus:border-[#0464a4] block p-5 shadow-inner cursor-pointer transition-colors">
                                    <option value="" disabled selected>Elige Talla / Color...</option>
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

                            <button type="submit" class="w-full bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-5 rounded-xl shadow-lg flex justify-center items-center transition-all transform hover:-translate-y-1 text-sm mt-4">
                                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                Agregar al Carrito
                            </button>
                        </form>
                    @else
                        <div class="bg-[#dcb47c]/10 border-l-4 border-[#dcb47c] text-[#343c4c] p-8 rounded-r-2xl shadow-sm mt-4 flex-grow flex flex-col justify-center">
                            <p class="text-base font-black uppercase tracking-wider mb-2">Acceso Requerido</p>
                            <p class="text-sm font-medium text-[#343c4c]/80 mb-6">Debes iniciar sesión con tu cuenta para poder añadir artículos al carrito y realizar tu compra.</p>
                            <a href="{{ route('login') }}" class="inline-block bg-[#343c4c] text-white text-xs font-black uppercase tracking-widest py-3 px-8 rounded-xl hover:bg-[#0464a4] transition-colors shadow-md text-center">
                                Iniciar sesión
                            </a>
                        </div>
                    @endauth
                </div>

                <div class="w-full lg:w-7/12 xl:w-1/2 bg-[#f4f4f4]/50 flex flex-col p-6 lg:p-10 relative border-b lg:border-b-0 lg:border-l border-[#343c4c]/5 order-1 lg:order-2">
                    
                    <div class="w-full flex-grow flex items-center justify-center transition-all duration-500 min-h-[400px]">
                        
                        <img id="mainImage" src="" alt="{{ $producto->nombre }}" 
                             class="hidden w-auto max-w-full h-auto max-h-[75vh] rounded-2xl shadow-lg border border-[#343c4c]/5 bg-white object-contain transition-opacity duration-300">
                        
                        @if($producto->video_url)
                            <video id="mainVideo" src="{{ asset('storage/' . $producto->video_url) }}" controls 
                                   class="hidden w-auto max-w-full h-auto max-h-[75vh] rounded-2xl shadow-xl object-contain"></video>
                        @endif

                        @if($producto->modelo_3d_url)
                            <div id="wrapper3D" class="hidden w-full h-[60vh] min-h-[400px] relative rounded-2xl shadow-xl border border-[#343c4c]/5 bg-white overflow-hidden">
                                <model-viewer id="main3D"
                                    src="{{ asset('storage/' . $producto->modelo_3d_url) }}" 
                                    auto-rotate camera-controls shadow-intensity="1"
                                    class="w-full h-full" alt="Modelo 3D Interactivo">
                                </model-viewer>
                                <div class="absolute bottom-4 left-4 bg-[#343c4c]/80 backdrop-blur px-3 py-1.5 rounded-lg shadow-md border border-white/10 flex items-center z-20 pointer-events-none">
                                    <span class="text-lg mr-2">🖱️</span>
                                    <span class="text-[9px] font-black text-white uppercase tracking-widest leading-tight">Arrastra para rotar</span>
                                </div>
                            </div>
                        @endif

                        <div id="noMedia" class="hidden flex-col items-center justify-center w-full h-[400px] bg-white rounded-2xl border border-[#343c4c]/5 text-[#343c4c]/30 shadow-sm">
                            <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-[10px] font-black uppercase tracking-widest">Sin multimedia</p>
                        </div>
                    </div>

                    @php $fotos = json_decode($producto->imagen_url, true) ?? []; @endphp

                    <div class="flex gap-3 overflow-x-auto pt-8 mt-4 custom-scrollbar relative z-20 w-full justify-center">
                        @foreach($fotos as $index => $foto)
                            <button onclick="showMedia('image', '{{ asset('storage/' . $foto) }}', this)" class="media-thumb w-16 h-16 rounded-xl border-2 border-transparent bg-white overflow-hidden flex-shrink-0 hover:border-[#0464a4] focus:outline-none focus:border-[#0464a4] transition-all shadow-sm relative group">
                                <img src="{{ asset('storage/' . $foto) }}" class="w-full h-full object-contain p-1">
                                <div class="absolute inset-0 bg-[#0464a4]/5 opacity-0 group-focus:opacity-100 transition-opacity"></div>
                            </button>
                        @endforeach

                        @if($producto->video_url)
                            <button onclick="showMedia('video', '', this)" class="media-thumb w-16 h-16 rounded-xl border-2 border-transparent bg-[#343c4c] overflow-hidden flex-shrink-0 hover:border-[#dc043c] focus:outline-none focus:border-[#dc043c] transition-all shadow-sm flex flex-col items-center justify-center text-white group">
                                <svg class="w-5 h-5 mb-1 text-[#dc043c] group-focus:text-white transition-colors" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span class="text-[8px] font-black uppercase tracking-widest">Video</span>
                            </button>
                        @endif

                        @if($producto->modelo_3d_url)
                            <button onclick="showMedia('3d', '', this)" class="media-thumb w-16 h-16 rounded-xl border-2 border-transparent bg-[#0464a4] overflow-hidden flex-shrink-0 hover:border-[#343c4c] focus:outline-none focus:border-[#343c4c] transition-all shadow-sm flex flex-col items-center justify-center text-white group">
                                <svg class="w-5 h-5 mb-1 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <span class="text-[8px] font-black uppercase tracking-widest">3D</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-[#343c4c]/10 p-8 md:p-12 mb-10 max-w-7xl mx-auto relative z-10">
            <div class="flex items-center mb-6 border-b-2 border-[#f4f4f4] pb-4">
                <svg class="w-8 h-8 mr-3 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c] uppercase tracking-tight">Detalles del Producto</h3>
            </div>
            
            <div class="text-[#343c4c]/80 leading-relaxed font-medium text-sm md:text-base whitespace-pre-line">
                @if($producto->descripcion)
                    {{ $producto->descripcion }}
                @else
                    <p class="text-[#343c4c]/40 italic">Este insumo no cuenta con una descripción detallada por el momento.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-[#343c4c]/10 p-8 md:p-12 mb-10 max-w-7xl mx-auto relative z-10">
            <div class="flex items-center mb-8 border-b-2 border-[#f4f4f4] pb-4">
                <svg class="w-8 h-8 mr-3 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c] uppercase tracking-tight">Opiniones de la Comunidad</h3>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <div class="lg:col-span-2 space-y-6">
                    @forelse($producto->resenas as $resena)
                        <div class="bg-[#f4f4f4]/80 rounded-2xl p-6 border border-[#343c4c]/5 hover:border-[#dcb47c]/50 transition-colors shadow-sm">
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
                        <div class="text-center py-12 text-[#343c4c]/40 bg-white rounded-2xl border-2 border-dashed border-[#dcb47c]/50">
                            <svg class="w-16 h-16 mx-auto mb-3 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <p class="font-black uppercase tracking-wider text-sm text-[#343c4c]">Aún no hay opiniones.</p>
                            <p class="text-xs mt-1">¡Sé el primero en calificarlo!</p>
                        </div>
                    @endforelse
                </div>

                <div>
                    @auth
                        @php $miResena = $producto->resenas->where('user_id', auth()->id())->first(); @endphp

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
                                            Guardar
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
    </div>
</div>

<style>
    /* Estilo encapsulado para formularios de estrellas */
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label { color: #dcb47c; }
    
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #343c4c20; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #0464a4; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar el visor con el primer contenido disponible
        @php
            $fotosInit = json_decode($producto->imagen_url, true) ?? [];
        @endphp
        
        const firstBtn = document.querySelector('.media-thumb');

        @if(count($fotosInit) > 0)
            showMedia('image', '{{ asset('storage/' . $fotosInit[0]) }}', firstBtn);
        @elseif($producto->video_url)
            showMedia('video', '', firstBtn);
        @elseif($producto->modelo_3d_url)
            showMedia('3d', '', firstBtn);
        @else
            document.getElementById('noMedia').classList.remove('hidden');
            document.getElementById('noMedia').classList.add('flex');
        @endif
    });

    function showMedia(type, url, btn) {
        // Elementos
        const imgEl = document.getElementById('mainImage');
        const vidEl = document.getElementById('mainVideo');
        const modWrapper = document.getElementById('wrapper3D');
        const noMedia = document.getElementById('noMedia');

        // Limpiar estado de todos los botones
        document.querySelectorAll('.media-thumb').forEach(el => {
            el.classList.remove('border-[#0464a4]', 'border-[#dc043c]', 'opacity-100');
            el.classList.add('border-transparent', 'opacity-60');
        });

        // Activar botón clickeado
        if(btn) {
            btn.classList.remove('border-transparent', 'opacity-60');
            if(type === 'video') {
                btn.classList.add('border-[#dc043c]', 'opacity-100');
            } else {
                btn.classList.add('border-[#0464a4]', 'opacity-100');
            }
        }

        // Ocultar todos los visualizadores
        if(imgEl) imgEl.classList.add('hidden');
        if(vidEl) { vidEl.classList.add('hidden'); vidEl.pause(); }
        if(modWrapper) modWrapper.classList.add('hidden');
        if(noMedia) { noMedia.classList.add('hidden'); noMedia.classList.remove('flex'); }

        // Mostrar el solicitado
        if (type === 'image') {
            if(imgEl) {
                imgEl.src = url;
                imgEl.classList.remove('hidden');
            }
        } else if (type === 'video') {
            if(vidEl) {
                vidEl.classList.remove('hidden');
                vidEl.play();
            }
        } else if (type === '3d') {
            if(modWrapper) {
                modWrapper.classList.remove('hidden');
            }
        }
    }
</script>
@endsection