<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sports Store</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen relative overflow-x-hidden">

    @php 
        $cartItems = session('carrito', []);
        $totalItems = count($cartItems);
    @endphp

    <header class="bg-white shadow-md relative z-40">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            
            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="text-2xl font-black text-blue-600 tracking-wider">
                    E-SPORTS
                </a>
            </div>
            
            <div class="hidden md:flex space-x-6 items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 font-medium">Inicio</a>
                
                @auth
                    @if(auth()->user()->rol)
                        <div class="border-l border-gray-300 h-6 mx-2"></div>
                        
                        @if(auth()->user()->rol->nombre === 'admin')
                            <span class="text-xs font-bold text-red-600 uppercase tracking-wider bg-red-50 px-2 py-1 rounded">Panel Admin:</span>
                            <a href="{{ route('proveedores.index') }}" class="text-gray-700 hover:text-red-600 font-medium text-sm">Proveedores</a>
                            <a href="{{ route('productos.index') }}" class="text-gray-700 hover:text-red-600 font-medium text-sm">Productos</a>
                            <a href="#" class="text-gray-700 hover:text-red-600 font-medium text-sm">Ofertas</a>
                            <a href="#" class="text-gray-700 hover:text-red-600 font-medium text-sm">Usuarios</a>

                        @elseif(auth()->user()->rol->nombre === 'personal')
                            <span class="text-xs font-bold text-purple-600 uppercase tracking-wider bg-purple-50 px-2 py-1 rounded">Logística:</span>
                            <a href="#" class="text-gray-700 hover:text-purple-600 font-medium text-sm">Inventario</a>
                            <a href="#" class="text-gray-700 hover:text-purple-600 font-medium text-sm">Control Envíos</a>

                        @elseif(auth()->user()->rol->nombre === 'cajero')
                            <span class="text-xs font-bold text-green-600 uppercase tracking-wider bg-green-50 px-2 py-1 rounded">Caja:</span>
                            <a href="#" class="text-gray-700 hover:text-green-600 font-medium text-sm">Validar Pagos QR</a>
                            <a href="#" class="text-gray-700 hover:text-green-600 font-medium text-sm">Ventas Confirmadas</a>

                        @elseif(auth()->user()->rol->nombre === 'cliente')
                            <a href="#" class="text-gray-700 hover:text-blue-600 font-medium text-sm">Mis Pedidos</a>
                            <a href="#" class="text-gray-700 hover:text-blue-600 font-medium text-sm">Mis Reseñas</a>
                        @endif
                    @endif
                @else
                    <a href="#" class="text-gray-700 hover:text-blue-600 font-medium text-sm">Lo Nuevo</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600 font-medium text-sm">Ofertas</a>
                @endauth
            </div>

            <div class="flex items-center space-x-4">
                @guest
                    @if(!\App\Models\User::whereHas('rol', function($q){ $q->where('nombre', 'admin'); })->exists())
                        <a href="{{ route('admin.register.form') }}" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-500 shadow animate-pulse">
                            Configurar Sistema
                        </a>
                    @endif

                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm">Iniciar Sesión</a>
                    <a href="{{ route('cliente.register.form') }}" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500 shadow">Registrarse</a>
                @endguest

                @auth
                    <button onclick="toggleCart()" class="relative p-2 text-gray-600 hover:text-blue-600 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        @if($totalItems > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">{{ $totalItems }}</span>
                        @endif
                    </button>

                    <div class="flex items-center space-x-3 ml-4 border-l pl-4">
                        <span class="text-sm font-medium text-gray-700">
                            Hola, 
                            <span class="font-bold @if(auth()->user()->rol->nombre === 'admin') text-red-600 @elseif(auth()->user()->rol->nombre === 'personal') text-purple-600 @elseif(auth()->user()->rol->nombre === 'cajero') text-green-600 @else text-blue-600 @endif">
                                {{ auth()->user()->username }}
                            </span>
                        </span>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 text-xs font-semibold text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 shadow-sm transition">
                                Salir
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8 flex-grow">
        @if(session('success') && !session('open_cart'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-gray-800 text-white py-6 mt-10">
        <div class="container mx-auto px-6 text-center text-sm">
            &copy; {{ date('Y') }} E-Sports S.R.L. - Todos los derechos reservados.
        </div>
    </footer>

    <div id="cartOverlay" onclick="toggleCart()" class="fixed inset-0 backdrop-blur-sm bg-gray-900/30 z-40 hidden transition-opacity duration-300"></div>
    
    <div id="cartPanel" class="fixed top-0 right-0 w-full max-w-md h-full bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Tu Carrito
            </h2>
            <button onclick="toggleCart()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <div class="flex-grow p-6 overflow-y-auto bg-white">
            @php $totalPrice = 0; @endphp
            @if(count($cartItems) > 0)
                <ul class="space-y-4">
                    @foreach($cartItems as $id => $item)
                        @php $subtotal = $item['precio'] * $item['cantidad']; $totalPrice += $subtotal; @endphp
                        <li class="flex items-center space-x-4 border-b pb-4">
                            <div class="w-16 h-16 bg-gray-100 rounded flex-shrink-0 flex items-center justify-center overflow-hidden">
                                @if($item['imagen_url'])
                                    <img src="{{ asset('storage/' . $item['imagen_url']) }}" alt="" class="object-cover w-full h-full">
                                @else
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-sm font-bold text-gray-800">{{ $item['nombre'] }}</h4>
                                <p class="text-xs text-gray-500">
                                    @if($item['talla']) Talla: <span class="font-semibold text-gray-700">{{ $item['talla'] }}</span> @endif
                                    @if($item['color']) | Color: <span class="font-semibold text-gray-700">{{ $item['color'] }}</span> @endif
                                </p>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm font-bold text-blue-600">Bs {{ number_format($item['precio'], 2) }} <span class="text-gray-400 text-xs font-normal">x {{ $item['cantidad'] }}</span></span>
                                    
                                    <form action="{{ route('carrito.eliminar') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $id }}">
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs underline">Quitar</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="text-center text-gray-500 mt-10">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p>Tu carrito está vacío.</p>
                </div>
            @endif
        </div>

        <div class="p-6 bg-gray-50 border-t">
            <div class="flex justify-between items-center mb-4">
                <span class="text-lg font-bold text-gray-700">Total a Pagar:</span>
                <span class="text-2xl font-black text-gray-900">Bs {{ number_format($totalPrice, 2) }}</span>
            </div>
            <button class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded shadow-lg opacity-50 cursor-not-allowed" disabled>
                Confirmar Compra
            </button>
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