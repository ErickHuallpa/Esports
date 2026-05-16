@extends('layouts.app')

@section('content')
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.4.0/model-viewer.min.js"></script>

<style>
    @keyframes floating {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-15px); }
        100% { transform: translateY(0px); }
    }
    .animate-floating {
        animation: floating 4s ease-in-out infinite;
    }
</style>

<div class="max-w-5xl mx-auto mt-10 bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
    <div class="grid grid-cols-1 md:grid-cols-2 min-h-[500px]">
        
        <div class="p-8 md:p-12 flex flex-col justify-center bg-white z-10">
            <div class="text-left mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900">Iniciar Sesión</h1>
                <p class="text-gray-500 mt-2">Accede a tu cuenta de E-SPORTS</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r shadow-sm">
                    <ul class="list-disc pl-5 text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700">Correo Electrónico</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus 
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm border p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>
                
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                    <input type="password" name="password" id="password" required 
                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm border p-3 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition duration-200 ease-in-out transform hover:-translate-y-0.5">
                        Ingresar al Sistema
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-gradient-to-br from-blue-50 to-gray-200 hidden md:flex items-center justify-center p-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-30" style="background-image: radial-gradient(circle at 2px 2px, #9ca3af 1px, transparent 0); background-size: 24px 24px;"></div>
            
            <div class="w-full h-full min-h-[400px] animate-floating z-10">
                <model-viewer 
                    src="{{ asset('3dmodels/worldcup.glb') }}" 
                    auto-rotate 
                    rotation-per-second="20deg"
                    camera-controls 
                    shadow-intensity="1.5" 
                    shadow-softness="1"
                    environment-image="neutral"
                    exposure="1.2"
                    class="w-full h-full"
                    style="--poster-color: transparent;"
                    alt="Modelo 3D E-Sports">
                </model-viewer>
            </div>
        </div>

    </div>
</div>
@endsection