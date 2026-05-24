<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sports Store</title>
    @vite('resources/css/app.css')
    <style>
    .group:hover .group-hover\:visible {
        visibility: visible;
    }

    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }

    .group:hover .group-hover\:translate-y-0 {
        transform: translateY(0);
    }
    </style>
</head>

<body class="bg-gray-50 font-sans antialiased flex flex-col min-h-screen relative overflow-x-hidden">

    @php
    $cartItems = session('carrito', []);
    $totalItems = count($cartItems);
    @endphp

    <header class="bg-white shadow-sm relative z-40 border-b border-gray-100">
        <nav class="container mx-auto px-6 py-3.5 flex justify-between items-center">

            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}"
                    class="text-2xl font-black text-blue-600 tracking-wider flex items-center">
                    <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    E-SPORTS
                </a>
            </div>

            <div class="hidden lg:flex space-x-2 items-center">
                <a href="{{ route('home') }}"
                    class="px-3 py-2 text-gray-700 hover:text-blue-600 hover:bg-blue-50 rounded-lg font-bold text-sm transition">Catálogo</a>

                @auth
                @if(auth()->user()->rol->nombre === 'admin')
                <div class="border-l border-gray-300 h-5 mx-2"></div>

                <!-- Gestión General -->
                <div class="relative group">
                    <button
                        class="px-3 py-2 text-gray-700 hover:text-red-600 hover:bg-red-50 rounded-lg font-bold text-sm transition flex items-center">
                        📚 Gestión General ▾
                    </button>
                    <div
                        class="absolute left-0 mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="p-2 space-y-1">
                            <a href="{{ route('productos.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-md">Artículos
                                y Productos</a>
                            <a href="{{ route('admin.cupones.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-md">Cupones</a>
                            <a href="{{ route('admin.ofertas.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-md">Ofertas</a>
                            <a href="{{ route('admin.categorias.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-md">Categorías</a>
                            <a href="{{ route('proveedores.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-md">Proveedores</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="{{ route('admin.usuarios.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-red-50 hover:text-red-700 rounded-md">Control
                                de Usuarios</a>
                        </div>
                    </div>
                </div>

                <!-- Logística -->
                <div class="relative group">
                    <button
                        class="px-3 py-2 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg font-bold text-sm transition flex items-center">
                        📦 Logística ▾
                    </button>
                    <div
                        class="absolute left-0 mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="p-2 space-y-1">
                            <a href="{{ route('personal.inventario.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-purple-50 hover:text-purple-700 rounded-md">Ingresos
                                a Almacén</a>
                            <a href="{{ route('personal.envios.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-purple-50 hover:text-purple-700 rounded-md">Control
                                de Despachos</a>
                        </div>
                    </div>
                </div>

                <!-- Finanzas -->
                <div class="relative group">
                    <button
                        class="px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg font-bold text-sm transition flex items-center">
                        💰 Finanzas ▾
                    </button>
                    <div
                        class="absolute left-0 mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="p-2 space-y-1">
                            <a href="{{ route('cajero.pos.index') }}"
                                class="block px-3 py-2 text-sm font-bold text-green-700 bg-green-50 rounded-md">POS
                                (Venta Directa)</a>
                            <a href="{{ route('admin.pagos.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700 rounded-md">Validar
                                Pagos QR</a>
                            <a href="{{ route('cajero.ventas.index') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-green-50 hover:text-green-700 rounded-md">Ventas
                                Históricas</a>
                        </div>
                    </div>
                </div>

                <!-- REPORTES Y ESTADÍSTICAS -->
                <div class="relative group">
                    <button
                        class="px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg font-bold text-sm transition flex items-center">
                        📊 Reportes ▾
                    </button>
                    <div
                        class="absolute left-0 mt-0 w-56 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="p-2 space-y-1">
                            <a href="{{ route('reportes.index') }}"
                                class="block px-3 py-2 text-sm font-bold text-indigo-700 bg-indigo-50 rounded-md transition">
                                📈 Dashboard General
                            </a>
                        </div>
                    </div>
                </div>

                @elseif(auth()->user()->rol->nombre === 'personal')
                <div class="border-l border-gray-300 h-5 mx-2"></div>
                <a href="{{ route('personal.inventario.index') }}"
                    class="px-3 py-2 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg font-bold text-sm transition">Ingreso
                    Almacén</a>
                <a href="{{ route('personal.envios.index') }}"
                    class="px-3 py-2 text-gray-700 hover:text-purple-600 hover:bg-purple-50 rounded-lg font-bold text-sm transition">Despachos
                    y Envíos</a>

                <!-- Reportes para personal -->
                <div class="relative group">
                    <button
                        class="px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg font-bold text-sm transition flex items-center">
                        📊 Reportes ▾
                    </button>
                    <div
                        class="absolute left-0 mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="p-2 space-y-1">
                            <a href="{{ route('reportes.inventario-bajo-stock') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 rounded-md transition">
                                Control de Inventario
                            </a>
                            <a href="{{ route('reportes.ventas') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 rounded-md transition">
                                Productos en Almacén
                            </a>
                        </div>
                    </div>
                </div>

                @elseif(auth()->user()->rol->nombre === 'cajero')
                <div class="border-l border-gray-300 h-5 mx-2"></div>
                <a href="{{ route('cajero.pos.index') }}"
                    class="px-3 py-2 text-green-700 bg-green-50 hover:bg-green-100 rounded-lg font-bold text-sm transition">POS
                    / Venta Directa</a>
                <a href="{{ route('admin.pagos.index') }}"
                    class="px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg font-bold text-sm transition">Validar
                    Pagos QR</a>
                <a href="{{ route('cajero.ventas.index') }}"
                    class="px-3 py-2 text-gray-700 hover:text-green-600 hover:bg-green-50 rounded-lg font-bold text-sm transition">Registro
                    de Ventas</a>

                <!-- Reportes para cajero -->
                <div class="relative group">
                    <button
                        class="px-3 py-2 text-gray-700 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg font-bold text-sm transition flex items-center">
                        📊 Mis Reportes ▾
                    </button>
                    <div
                        class="absolute left-0 mt-0 w-48 bg-white border border-gray-100 rounded-xl shadow-lg opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="p-2 space-y-1">
                            <a href="{{ route('reportes.ventas', ['vendedor_id' => auth()->id()]) }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 rounded-md transition">
                                Mis Ventas
                            </a>
                            <a href="{{ route('reportes.productos-mas-vendidos') }}"
                                class="block px-3 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-indigo-700 rounded-md transition">
                                Productos Vendidos
                            </a>
                        </div>
                    </div>
                </div>
                @endif
                @else
                <div class="border-l border-gray-300 h-5 mx-2"></div>
                <a href="#" class="px-3 py-2 text-gray-600 hover:text-blue-600 font-bold text-sm transition">Lo
                    Nuevo</a>
                <a href="#" class="px-3 py-2 text-gray-600 hover:text-blue-600 font-bold text-sm transition">Ofertas</a>
                @endauth
            </div>

            <div class="flex items-center space-x-4">
                @guest
                @if(!\App\Models\User::whereHas('rol', function($q){ $q->where('nombre', 'admin'); })->exists())
                <a href="{{ route('admin.register.form') }}"
                    class="px-4 py-2 text-sm font-bold text-white bg-red-600 rounded-lg hover:bg-red-700 shadow animate-pulse">
                    Configurar Sistema
                </a>
                @endif
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-bold text-sm">Iniciar
                    Sesión</a>
                <a href="{{ route('cliente.register.form') }}"
                    class="px-4 py-2 text-sm font-bold text-white bg-gray-900 rounded-lg hover:bg-black shadow transition">Registrarse</a>
                @endguest

                @auth
                <button onclick="toggleCart()"
                    class="relative p-2 text-gray-600 hover:text-blue-600 transition bg-gray-50 rounded-full hover:bg-blue-50">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    @if($totalItems > 0)
                    <span
                        class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-[10px] font-black leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-500 border-2 border-white rounded-full">{{ $totalItems }}</span>
                    @endif
                </button>

                <div class="relative ml-2 pl-4 border-l border-gray-200 group">
                    <button
                        class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 focus:outline-none py-1">

                        <div
                            class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center text-blue-700 font-black text-sm uppercase overflow-hidden shadow-sm border border-gray-200">
                            @if(auth()->user()->foto_perfil)
                            <img src="{{ asset('storage/' . auth()->user()->foto_perfil) }}"
                                class="w-full h-full object-cover">
                            @else
                            {{ substr(auth()->user()->persona->nombre, 0, 1) }}{{ substr(auth()->user()->persona->apellidos, 0, 1) }}
                            @endif
                        </div>

                        <div class="text-left hidden md:block">
                            <span class="block text-[10px] text-gray-400 font-bold uppercase leading-none">Hola,
                                {{ auth()->user()->username }}</span>
                            <span class="block text-sm font-bold leading-none mt-1">Mi Cuenta ▾</span>
                        </div>
                    </button>

                    <div
                        class="absolute right-0 w-56 mt-0 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                        <div class="p-4 border-b border-gray-50 bg-gray-50/50 rounded-t-xl">
                            <p class="text-sm font-bold text-gray-800 line-clamp-1">
                                {{ auth()->user()->persona->nombre }} {{ auth()->user()->persona->apellidos }}</p>
                            <p class="text-[10px] text-gray-500 font-medium truncate">{{ auth()->user()->email }}</p>
                            <span
                                class="inline-block mt-2 text-[9px] font-black uppercase tracking-widest text-blue-600 bg-blue-100 px-2 py-0.5 rounded">{{ auth()->user()->rol->nombre }}</span>
                        </div>
                        <div class="p-2 space-y-1">
                            <a href="{{ route('perfil.edit') }}"
                                class="block px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-700 rounded-md transition">👤
                                Administrar Perfil</a>
                            <a href="{{ route('cliente.pedidos') }}"
                                class="block px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-700 rounded-md transition">📦
                                Mis Pedidos Logísticos</a>
                            <a href="{{ route('cliente.resenas') }}"
                                class="block px-3 py-2 text-sm font-semibold text-gray-600 hover:bg-blue-50 hover:text-blue-700 rounded-md transition">⭐
                                Mis Reseñas</a>
                        </div>
                        <div class="p-2 border-t border-gray-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md font-bold transition">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8 flex-grow">
        @if(session('success') && !session('open_cart'))
        <div class="mb-5 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl shadow-sm flex items-center"
            role="alert">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-5 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl shadow-sm flex items-center"
            role="alert">
            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-5 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-xl shadow-sm"
            role="alert">
            <strong class="font-bold text-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                Revisa los siguientes detalles:
            </strong>
            <ul class="list-disc pl-7 mt-2 text-sm font-medium space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-900 text-gray-400 py-8 mt-auto">
        <div
            class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center text-xs font-semibold">
            <div class="mb-4 md:mb-0">
                &copy; {{ date('Y') }} E-Sports S.R.L. Potosí, Bolivia. Todos los derechos reservados.
            </div>
            <div class="flex space-x-4">
                <a href="#" class="hover:text-white transition">Términos y Condiciones</a>
                <a href="#" class="hover:text-white transition">Políticas de Privacidad</a>
            </div>
        </div>
    </footer>

    <div id="cartOverlay" onclick="toggleCart()"
        class="fixed inset-0 backdrop-blur-sm bg-gray-900/40 z-40 hidden transition-opacity duration-300"></div>
    <div id="cartPanel"
        class="fixed top-0 right-0 w-full max-w-md h-full bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <h2 class="text-lg font-black text-gray-900 flex items-center tracking-tight">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
                Carrito de Compras
            </h2>
            <button onclick="toggleCart()"
                class="text-gray-400 hover:text-red-500 bg-gray-50 hover:bg-red-50 rounded-full p-2 transition">&times;</button>
        </div>

        <div class="flex-grow p-6 overflow-y-auto bg-gray-50">
            @php $totalPrice = 0; @endphp
            @if(count($cartItems) > 0)
            <ul class="space-y-3">
                @foreach($cartItems as $id => $item)
                @php $subtotal = $item['precio'] * $item['cantidad']; $totalPrice += $subtotal; @endphp
                <li class="flex items-center space-x-4 bg-white p-3 rounded-xl border border-gray-100 shadow-sm">
                    <div
                        class="w-16 h-16 bg-gray-50 rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden border border-gray-100">
                        @if($item['imagen_url'])
                        <img src="{{ asset('storage/' . $item['imagen_url']) }}" alt=""
                            class="object-cover w-full h-full">
                        @else
                        <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-xs font-bold text-gray-800 line-clamp-2 leading-tight">{{ $item['nombre'] }}
                        </h4>
                        <div class="flex justify-between items-end mt-2">
                            <span class="text-sm font-black text-blue-600">Bs {{ number_format($item['precio'], 2) }}
                                <span class="text-gray-400 text-[10px] font-bold ml-1">x
                                    {{ $item['cantidad'] }}</span></span>
                            <form action="{{ route('carrito.eliminar') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <button type="submit"
                                    class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-2 py-1 rounded text-[10px] font-bold transition">Quitar</button>
                            </form>
                        </div>
                    </div>
                </li>
                @endforeach
            </ul>
            @else
            <div class="h-full flex flex-col items-center justify-center text-center text-gray-400">
                <svg class="w-20 h-20 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="font-semibold text-sm">Aún no has agregado artículos.</p>
            </div>
            @endif
        </div>

        <div class="p-6 bg-white border-t border-gray-100 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <div class="flex justify-between items-center mb-4">
                <span class="text-sm font-bold text-gray-500 uppercase tracking-widest">Total a Pagar:</span>
                <span class="text-2xl font-black text-gray-900">Bs {{ number_format($totalPrice, 2) }}</span>
            </div>
            <a href="{{ route('checkout.form') }}"
                class="w-full block bg-gray-900 hover:bg-black text-white font-bold py-3.5 text-sm text-center rounded-xl shadow-lg transition {{ count($cartItems) == 0 ? 'opacity-50 pointer-events-none' : '' }}">
                Procesar y Pagar
            </a>
        </div>
    </div>

    <script>
    function toggleCart() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('cartOverlay');
        if (panel.classList.contains('translate-x-full')) {
            panel.classList.remove('translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            panel.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        }
    }
    @if(session('open_cart'))
    document.addEventListener("DOMContentLoaded", function() {
        toggleCart();
    });
    @endif
    </script>
</body>

</html>
