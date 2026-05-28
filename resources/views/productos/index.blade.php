@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-6">
    <div class="mb-8 flex flex-col md:flex-row justify-between md:items-end gap-5 border-b-2 border-[#f4f4f4] pb-5">
        <div>
            <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Catálogo y Productos</h1>
            <p class="text-[#343c4c]/60 text-sm font-medium mt-1">Gestiona inventario, asigna variantes de stock y supervisa el material 3D.</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="openCategoriaModal()" class="bg-[#343c4c] hover:bg-[#dcb47c] text-white hover:text-[#343c4c] font-black uppercase tracking-widest py-3 px-5 rounded-xl shadow-md flex items-center justify-center transition-all text-xs transform hover:-translate-y-0.5 border border-transparent hover:border-[#343c4c]/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Categoría
            </button>
            <button onclick="openProductoModal()" class="bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3 px-5 rounded-xl shadow-md flex items-center justify-center transition-all text-xs transform hover:-translate-y-0.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Producto
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        @forelse($productos as $prod)
            @php 
                $stockTotal = $prod->variantes->sum('stock');
                $fotos = json_decode($prod->imagen_url, true) ?? [];
                $portada = count($fotos) > 0 ? $fotos[0] : null;
            @endphp
            
            <div class="bg-white rounded-3xl shadow-lg border border-[#343c4c]/5 overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-1 hover:shadow-2xl group">
                
                <div class="h-56 bg-[#f4f4f4] relative flex items-center justify-center overflow-hidden p-4">
                    @if($portada)
                        <img src="{{ asset('storage/' . $portada) }}" alt="{{ $prod->nombre }}" class="w-full h-full object-contain drop-shadow-md group-hover:scale-110 transition-transform duration-500">
                    @else
                        <svg class="w-16 h-16 text-[#343c4c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    @endif

                    <div class="absolute top-3 left-3 flex flex-col gap-2">
                        @if($prod->modelo_3d_url)
                            <span class="bg-[#0464a4] text-white text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-md shadow-sm border border-white/20 flex items-center w-fit">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                3D Activo
                            </span>
                        @endif
                    </div>

                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 bg-[#343c4c] text-[#dcb47c] text-[10px] font-black uppercase tracking-widest rounded-md shadow-sm border border-[#dcb47c]/20">
                            Stock: {{ $stockTotal }}
                        </span>
                    </div>
                </div>

                <div class="p-5 flex-grow bg-white z-10 relative">
                    <span class="text-[9px] font-black text-[#dcb47c] uppercase tracking-widest">{{ $prod->categoria->nombre ?? 'Sin Categoría' }}</span>
                    <h3 class="text-sm font-black text-[#343c4c] line-clamp-2 mt-1 leading-tight group-hover:text-[#0464a4] transition-colors" title="{{ $prod->nombre }}">{{ $prod->nombre }}</h3>
                    <p class="text-[10px] text-[#343c4c]/50 font-bold uppercase tracking-wider mt-1.5">{{ $prod->marca ?? 'N/R' }}</p>
                    
                    <div class="mt-4 pt-3 border-t-2 border-[#f4f4f4] grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-[#f4f4f4]/50 p-2 rounded-lg text-center">
                            <span class="block text-[9px] font-black text-[#343c4c]/40 uppercase tracking-widest">Costo</span>
                            <span class="font-bold text-[#343c4c]/70">Bs {{ number_format($prod->precio_compra, 2) }}</span>
                        </div>
                        <div class="bg-[#dc043c]/5 p-2 rounded-lg text-center">
                            <span class="block text-[9px] font-black text-[#dc043c]/60 uppercase tracking-widest">Venta</span>
                            <span class="font-black text-[#dc043c]">Bs {{ number_format($prod->precio_venta, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-[#f4f4f4]/50 border-t border-[#f4f4f4] flex flex-col gap-2">
                    <div class="flex gap-2">
                        <button onclick="openProductoModal({{ $prod->toJson() }})" class="flex-1 text-[#0464a4] font-black text-[10px] uppercase tracking-widest px-3 py-2 bg-white border border-[#0464a4]/20 hover:bg-[#0464a4] hover:text-white rounded-xl transition-colors shadow-sm">Editar</button>
                        <button onclick="openDeleteModal({{ $prod->id }}, '{{ addslashes($prod->nombre) }}')" class="flex-1 text-[#dc043c] font-black text-[10px] uppercase tracking-widest px-3 py-2 bg-white border border-[#dc043c]/20 hover:bg-[#dc043c] hover:text-white rounded-xl transition-colors shadow-sm">Eliminar</button>
                    </div>
                    
                    @if($prod->proveedor && $prod->proveedor->telefono)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $prod->proveedor->telefono) }}?text=Hola%20{{ urlencode($prod->proveedor->nombre_empresa) }},%20necesitamos%20solicitar%20un%20nuevo%20lote%20del%20producto:%20{{ urlencode($prod->nombre) }}." 
                           target="_blank" 
                           class="text-green-700 font-black text-[10px] uppercase tracking-widest px-3 py-2.5 bg-green-50 border border-green-200 hover:bg-green-600 hover:text-white rounded-xl text-center flex items-center justify-center transition-colors shadow-sm mt-1">
                            <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                            Contactar Proveedor
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-16 text-center rounded-3xl border-2 border-dashed border-[#dcb47c] shadow-lg relative z-10">
                <svg class="w-20 h-20 mx-auto text-[#dcb47c] mb-4 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c] uppercase tracking-wide">Inventario Vacío</h3>
                <p class="text-[#343c4c]/60 mt-2 font-medium">No hay productos en el catálogo operativo. Registra el primero.</p>
            </div>
        @endforelse
    </div>
</div>

<div id="productoModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center overflow-y-auto py-10">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl mx-4 overflow-hidden border border-[#343c4c]/10 transform transition-all my-auto">
        <div class="bg-[#343c4c] px-8 py-5 border-b-4 border-[#dcb47c] flex justify-between items-center sticky top-0 z-20">
            <h3 id="productoModalTitle" class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                <svg class="w-5 h-5 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                Nuevo Producto
            </h3>
            <button onclick="closeProductoModal()" class="text-white hover:text-[#dc043c] text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form id="productoForm" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="productoFormMethod" value="POST">

            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-8 max-h-[70vh] overflow-y-auto custom-scrollbar">
                
                <div class="md:col-span-2 space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombre Completo *</label>
                        <input type="text" name="nombre" id="nombre" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Categoría *</label>
                            <select name="categoria_id" id="categoria_id" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer">
                                <option value="">Seleccione...</option>
                                @foreach($categorias as $cat) <option value="{{ $cat->id }}">{{ $cat->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Proveedor Asignado *</label>
                            <select name="proveedor_id" id="proveedor_id" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer">
                                <option value="">Seleccione...</option>
                                @foreach($proveedores as $prov) <option value="{{ $prov->id }}">{{ $prov->nombre_empresa }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Marca de Fabricación</label>
                        <input type="text" name="marca" id="marca" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Descripción Funcional</label>
                        <textarea name="descripcion" id="descripcion" rows="3" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-medium text-[#343c4c] resize-none"></textarea>
                    </div>
                </div>

                <div class="bg-[#f4f4f4]/50 p-6 rounded-2xl border border-[#343c4c]/5 space-y-5 h-fit">
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Costo de Compra (Bs) *</label>
                        <input type="number" step="0.01" name="precio_compra" id="precio_compra" required class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-black text-[#343c4c] shadow-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-[#dc043c] uppercase tracking-widest mb-1.5">Precio de Venta PVP *</label>
                        <input type="number" step="0.01" name="precio_venta" id="precio_venta" required class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#dc043c] font-black text-[#dc043c] shadow-sm">
                    </div>
                    
                    <div class="border-t-2 border-[#f4f4f4] pt-4 mt-2">
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Imágenes (Múltiple)</label>
                        <input type="file" name="imagenes[]" multiple accept="image/*" class="w-full text-[10px] text-[#343c4c] font-bold file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-[#343c4c] file:text-white file:cursor-pointer hover:file:bg-[#dcb47c] hover:file:text-[#343c4c] transition-all bg-white rounded-xl shadow-sm p-1">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-[#0464a4] uppercase tracking-widest mb-1.5">Modelo 3D (.glb)</label>
                        <input type="file" name="modelo_3d" accept=".glb" class="w-full text-[10px] text-[#0464a4] font-bold file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-[#0464a4] file:text-white file:cursor-pointer hover:file:bg-[#343c4c] transition-all bg-white rounded-xl shadow-sm p-1">
                    </div>
                </div>

                <div class="md:col-span-3 pt-6 border-t-2 border-[#f4f4f4]" id="sectionVariantes">
                    <div class="flex justify-between items-center mb-4 bg-[#f4f4f4] px-4 py-3 rounded-xl border border-[#343c4c]/5">
                        <h4 class="text-[10px] font-black text-[#343c4c] uppercase tracking-widest">Inventariar Variantes Iniciales</h4>
                        <button type="button" onclick="agregarFila()" class="bg-[#0464a4] text-white text-[9px] font-black uppercase tracking-widest py-2 px-4 rounded-lg shadow-sm hover:bg-[#343c4c] transition-colors">
                            + Añadir Variante
                        </button>
                    </div>
                    <div id="wrapperFilas" class="space-y-3">
                        <div class="fila-variante flex space-x-3 bg-white p-3 rounded-xl border border-[#f4f4f4] shadow-sm items-center">
                            <input type="text" name="variante_talla[]" placeholder="Talla / Medida" class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] font-bold">
                            <input type="text" name="variante_color[]" placeholder="Color" class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] font-bold">
                            <input type="number" name="variante_stock[]" placeholder="Stock" required class="w-24 bg-[#f4f4f4] border-none rounded-lg p-2.5 text-xs font-black text-[#0464a4] focus:ring-2 focus:ring-[#0464a4] text-center">
                            <button type="button" onclick="eliminarFila(this)" class="text-[#dc043c] hover:bg-[#dc043c]/10 p-2 rounded-lg font-black text-lg transition-colors">&times;</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-8 py-5 border-t border-[#f4f4f4] bg-[#f4f4f4]/50 flex justify-end space-x-3 rounded-b-3xl">
                <button type="button" onclick="closeProductoModal()" class="px-5 py-3 bg-gray-200 text-[#343c4c] rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-300 transition-colors">Cancelar</button>
                <button type="submit" class="px-6 py-3 bg-[#0464a4] hover:bg-[#343c4c] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-md transition-all transform hover:-translate-y-0.5">Guardar Producto</button>
            </div>
        </form>
    </div>
</div>

<div id="categoriaModal" class="fixed inset-0 z-[60] hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#dcb47c] flex justify-between items-center">
            <h3 class="text-sm font-black text-white uppercase tracking-widest">Nueva Categoría</h3>
            <button onclick="closeCategoriaModal()" class="text-white hover:text-[#dc043c] text-2xl font-bold transition-colors">&times;</button>
        </div>
        <form method="POST" action="{{ route('categorias.store.rapida') }}">
            @csrf
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombre de Categoría *</label>
                    <input type="text" name="nombre" required placeholder="Ej: Guantes de Box..." class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Descripción</label>
                    <textarea name="descripcion" rows="2" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-medium text-[#343c4c] resize-none"></textarea>
                </div>
            </div>
            <div class="px-8 py-5 border-t border-[#f4f4f4] bg-[#f4f4f4]/50 flex justify-end space-x-3">
                <button type="button" onclick="closeCategoriaModal()" class="px-4 py-2.5 bg-gray-200 text-[#343c4c] rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-300 transition-colors">Cancelar</button>
                <button type="submit" class="px-5 py-2.5 bg-[#343c4c] hover:bg-[#dcb47c] hover:text-[#343c4c] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-md transition-all">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/80 flex items-center justify-center">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md mx-4 p-8 text-center border-t-4 border-[#dc043c]">
        <svg class="w-16 h-16 text-[#dc043c] mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        <form id="deleteForm" method="POST" action="">
            @csrf @method('DELETE')
            <h3 class="mb-6 text-sm font-bold text-[#343c4c] leading-relaxed">¿Estás seguro de eliminar el producto<br><strong id="deleteName" class="text-lg font-black text-[#dc043c] uppercase block mt-1"></strong></h3>
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-6 py-3 bg-gray-200 text-[#343c4c] rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-gray-300 transition-colors">Cancelar</button>
                <button type="submit" class="px-6 py-3 bg-[#dc043c] hover:bg-[#343c4c] text-white rounded-xl font-black text-[10px] uppercase tracking-widest shadow-md transition-colors">Confirmar Eliminación</button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f4; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #dcb47c; border-radius: 10px; }
</style>

<script>
    function agregarFila() {
        const row = `<div class="fila-variante flex space-x-3 bg-white p-3 rounded-xl border border-[#f4f4f4] shadow-sm items-center mt-3">
            <input type="text" name="variante_talla[]" placeholder="Talla / Medida" class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] font-bold">
            <input type="text" name="variante_color[]" placeholder="Color" class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-xs focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] font-bold">
            <input type="number" name="variante_stock[]" placeholder="Stock" required class="w-24 bg-[#f4f4f4] border-none rounded-lg p-2.5 text-xs font-black text-[#0464a4] focus:ring-2 focus:ring-[#0464a4] text-center">
            <button type="button" onclick="eliminarFila(this)" class="text-[#dc043c] hover:bg-[#dc043c]/10 p-2 rounded-lg font-black text-lg transition-colors">&times;</button>
        </div>`;
        document.getElementById('wrapperFilas').insertAdjacentHTML('beforeend', row);
    }

    function eliminarFila(btn) {
        const wrapper = document.getElementById('wrapperFilas');
        if(wrapper.children.length > 1) btn.closest('.fila-variante').remove();
    }

    function openProductoModal(prod = null) {
        const modal = document.getElementById('productoModal');
        const form = document.getElementById('productoForm');
        const method = document.getElementById('productoFormMethod');
        const sect = document.getElementById('sectionVariantes');

        if(prod) {
            document.getElementById('productoModalTitle').innerHTML = '<svg class="w-5 h-5 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg> Modificar Catálogo';
            form.action = `/productos/${prod.id}`;
            method.value = 'PUT';
            sect.classList.add('hidden');

            document.getElementById('nombre').value = prod.nombre;
            document.getElementById('categoria_id').value = prod.categoria_id;
            document.getElementById('proveedor_id').value = prod.proveedor_id;
            document.getElementById('descripcion').value = prod.descripcion || '';
            document.getElementById('marca').value = prod.marca || '';
            document.getElementById('precio_compra').value = prod.precio_compra;
            document.getElementById('precio_venta').value = prod.precio_venta;
        } else {
            document.getElementById('productoModalTitle').innerHTML = '<svg class="w-5 h-5 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg> Nuevo Producto';
            form.action = `{{ route('productos.store') }}`;
            method.value = 'POST';
            sect.classList.remove('hidden');
            form.reset();
        }
        modal.classList.remove('hidden');
    }

    function closeProductoModal() { document.getElementById('productoModal').classList.add('hidden'); }
    
    // CONTROL DEL MODAL DE CATEGORIAS
    function openCategoriaModal() { document.getElementById('categoriaModal').classList.remove('hidden'); }
    function closeCategoriaModal() { document.getElementById('categoriaModal').classList.add('hidden'); }

    // CONTROL MODAL ELIMINAR
    function openDeleteModal(id, nom) {
        document.getElementById('deleteName').innerText = nom;
        document.getElementById('deleteForm').action = `/productos/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }
</script>
@endsection