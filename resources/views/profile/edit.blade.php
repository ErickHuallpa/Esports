@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mi Cuenta</h1>
        <p class="text-gray-500 text-sm">Gestiona tu información personal, foto de perfil y opciones de seguridad.</p>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        
        <div class="w-full md:w-1/4">
            <div class="bg-white rounded-2xl border shadow-sm p-4 sticky top-6">
                <div class="flex flex-col items-center space-y-3 mb-6 p-2 text-center border-b pb-4">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-black text-2xl uppercase overflow-hidden border-2 border-white shadow">
                        @if($user->foto_perfil)
                            <img src="{{ asset('storage/' . $user->foto_perfil) }}" class="w-full h-full object-cover" alt="Foto de perfil">
                        @else
                            {{ substr($user->persona->nombre, 0, 1) }}{{ substr($user->persona->apellidos, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 leading-tight">{{ $user->username }}</p>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50 px-2 py-0.5 rounded">{{ $user->rol->nombre }}</span>
                    </div>
                </div>

                <nav class="space-y-1">
                    <a href="{{ route('perfil.edit') }}" class="flex items-center px-4 py-2.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-bold transition">
                        👤 Datos Personales
                    </a>
                    <a href="{{ route('cliente.pedidos') }}" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-semibold transition">
                        📦 Mis Pedidos
                    </a>
                    <a href="{{ route('cliente.resenas') }}" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-semibold transition">
                        ⭐ Mis Reseñas
                    </a>
                </nav>
            </div>
        </div>

        <div class="w-full md:w-3/4 space-y-8">
            
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-800">Información del Perfil</h2>
                    <p class="text-xs text-gray-500 mt-1">Actualiza tu foto y tus datos para evitar problemas en tus envíos.</p>
                </div>
                
                <div class="p-6">
                    <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-6 p-4 bg-gray-50 border rounded-xl flex items-center space-x-4">
                            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center overflow-hidden border">
                                @if($user->foto_perfil)
                                    <img src="{{ asset('storage/' . $user->foto_perfil) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cambiar Foto de Perfil</label>
                                <input type="file" name="foto_perfil" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white file:cursor-pointer hover:file:bg-blue-700 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Cédula de Identidad (C.I.)</label>
                                <input type="text" name="ci" value="{{ old('ci', $user->persona->ci) }}" required class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500 bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombre de Usuario</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nombres</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $user->persona->nombre) }}" required class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Apellidos</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $user->persona->apellidos) }}" required class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Correo Electrónico</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Teléfono Móvil</label>
                                <input type="text" name="telefono" value="{{ old('telefono', $user->persona->telefono) }}" class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->persona->fecha_nacimiento)->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Dirección Habitual</label>
                                <input type="text" name="direccion" value="{{ old('direccion', $user->persona->direccion) }}" class="w-full rounded-lg border-gray-300 p-2.5 text-sm focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="bg-gray-900 hover:bg-black text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition">
                                Guardar Información
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-800">Seguridad de la Cuenta</h2>
                </div>
                <div class="p-6">
                    <form action="{{ route('perfil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 max-w-md">
                            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Contraseña Actual *</label><input type="password" name="current_password" required class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500"></div>
                            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nueva Contraseña *</label><input type="password" name="password" required class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500"></div>
                            <div><label class="block text-xs font-bold text-gray-700 uppercase mb-1">Confirmar *</label><input type="password" name="password_confirmation" required class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500"></div>
                        </div>
                        <div class="mt-6"><button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm transition">Actualizar Contraseña</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection