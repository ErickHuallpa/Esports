@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white border border-gray-200 shadow-xl rounded-2xl p-8 my-6">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Configuración Inicial del Sistema</h1>
        <p class="text-gray-500 mt-2">Registra tus datos personales y de cuenta para inicializar el perfil Administrador.</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm">
            <ul class="list-disc pl-5 text-sm font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.register.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Datos Personales
                </h3>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nombre(s) *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Apellidos *</label>
                    <input type="text" name="apellidos" value="{{ old('apellidos') }}" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Cédula de Identidad (C.I.) *</label>
                        <input type="text" name="ci" value="{{ old('ci') }}" required placeholder="Ej: 8344122" class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Teléfono / Celular</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Fecha de Nacimiento</label>
                    <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Dirección de Domicilio</label>
                    <textarea name="direccion" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('direccion') }}</textarea>
                </div>
            </div>

            <div class="space-y-4 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Credenciales de Acceso
                </h3>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nombre de Usuario (Nickname) *</label>
                    <input type="text" name="username" value="{{ old('username') }}" required placeholder="Ej: admin2026" class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Correo Electrónico Corporativo *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@esports.com" class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Contraseña de Administrador *</label>
                    <input type="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2.5 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-white">
                </div>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t flex justify-end">
            <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition duration-150 transform hover:-translate-y-0.5">
                Inicializar y Guardar Administrador
            </button>
        </div>
    </form>
</div>
@endsection