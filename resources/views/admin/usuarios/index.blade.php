@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Control de Accesos y Usuarios</h2>
        <p class="text-gray-500 text-sm">Gestiona al personal operativo y a los clientes registrados en E-Sports.</p>
    </div>
    
    <div class="flex flex-wrap items-center gap-3">
        <form action="{{ route('admin.usuarios.index') }}" method="GET" class="flex shadow-sm rounded-lg">
            <select name="filtro_rol" onchange="this.form.submit()" class="border-gray-300 rounded-l-lg border-r-0 text-sm focus:ring-blue-500 py-2">
                <option value="default" {{ !request()->has('filtro_rol') || request('filtro_rol') == 'default' ? 'selected' : '' }}>Personal y Cajeros (Por Defecto)</option>
                <option value="todos" {{ request('filtro_rol') == 'todos' ? 'selected' : '' }}>Todos los Usuarios</option>
                @foreach($roles as $rol)
                    <option value="{{ $rol->id }}" {{ request('filtro_rol') == $rol->id ? 'selected' : '' }}>
                        Solo {{ ucfirst($rol->nombre) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-200 px-3 rounded-r-lg border border-gray-300 border-l-0 hover:bg-gray-300 transition">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
        </form>

        <button onclick="abrirModalUsuario()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow flex items-center transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Nuevo Usuario
        </button>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($usuarios as $user)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between transition-opacity {{ $user->activo ? 'opacity-100' : 'opacity-60 grayscale' }}">
            
            <div class="p-5">
                <div class="flex justify-between items-start mb-4">
                    @if($user->rol->nombre === 'admin')
                        <span class="px-2.5 py-1 bg-red-100 text-red-800 text-xs font-bold rounded-lg">Admin</span>
                    @elseif($user->rol->nombre === 'personal')
                        <span class="px-2.5 py-1 bg-purple-100 text-purple-800 text-xs font-bold rounded-lg">Logística</span>
                    @elseif($user->rol->nombre === 'cajero')
                        <span class="px-2.5 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-lg">Caja</span>
                    @else
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-lg">Cliente</span>
                    @endif

                    @if($user->activo)
                        <span class="flex items-center text-xs font-semibold text-green-600"><span class="w-2 h-2 rounded-full bg-green-500 mr-1 animate-pulse"></span> Activo</span>
                    @else
                        <span class="flex items-center text-xs font-semibold text-red-600"><span class="w-2 h-2 rounded-full bg-red-500 mr-1"></span> Baja</span>
                    @endif
                </div>

                <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $user->persona->nombre }} {{ $user->persona->apellidos }}</h3>
                <p class="text-sm font-semibold text-gray-700 mt-1">@ {{ $user->username }}</p>
                <p class="text-xs text-blue-600 mt-1 hover:underline">{{ $user->email }}</p>
                
                <div class="mt-4 pt-3 border-t text-xs text-gray-500 space-y-1">
                    <p><strong>C.I.:</strong> {{ $user->persona->ci ?? 'No registrado' }}</p>
                    <p><strong>Telf:</strong> {{ $user->persona->telefono ?? 'No registrado' }}</p>
                    <p><strong>Último Acceso:</strong> {{ $user->ultimo_login ? $user->ultimo_login->diffForHumans() : 'Nunca ingresó' }}</p>
                </div>
            </div>

            <div class="bg-gray-50 p-3 border-t flex space-x-2">
                <button onclick="abrirModalUsuario({{ $user->toJson() }})" class="flex-1 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold py-1.5 px-3 rounded text-xs transition shadow-sm">
                    Editar Datos
                </button>
                
                <form action="{{ route('admin.usuarios.estado', $user->id) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    @if($user->activo)
                        <button type="submit" onclick="return confirm('¿Dar de baja a este usuario? Ya no podrá acceder al sistema.')" class="w-full bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold py-1.5 px-3 rounded text-xs transition shadow-sm">
                            Dar de Baja
                        </button>
                    @else
                        <button type="submit" onclick="return confirm('¿Reactivar el acceso de este usuario?')" class="w-full bg-green-50 hover:bg-green-100 text-green-700 border border-green-200 font-bold py-1.5 px-3 rounded text-xs transition shadow-sm">
                            Activar
                        </button>
                    @endif
                </form>
            </div>
            
        </div>
    @empty
        <div class="col-span-full py-12 text-center bg-white rounded-xl border shadow-sm">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <p class="text-gray-500 font-medium">No se encontraron usuarios bajo este filtro.</p>
        </div>
    @endforelse
