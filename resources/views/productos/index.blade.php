@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Administración de Productos</h2>
        <p class="text-gray-500 text-sm">Gestiona el catálogo, asigna variantes de stock y supervisa el material 3D.</p>
    </div>
    <div class="flex space-x-3">
        <button onclick="openCategoriaModal()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow flex items-center transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            Nueva Categoría
        </button>
        <button onclick="openProductoModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Nuevo Producto
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($productos as $prod)
        @php 
            $stockTotal = $prod->variantes->sum('stock');
            $fotos = json_decode($prod->imagen_url, true) ?? [];
            $portada = count($fotos) > 0 ? $fotos[0] : null;
        @endphp
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col justify-between">
            <div class="h-48 bg-gray-50 relative flex items-center justify-center">
                @if($portada)
                    <img src="{{ asset('storage/' . $portada) }}" alt="" class="w-full h-full object-cover">
                @else
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @endif

                <div class="absolute top-2 left-2">
                    @if($prod->modelo_3d_url)
                        <span class="bg-purple-600 text-white text-xs font-bold px-2 py-0.5 rounded shadow flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            3D Activo
                        </span>
                    @else
                        <span class="bg-gray-400 text-white text-xs font-bold px-2 py-0.5 rounded shadow">Sin 3D</span>
                    @endif
                </div>

                <div class="absolute top-2 right-2">
                    <span class="px-2 py-0.5 bg-gray-900 text-white text-xs font-bold rounded shadow">Stock: {{ $stockTotal }}</span>
                </div>
            </div>

            <div class="p-4 flex-grow">
                <span class="text-xs font-bold text-blue-600 uppercase">{{ $prod->categoria->nombre ?? 'Sin Categoría' }}</span>
                <h3 class="text-md font-bold text-gray-800 line-clamp-1 mt-1">{{ $prod->nombre }}</h3>
                <p class="text-xs text-gray-400">Marca: {{ $prod->marca ?? 'N/R' }}</p>
                
                <div class="mt-3 pt-2 border-t text-xs space-y-1 text-gray-600">
                    <p><strong>P. Compra:</strong> Bs {{ number_format($prod->precio_compra, 2) }}</p>
                    <p><strong>P. Venta:</strong> Bs {{ number_format($prod->precio_venta, 2) }}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-3 border-t flex flex-col space-y-2">
                <div class="flex space-x-2">
                    <button onclick="openProductoModal({{ $prod->toJson() }})" class="text-blue-600 font-semibold text-xs px-3 py-1.5 bg-blue-50 hover:bg-blue-100 rounded w-full transition">Editar</button>
                    <button onclick="openDeleteModal({{ $prod->id }}, '{{ $prod->nombre }}')" class="text-red-600 font-semibold text-xs px-3 py-1.5 bg-red-50 hover:bg-red-100 rounded w-full transition">Eliminar</button>
                </div>
                
                @if($prod->proveedor && $prod->proveedor->telefono)
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $prod->proveedor->telefono) }}?text=Hola%20{{ urlencode($prod->proveedor->nombre_empresa) }},%20necesitamos%20solicitar%20un%20nuevo%20lote%20del%20producto:%20{{ urlencode($prod->nombre) }}." 
                       target="_blank" 
                       class="text-green-700 font-bold text-xs px-3 py-1.5 bg-green-50 hover:bg-green-100 rounded text-center flex items-center justify-center transition">
                        Contactar Proveedor (WA)
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white p-8 text-center rounded-xl border shadow-sm">
            <p class="text-gray-500">No hay productos en el catálogo operativo.</p>
        </div>
    @endforelse
</div>

