<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sports Store</title>
    @vite('resources/css/app.css')
    <style>
        /* Transiciones suaves para los menús desplegables */
        .group:hover .group-hover\:visible { visibility: visible; }
        .group:hover .group-hover\:opacity-100 { opacity: 1; }
        .group:hover .group-hover\:translate-y-0 { transform: translateY(0); }
    </style>
</head>

<body class="bg-[#f4f4f4] text-[#343c4c] font-sans antialiased flex flex-col min-h-screen relative overflow-x-hidden">

    @php
        $cartItems = session('carrito', []);
        $totalItems = count($cartItems);
    @endphp

    <header class="bg-[#343c4c] shadow-lg relative z-40 border-b-4 border-[#dc043c]">
        
        <div class="h-1.5 w-full bg-[#dcb47c]"></div>

        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">

            <div class="flex items-center space-x-4">
                <a href="{{ route('home') }}" class="flex items-center group transition">
                    <img src="{{ asset('logo/logo.png') }}" alt="Logo E-Sports" class="h-12 w-auto object-contain group-hover:scale-105 transition-transform drop-shadow-md">
                    <span class="text-2xl font-black text-[#f4f4f4] tracking-wider ml-3 group-hover:text-[#dcb47c] transition-colors">
                        E-SPORTS
                    </span>
                </a>
            </div>

            <div class="hidden lg:flex space-x-2 items-center">
                <a href="{{ route('home') }}" class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] rounded-lg font-bold text-sm transition-colors uppercase tracking-wider">
                    Catálogo
                </a>

                @auth
                    @if(auth()->user()->rol)
                        <div class="border-l-2 border-white/20 h-6 mx-3"></div>

                        @if(auth()->user()->rol->nombre === 'admin')
                            <span class="text-[10px] font-black text-white uppercase tracking-widest bg-[#dc043c] px-3 py-1.5 rounded shadow-sm border border-[#dc043c]/50">Admin</span>

                            <div class="relative group">
                                <button class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] rounded-lg font-bold text-sm transition-colors flex items-center uppercase tracking-wider">
                                    Gestión ▾
                                </button>
                                <div class="absolute left-0 mt-0 w-52 bg-white border-t-4 border-[#dcb47c] rounded-b-xl shadow-2xl opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                                    <div class="p-2 space-y-1">
                                        <a href="{{ route('productos.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">Artículos y Productos</a>
                                        <a href="{{ route('admin.cupones.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">Cupones</a>
                                        <a href="{{ route('admin.ofertas.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">Ofertas</a>
                                        <a href="{{ route('admin.categorias.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">Categorías</a>
                                        <a href="{{ route('proveedores.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">Proveedores</a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('admin.usuarios.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#dc043c] hover:text-white rounded-lg transition">Control de Usuarios</a>
                                    </div>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] rounded-lg font-bold text-sm transition-colors flex items-center uppercase tracking-wider">
                                    Logística ▾
                                </button>
                                <div class="absolute left-0 mt-0 w-52 bg-white border-t-4 border-[#0464a4] rounded-b-xl shadow-2xl opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                                    <div class="p-2 space-y-1">
                                        <a href="{{ route('personal.inventario.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">Ingresos a Almacén</a>
                                        <a href="{{ route('personal.envios.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">Control de Despachos</a>
                                    </div>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] rounded-lg font-bold text-sm transition-colors flex items-center uppercase tracking-wider">
                                    Finanzas ▾
                                </button>
                                <div class="absolute left-0 mt-0 w-52 bg-white border-t-4 border-[#dc043c] rounded-b-xl shadow-2xl opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                                    <div class="p-2 space-y-1">
                                        <a href="{{ route('cajero.pos.index') }}" class="block px-4 py-2.5 text-sm font-black text-[#dcb47c] bg-[#343c4c] rounded-lg hover:bg-[#dc043c] hover:text-white transition shadow-sm">POS (Venta Directa)</a>
                                        <a href="{{ route('admin.pagos.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#dc043c] rounded-lg transition">Validar Pagos QR</a>
                                        <a href="{{ route('cajero.ventas.index') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#dc043c] rounded-lg transition">Ventas Históricas</a>
                                    </div>
                                </div>
                            </div>

                            <div class="relative group">
                                <button class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] rounded-lg font-bold text-sm transition-colors flex items-center uppercase tracking-wider">
                                    Reportes ▾
                                </button>
                                <div class="absolute left-0 mt-0 w-52 bg-white border-t-4 border-[#dcb47c] rounded-b-xl shadow-2xl opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-200 z-50">
                                    <div class="p-2 space-y-1">
                                        <a href="{{ route('reportes.index') }}" class="block px-4 py-2.5 text-sm font-bold text-white bg-[#0464a4] hover:bg-[#343c4c] rounded-lg transition shadow-sm">
                                            📈 Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>

                        @elseif(auth()->user()->rol->nombre === 'personal')
                            <span class="text-[10px] font-black text-white uppercase tracking-widest bg-[#0464a4] px-3 py-1.5 rounded shadow-sm">Logística</span>
                            <a href="{{ route('personal.inventario.index') }}" class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] font-bold text-sm transition uppercase">Ingreso Almacén</a>
                            <a href="{{ route('personal.envios.index') }}" class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] font-bold text-sm transition uppercase">Despachos</a>

                        @elseif(auth()->user()->rol->nombre === 'cajero')
                            <span class="text-[10px] font-black text-[#343c4c] uppercase tracking-widest bg-[#dcb47c] px-3 py-1.5 rounded shadow-sm">Caja</span>
                            <a href="{{ route('cajero.pos.index') }}" class="px-4 py-2 bg-[#dc043c] text-white hover:bg-[#dcb47c] hover:text-[#343c4c] rounded-lg font-bold text-sm transition shadow-sm uppercase">Punto de Venta</a>
                            <a href="{{ route('admin.pagos.index') }}" class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] font-bold text-sm transition uppercase">Pagos QR</a>
                            <a href="{{ route('cajero.ventas.index') }}" class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] font-bold text-sm transition uppercase">Ventas</a>
                        @endif
                    @endif
                @else
                    <div class="border-l-2 border-white/20 h-6 mx-3"></div>
                    <a href="#" class="px-4 py-2 text-[#f4f4f4] hover:text-[#dcb47c] font-bold text-sm transition uppercase tracking-wider">Novedades</a>
                    <a href="#" class="px-4 py-2 text-[#dcb47c] hover:text-white font-bold text-sm transition uppercase tracking-wider">Ofertas</a>
                @endauth
            </div>

            <div class="flex items-center space-x-4">
                @guest
                    @if(!\App\Models\User::whereHas('rol', function($q){ $q->where('nombre', 'admin'); })->exists())
                        <a href="{{ route('admin.register.form') }}" class="px-5 py-2.5 text-sm font-black tracking-widest uppercase text-white bg-[#dc043c] rounded-lg hover:bg-[#0464a4] shadow-md animate-pulse transition">
                            Configurar Sistema
                        </a>
                    @endif
                    <a href="{{ route('login') }}" class="text-[#f4f4f4] hover:text-[#dcb47c] font-bold text-sm transition uppercase tracking-wider">Login</a>
                    <a href="{{ route('cliente.register.form') }}" class="px-5 py-2 text-sm font-bold text-[#343c4c] bg-[#dcb47c] rounded-md hover:bg-white shadow transition uppercase tracking-wider">Registro</a>
                @endguest

                @auth
                    <button onclick="toggleCart()" class="relative p-2.5 text-white bg-white/10 rounded-full hover:bg-[#dc043c] shadow-sm transition border border-white/20 hover:border-transparent">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        @if($totalItems > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-[11px] font-black leading-none text-[#343c4c] transform translate-x-1/4 -translate-y-1/4 bg-[#dcb47c] border-2 border-[#343c4c] rounded-full">{{ $totalItems }}</span>
                        @endif
                    </button>

                    <div class="relative ml-2 pl-4 border-l-2 border-white/20 group">
                        <button class="flex items-center space-x-3 text-[#f4f4f4] hover:text-[#dcb47c] focus:outline-none py-1 transition">
                            
                            <div class="w-10 h-10 bg-[#dcb47c] rounded-full flex items-center justify-center text-[#343c4c] font-black text-sm uppercase overflow-hidden shadow-inner border-2 border-[#dcb47c]">
                                @if(auth()->user()->foto_perfil)
                                    <img src="{{ asset('storage/' . auth()->user()->foto_perfil) }}" class="w-full h-full object-cover">
                                @else
                                    {{ substr(auth()->user()->persona->nombre, 0, 1) }}{{ substr(auth()->user()->persona->apellidos, 0, 1) }}
                                @endif
                            </div>

                            <div class="text-left hidden md:block">
                                <span class="block text-[10px] text-[#f4f4f4]/60 font-black uppercase tracking-widest leading-none">Hola, {{ auth()->user()->username }}</span>
                                <span class="block text-sm font-bold leading-none mt-1">Mi Cuenta ▾</span>
                            </div>
                        </button>

                        <div class="absolute right-0 w-64 mt-0 bg-white rounded-xl shadow-2xl border-t-4 border-[#0464a4] opacity-0 invisible translate-y-2 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 transition-all duration-300 z-50 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 bg-gray-50/80">
                                <p class="text-sm font-black text-[#343c4c] line-clamp-1 uppercase">{{ auth()->user()->persona->nombre }} {{ auth()->user()->persona->apellidos }}</p>
                                <p class="text-[11px] text-[#343c4c]/60 font-medium truncate mt-0.5">{{ auth()->user()->email }}</p>
                                <span class="inline-block mt-3 text-[9px] font-black uppercase tracking-widest text-white bg-[#0464a4] px-2 py-1 rounded shadow-sm">{{ auth()->user()->rol->nombre }}</span>
                            </div>
                            <div class="p-2 space-y-1 bg-white">
                                <a href="{{ route('perfil.edit') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">👤 Administrar Perfil</a>
                                <a href="{{ route('cliente.pedidos') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">📦 Mis Pedidos</a>
                                <a href="{{ route('cliente.resenas') }}" class="block px-4 py-2.5 text-sm font-semibold text-[#343c4c] hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-lg transition">⭐ Mis Reseñas</a>
                            </div>
                            <div class="p-2 border-t border-gray-100 bg-white">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-[#dc043c] hover:bg-[#dc043c]/10 rounded-lg font-black transition">
                                        CERRAR SESIÓN
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-10 flex-grow">
        @if(session('success') && !session('open_cart'))
        <div class="mb-5 bg-green-50 border-l-4 border-green-500 text-green-800 px-5 py-4 rounded shadow-sm flex items-center" role="alert">
            <svg class="w-6 h-6 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-5 bg-red-50 border-l-4 border-[#dc043c] text-[#dc043c] px-5 py-4 rounded shadow-sm flex items-center" role="alert">
            <svg class="w-6 h-6 mr-3 text-[#dc043c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-sm font-bold">{{ session('error') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="mb-5 bg-[#dcb47c]/10 border-l-4 border-[#dcb47c] text-[#343c4c] px-6 py-5 rounded shadow-sm" role="alert">
            <strong class="font-black text-sm flex items-center uppercase tracking-wider">
                <svg class="w-5 h-5 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Revisa los siguientes detalles:
            </strong>
            <ul class="list-disc pl-8 mt-3 text-sm font-medium space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-[#343c4c] text-[#f4f4f4] py-10 mt-auto border-t-4 border-[#0464a4]">
        <div class="container mx-auto px-6 flex flex-col md:flex-row justify-between items-center text-sm font-medium">
            <div class="mb-4 md:mb-0 flex items-center">
                <img src="{{ asset('logo/logo.png') }}" class="h-6 w-auto mr-3 grayscale hover:grayscale-0 transition opacity-80 hover:opacity-100" alt="Logo Footer">
                &copy; {{ date('Y') }} E-Sports S.R.L. Potosí, Bolivia. Todos los derechos reservados.
            </div>
            <div class="flex space-x-6">
                <a href="#" class="hover:text-[#dcb47c] transition-colors">Términos y Condiciones</a>
                <a href="#" class="hover:text-[#dcb47c] transition-colors">Políticas de Privacidad</a>
            </div>
        </div>
    </footer>

    <div id="cartOverlay" onclick="toggleCart()" class="fixed inset-0 backdrop-blur-sm bg-[#343c4c]/60 z-40 hidden transition-opacity duration-300"></div>
    <div id="cartPanel" class="fixed top-0 right-0 w-full max-w-md h-full bg-[#f4f4f4] shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-l-4 border-[#dcb47c]">
        
        <div class="px-6 py-6 border-b border-gray-200 bg-white flex justify-between items-center">
            <h2 class="text-xl font-black text-[#343c4c] flex items-center tracking-wide uppercase">
                <svg class="w-6 h-6 mr-3 text-[#dc043c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Carrito de Compras
            </h2>
            <button onclick="toggleCart()" class="text-[#343c4c]/50 hover:text-[#dc043c] bg-gray-100 hover:bg-red-50 rounded-full p-2 transition-colors">&times;</button>
        </div>

        <div class="flex-grow p-6 overflow-y-auto bg-[#f4f4f4]">
            @php $totalPrice = 0; @endphp
            @if(count($cartItems) > 0)
            <ul class="space-y-4">
                @foreach($cartItems as $id => $item)
                @php $subtotal = $item['precio'] * $item['cantidad']; $totalPrice += $subtotal; @endphp
                <li class="flex items-center space-x-4 bg-white p-4 rounded-xl border border-gray-200 shadow-sm relative group/item hover:border-[#0464a4] transition-colors">
                    <div class="w-20 h-20 bg-[#f4f4f4] rounded-lg flex-shrink-0 flex items-center justify-center overflow-hidden border border-gray-100">
                        @if($item['imagen_url'])
                        <img src="{{ asset('storage/' . $item['imagen_url']) }}" alt="" class="object-cover w-full h-full">
                        @else
                        <svg class="w-8 h-8 text-[#343c4c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        @endif
                    </div>
                    <div class="flex-grow">
                        <h4 class="text-sm font-bold text-[#343c4c] line-clamp-2 leading-tight pr-6">{{ $item['nombre'] }}</h4>
                        <div class="flex justify-between items-end mt-3">
                            <span class="text-base font-black text-[#dc043c]">Bs {{ number_format($item['precio'], 2) }}
                                <span class="text-[#343c4c]/40 text-xs font-bold ml-1">x {{ $item['cantidad'] }}</span>
                            </span>
                        </div>
                    </div>
                    <form action="{{ route('carrito.eliminar') }}" method="POST" class="absolute top-3 right-3 opacity-0 group-hover/item:opacity-100 transition-opacity">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">
                        <button type="submit" title="Quitar del carrito" class="text-[#dc043c] bg-red-50 hover:bg-[#dc043c] hover:text-white p-2 rounded-lg font-bold transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </li>
                @endforeach
            </ul>
            @else
            <div class="h-full flex flex-col items-center justify-center text-center text-[#343c4c]/40">
                <svg class="w-24 h-24 mb-4 text-[#343c4c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="font-bold text-base text-[#343c4c]">Aún no has agregado artículos.</p>
                <p class="text-xs mt-1">Explora el catálogo y aprovecha nuestras ofertas.</p>
            </div>
            @endif
        </div>

        <div class="p-6 bg-white border-t border-gray-200 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.05)]">
            <div class="flex justify-between items-center mb-5">
                <span class="text-sm font-black text-[#343c4c]/60 uppercase tracking-widest">Total a Pagar:</span>
                <span class="text-3xl font-black text-[#dc043c]">Bs {{ number_format($totalPrice, 2) }}</span>
            </div>
            <a href="{{ route('checkout.form') }}" class="w-full block bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-wider py-4 text-center rounded-xl shadow-lg transition-colors {{ count($cartItems) == 0 ? 'opacity-50 pointer-events-none' : '' }}">
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
        document.addEventListener("DOMContentLoaded", function () {
            toggleCart();
        });
        @endif
    </script>
</body>
</html>