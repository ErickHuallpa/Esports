@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white border border-gray-200 shadow-xl rounded-2xl p-8 my-10">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-black text-gray-900 tracking-tight">Iniciar Sesión</h1>
        <p class="text-sm text-gray-500 mt-1">Ingresa tus credenciales para acceder a tu cuenta de E-Sports.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded shadow-sm text-sm font-semibold">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('login.store') }}" method="POST" class="space-y-4">
        @csrf
        
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase">Usuario o Correo Electrónico</label>
            <input type="text" name="login" value="{{ old('login') }}" required placeholder="usuario123 o correo@ejemplo.com" class="mt-1 block w-full rounded-lg border-gray-300 border p-3 shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase">Contraseña</label>
            <input type="password" name="password" required placeholder="••••••••" class="mt-1 block w-full rounded-lg border-gray-300 border p-3 shadow-sm text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="flex items-center justify-between pt-1">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-600">Recordar mi cuenta</label>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-bold py-3 rounded-xl shadow transition duration-150">
                Ingresar al Sistema
            </button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-500 border-t pt-4">
        ¿No tienes una cuenta? 
        <a href="{{ route('cliente.register.form') }}" class="text-blue-600 hover:underline font-semibold">Regístrate aquí</a>
    </div>
</div>
@endsection