<div id="productoModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 my-8 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50 sticky top-0 z-10">
            <h3 id="productoModalTitle" class="text-lg font-bold text-gray-800">Nuevo Producto</h3>
            <button onclick="closeProductoModal()" class="text-gray-400 text-2xl font-bold">&times;</button>
        </div>

        <form id="productoForm" method="POST" action="{{ route('productos.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_method" id="productoFormMethod" value="POST">

            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 max-h-[65vh] overflow-y-auto">
                <div class="md:col-span-2 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase">Nombre Completo *</label>
                        <input type="text" name="nombre" id="nombre" required class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Categoría *</label>
                            <select name="categoria_id" id="categoria_id" required class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                                <option value="">Seleccione...</option>
                                @foreach($categorias as $cat) <option value="{{ $cat->id }}">{{ $cat->nombre }}</option> @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Proveedor Asignado *</label>
                            <select name="proveedor_id" id="proveedor_id" required class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm bg-white">
                                <option value="">Seleccione...</option>
                                @foreach($proveedores as $prov) <option value="{{ $prov->id }}">{{ $prov->nombre_empresa }}</option> @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase">Descripción Funcional</label>
                        <textarea name="descripcion" id="descripcion" rows="2" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase">Marca de Fabricación</label>
                        <input type="text" name="marca" id="marca" class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm">
                    </div>
                </div>

                <div class="space-y-4 bg-gray-50 p-4 rounded-lg border">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase">Costo de Compra (Bs) *</label>
                        <input type="number" step="0.01" name="precio_compra" id="precio_compra" required class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-blue-700 uppercase">Precio de Venta al Público *</label>
                        <input type="number" step="0.01" name="precio_venta" id="precio_venta" required class="mt-1 block w-full rounded-md border-gray-300 border p-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase">Imágenes del Producto (Múltiple)</label>
                        <input type="file" name="imagenes[]" multiple accept="image/*" class="mt-1 block w-full text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase">Modelo Simulación 3D (.glb)</label>
                        <input type="file" name="modelo_3d" accept=".glb" class="mt-1 block w-full text-xs">
                    </div>
                </div>

                <div class="md:col-span-3 border-t pt-4" id="sectionVariantes">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-sm font-bold text-gray-700 uppercase">Inventariar Variantes Iniciales</h4>
                        <button type="button" onclick="agregarFila()" class="bg-green-600 text-white text-xs font-bold py-1 px-3 rounded shadow hover:bg-green-700">+ Variante</button>
                    </div>
                    <div id="wrapperFilas" class="space-y-2">
                        <div class="fila-variante flex space-x-2 bg-gray-50 p-2 rounded border">
                            <input type="text" name="variante_talla[]" placeholder="Talla / Medida" class="w-full rounded border-gray-300 p-1.5 text-xs">
                            <input type="text" name="variante_color[]" placeholder="Color" class="w-full rounded border-gray-300 p-1.5 text-xs">
                            <input type="number" name="variante_stock[]" placeholder="Stock" required class="w-28 rounded border-gray-300 p-1.5 text-xs font-bold text-blue-600">
                            <button type="button" onclick="eliminarFila(this)" class="text-red-500 font-bold px-2">&times;</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3 sticky bottom-0">
                <button type="button" onclick="closeProductoModal()" class="px-4 py-2 bg-gray-200 rounded font-medium text-sm">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded font-bold text-sm">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<div id="categoriaModal" class="fixed inset-0 z-[60] hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-800">Nueva Categoría</h3>
            <button onclick="closeCategoriaModal()" class="text-gray-400 text-2xl font-bold">&times;</button>
        </div>
        <form method="POST" action="{{ route('categorias.store.rapida') }}">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre de Categoría *</label>
                    <input type="text" name="nombre" required placeholder="Ej: Guantes de Box, Balones..." class="mt-1 block w-full rounded-md border-gray-300 border p-2 focus:ring-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea name="descripcion" rows="2" class="mt-1 block w-full rounded-md border-gray-300 border p-2 focus:ring-blue-500 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeCategoriaModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-medium transition text-sm">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900 font-bold transition text-sm">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 p-6 text-center">
        <form id="deleteForm" method="POST" action="">
            @csrf @method('DELETE')
            <h3 class="mb-5 text-md font-medium text-gray-600">¿Eliminar el producto <strong id="deleteName"></strong>?</h3>
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 rounded font-medium text-xs">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded font-bold text-xs">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function agregarFila() {
        const row = `<div class="fila-variante flex space-x-2 bg-gray-50 p-2 rounded border">
            <input type="text" name="variante_talla[]" placeholder="Talla / Medida" class="w-full rounded border-gray-300 p-1.5 text-xs">
            <input type="text" name="variante_color[]" placeholder="Color" class="w-full rounded border-gray-300 p-1.5 text-xs">
            <input type="number" name="variante_stock[]" placeholder="Stock" required class="w-28 rounded border-gray-300 p-1.5 text-xs font-bold text-blue-600">
            <button type="button" onclick="eliminarFila(this)" class="text-red-500 font-bold px-2">&times;</button>
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
            document.getElementById('productoModalTitle').innerText = 'Modificar Catálogo';
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
            document.getElementById('productoModalTitle').innerText = 'Nuevo Producto';
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

    function openDeleteModal(id, nom) {
        document.getElementById('deleteName').innerText = nom;
        document.getElementById('deleteForm').action = `/productos/${id}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    function closeDeleteModal() { document.getElementById('deleteModal').classList.add('hidden'); }
</script>
@endsection