</div>

<div id="modalUsuario" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center overflow-y-auto transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-4 my-8 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50 sticky top-0">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Alta de Nuevo Usuario</h3>
            <button onclick="cerrarModalUsuario()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold transition">&times;</button>
        </div>

        <form id="usuarioForm" action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 max-h-[70vh] overflow-y-auto">
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-gray-500 uppercase border-b pb-2">Identidad Civil</h4>
                    <div>
                        <label class="block text-xs font-bold text-gray-700">Nombres *</label>
                        <input type="text" id="nombre" name="nombre" required class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos" required class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700">C.I.</label>
                            <input type="text" id="ci" name="ci" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700">Teléfono</label>
                            <input type="text" id="telefono" name="telefono" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="space-y-4 bg-gray-50 p-4 rounded-lg border">
                    <h4 class="text-xs font-bold text-gray-500 uppercase border-b pb-2">Credenciales Sistema</h4>
                    <div>
                        <label class="block text-xs font-bold text-gray-700">Rol Operativo *</label>
                        <select id="rol_id" name="rol_id" required class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500 bg-white">
                            <option value="">Seleccione un rol...</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700">Correo Electrónico *</label>
                        <input type="email" id="email" name="email" required placeholder="correo@ejemplo.com" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500 bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700">Username *</label>
                        <input type="text" id="username" name="username" required placeholder="usuario123" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500 bg-white">
                    </div>
                    <div>
                        <label id="labelPassword" class="block text-xs font-bold text-gray-700">Contraseña Temporal *</label>
                        <input type="password" id="password" name="password" required placeholder="Mínimo 6 caracteres" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-blue-500 bg-white">
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalUsuario()" class="px-4 py-2 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 rounded font-medium text-sm shadow-sm transition">Cancelar</button>
                <button type="submit" id="btnSubmit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold text-sm shadow transition">Registrar Cuenta</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirModalUsuario(user = null) {
        const modal = document.getElementById('modalUsuario');
        const form = document.getElementById('usuarioForm');
        const method = document.getElementById('formMethod');
        const title = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');
        const passInput = document.getElementById('password');
        const labelPass = document.getElementById('labelPassword');

        if (user) {
            // MODO EDICIÓN
            title.innerText = 'Editar Datos de Usuario';
            btnSubmit.innerText = 'Guardar Cambios';
            form.action = `/usuarios/${user.id}`;
            method.value = 'PUT';

            // Desplegar datos (Carga desde objeto JSON anidado)
            document.getElementById('nombre').value = user.persona.nombre;
            document.getElementById('apellidos').value = user.persona.apellidos;
            document.getElementById('ci').value = user.persona.ci || '';
            document.getElementById('telefono').value = user.persona.telefono || '';
            document.getElementById('rol_id').value = user.rol_id;
            document.getElementById('email').value = user.email;
            document.getElementById('username').value = user.username;

            // La contraseña es opcional al editar
            passInput.required = false;
            passInput.value = '';
            passInput.placeholder = 'Dejar en blanco para no cambiar';
            labelPass.innerText = 'Nueva Contraseña (Opcional)';
        } else {
            // MODO CREACIÓN
            title.innerText = 'Alta de Nuevo Usuario';
            btnSubmit.innerText = 'Registrar Cuenta';
            form.action = `{{ route('admin.usuarios.store') }}`;
            method.value = 'POST';
            
            form.reset();
            passInput.required = true;
            passInput.placeholder = 'Mínimo 6 caracteres';
            labelPass.innerText = 'Contraseña Temporal *';
        }

        modal.classList.remove('hidden');
    }

    function cerrarModalUsuario() {
        document.getElementById('modalUsuario').classList.add('hidden');
    }
</script>
@endsection