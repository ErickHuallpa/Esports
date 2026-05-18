@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto bg-white border border-gray-200 shadow-xl rounded-2xl p-8 my-4">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Crea tu Cuenta de Comprador</h1>
        <p class="text-sm text-gray-500 mt-1">Regístrate para gestionar tu carrito de compras e interactuar con simulaciones 3D.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded shadow-sm text-sm">
            <ul class="list-disc pl-5 font-semibold">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('cliente.register.store') }}" method="POST" class="space-y-4">
        @csrf
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase">Apellidos *</label>
                <input type="text" name="apellidos" value="{{ old('apellidos') }}" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase">C.I. (Opcional)</label>
                <input type="text" name="ci" value="{{ old('ci') }}" class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase">Teléfono / Celular</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase">Nombre de Usuario *</label>
            <input type="text" name="username" value="{{ old('username') }}" required placeholder="usuario123" class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase">Correo Electrónico *</label>
            <input type="email" name="email" value="{{ old('email') }}" required placeholder="ejemplo@correo.com" class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase">Contraseña *</label>
                <input type="password" name="password" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase">Confirmar Contraseña *</label>
                <input type="password" name="password_confirmation" required class="mt-1 block w-full rounded-lg border-gray-300 border p-2 shadow-sm text-sm">
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-xl shadow transition duration-150">
                Finalizar Registro
            </button>
        </div>
    </form>
</div>
@endsection