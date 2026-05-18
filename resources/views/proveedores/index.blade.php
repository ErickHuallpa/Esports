@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Directorio de Proveedores</h2>
        <p class="text-gray-500 text-sm">Gestiona las empresas que suministran tus productos deportivos.</p>
    </div>
    <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nuevo Proveedor
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($proveedores as $prov)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
            <div class="p-5">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg font-bold text-gray-800">{{ $prov->nombre_empresa }}</h3>
                    @if($prov->activo)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Inactivo</span>
                    @endif
                </div>
                
                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <p><strong class="text-gray-700">Contacto:</strong> {{ $prov->contacto_nombre ?? 'No registrado' }}</p>
                    <p><strong class="text-gray-700">Teléfono:</strong> {{ $prov->telefono ?? 'No registrado' }}</p>
                    <p><strong class="text-gray-700">Email:</strong> {{ $prov->email ?? 'No registrado' }}</p>
                    <p><strong class="text-gray-700">Ubicación:</strong> {{ $prov->ciudad ?? 'N/A' }}, {{ $prov->pais ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-200 flex justify-end space-x-2 rounded-b-lg">
                <button onclick="openModal({{ $prov->toJson() }})" class="text-blue-600 hover:text-blue-800 text-sm font-semibold px-3 py-1 bg-blue-100 rounded transition">
                    Editar
                </button>
                <button onclick="openDeleteModal({{ $prov->id }}, '{{ $prov->nombre_empresa }}')" class="text-red-600 hover:text-red-800 text-sm font-semibold px-3 py-1 bg-red-100 rounded transition">
                    Eliminar
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white p-8 text-center rounded-lg border border-gray-200 shadow-sm">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            <p class="text-gray-500 text-lg">No hay proveedores registrados aún.</p>
        </div>
    @endforelse
</div>

<div id="proveedorModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Nuevo Proveedor</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <form id="proveedorForm" method="POST" action="{{ route('proveedores.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nombre de la Empresa *</label>
                    <input type="text" name="nombre_empresa" id="nombre_empresa" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Persona de Contacto</label>
                    <input type="text" name="contacto_nombre" id="contacto_nombre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <textarea name="direccion" id="direccion" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ciudad</label>
                    <input type="text" name="ciudad" id="ciudad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">País</label>
                    <input type="text" name="pais" id="pais" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-medium transition">Cancelar</button>
                <button type="submit" id="btnSubmit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold transition">Guardar Proveedor</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500">¿Estás seguro de que deseas eliminar al proveedor <strong id="deleteProvName" class="text-gray-800"></strong>?</h3>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-medium transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold transition">Sí, eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(proveedor = null) {
        const modal = document.getElementById('proveedorModal');
        const form = document.getElementById('proveedorForm');
        const method = document.getElementById('formMethod');
        const title = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');

        if (proveedor) {
            // Configuración en Modo Edición
            title.innerText = 'Editar Proveedor';
            btnSubmit.innerText = 'Actualizar Cambios';
            form.action = `/proveedores/${proveedor.id}`;
            method.value = 'PUT'; 

            // Carga de campos en base al JSON recibido
            document.getElementById('nombre_empresa').value = proveedor.nombre_empresa || '';
            document.getElementById('contacto_nombre').value = proveedor.contacto_nombre || '';
            document.getElementById('telefono').value = proveedor.telefono || '';
            document.getElementById('email').value = proveedor.email || '';
            document.getElementById('direccion').value = proveedor.direccion || '';
            document.getElementById('ciudad').value = proveedor.ciudad || '';
            document.getElementById('pais').value = proveedor.pais || '';
        } else {
            // Configuración en Modo Creación
            title.innerText = 'Nuevo Proveedor';
            btnSubmit.innerText = 'Guardar Proveedor';
            form.action = `{{ route('proveedores.store') }}`;
            method.value = 'POST';
            form.reset(); 
        }

        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('proveedorModal').classList.add('hidden');
    }

    function openDeleteModal(id, nombre) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        
        document.getElementById('deleteProvName').innerText = nombre;
        form.action = `/proveedores/${id}`;
        
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection