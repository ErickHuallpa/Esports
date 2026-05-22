@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Estructura de Categorías</h1>
        <p class="text-gray-500 text-sm">Gestiona las agrupaciones del catálogo. Las categorías desactivadas ocultarán sus productos vinculados en el catálogo público.</p>
    </div>
    <button onclick="abrirCrearModal()" class="inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm transition">
        ➕ Agregar Nueva Categoría
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($categorias as $cat)
        <div class="bg-white border rounded-2xl p-5 shadow-sm relative flex flex-col justify-between transition hover:shadow-md {{ !$cat->activo ? 'border-dashed border-red-300 bg-red-50/20' : 'border-gray-200' }}">
            <div>
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-bold text-lg text-gray-900 line-clamp-1" id="nombre_txt_{{ $cat->id }}">{{ $cat->nombre }}</h3>
                    
                    @if($cat->activo)
                        <span class="px-2 py-0.5 bg-green-100 text-green-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Activo</span>
                    @else
                        <span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-bold rounded-full uppercase tracking-wider">Oculto</span>
                    @endif
                </div>
                
                <p class="text-xs text-gray-500 line-clamp-2 min-h-[32px]" id="desc_txt_{{ $cat->id }}">{{ $cat->descripcion ?? 'Sin descripción detallada registrada.' }}</p>
                
                <div class="mt-4 bg-gray-50 border rounded-lg p-2.5 flex items-center justify-between text-xs">
                    <span class="text-gray-500 font-medium">Artículos vinculados:</span>
                    <span class="font-black text-gray-800 text-sm">{{ $cat->productos_count }} un.</span>
                </div>
            </div>

            <div class="mt-6 pt-3 border-t flex gap-2 justify-end">
                <form action="{{ route('admin.categorias.estado', $cat->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-bold px-3 py-1.5 rounded-lg border transition {{ $cat->activo ? 'bg-amber-50 hover:bg-amber-100 text-amber-700 border-amber-200' : 'bg-green-50 hover:bg-green-100 text-green-700 border-green-200' }}">
                        {{ $cat->activo ? '👁️ Ocultar' : '👁️ Mostrar' }}
                    </button>
                </form>

                <button onclick="abrirEditarModal({{ $cat->id }}, '{{ addslashes($cat->nombre) }}', '{{ addslashes($cat->descripcion) }}')" class="text-xs font-bold px-3 py-1.5 bg-gray-100 hover:bg-gray-200 border text-gray-700 rounded-lg transition">
                    📝 Editar
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center bg-white border-2 border-dashed p-12 rounded-2xl text-gray-400">
            No existen categorías registradas en el sistema.
        </div>
    @endforelse
</div>

<div id="crearModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-base">Registrar Nueva Categoría</h3>
            <button onclick="cerrarCrearModal()" class="text-gray-400 text-2xl font-bold hover:text-gray-600 focus:outline-none">&times;</button>
        </div>
        <form action="{{ route('admin.categorias.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre de la Categoría *</label>
                    <input type="text" name="nombre" required placeholder="Ej: Cascos de Seguridad" class="w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Descripción Breve</label>
                    <textarea name="descripcion" rows="3" placeholder="Describe los artículos que agrupa esta categoría..." class="w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-2 text-xs">
                <button type="button" onclick="cerrarCrearModal()" class="px-4 py-2 bg-gray-200 rounded-lg font-medium text-gray-800 hover:bg-gray-300 transition">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition shadow-sm">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<div id="editarModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-base">Corregir Datos de Categoría</h3>
            <button onclick="cerrarEditarModal()" class="text-gray-400 text-2xl font-bold hover:text-gray-600 focus:outline-none">&times;</button>
        </div>
        <form id="editarForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre de la Categoría *</label>
                    <input type="text" id="edit_nombre" name="nombre" required class="w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Descripción Breve</label>
                    <textarea id="edit_descripcion" name="descripcion" rows="3" class="w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-blue-500"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-2 text-xs">
                <button type="button" onclick="cerrarEditarModal()" class="px-4 py-2 bg-gray-200 rounded-lg font-medium text-gray-800 hover:bg-gray-300 transition">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg transition shadow-sm">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirCrearModal() {
        document.getElementById('crearModal').classList.remove('hidden');
    }
    function cerrarCrearModal() {
        document.getElementById('crearModal').classList.add('hidden');
    }

    function abrirEditarModal(id, nombre, descripcion) {
        document.getElementById('editarForm').action = `/categorias/${id}`;
        document.getElementById('edit_nombre').value = nombre;
        document.getElementById('edit_descripcion').value = descripcion;
        document.getElementById('editarModal').classList.remove('hidden');
    }
    function cerrarEditarModal() {
        document.getElementById('editarModal').classList.add('hidden');
    }
</script>
@endsection