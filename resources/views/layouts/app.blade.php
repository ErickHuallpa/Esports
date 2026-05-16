<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Sports Store</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 font-sans antialiased flex flex-col min-h-screen">

    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-blue-600">
                E-SPORTS
            </div>
            
            <div class="hidden md:flex space-x-6 items-center">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 font-medium">Inicio</a>
                
                @auth
                    @if(auth()->user()->tipo_usuario === 'admin')
                        <div class="border-l border-gray-300 h-6 mx-2"></div>
                        <span class="text-xs font-bold text-red-600 uppercase tracking-wider">Gestión Admin:</span>
                        <a href="#" class="text-gray-700 hover:text-red-600 font-medium text-sm">Proveedores</a>
                        <a href="#" class="text-gray-700 hover:text-red-600 font-medium text-sm">Productos</a>
                        <a href="#" class="text-gray-700 hover:text-red-600 font-medium text-sm">Ofertas</a>
                        <a href="#" class="text-gray-700 hover:text-red-600 font-medium text-sm">Inventario</a>
                        <a href="#" class="text-gray-700 hover:text-red-600 font-medium text-sm">Órdenes</a>
                    @else
                        <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Lo Nuevo</a>
                        <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Categorías</a>
                        <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Mis Pedidos</a>
                    @endif
                @else
                    <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Lo Nuevo</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Categorías</a>
                    <a href="#" class="text-gray-700 hover:text-blue-600 font-medium">Ofertas</a>
                @endauth
            </div>

            <div class="flex items-center space-x-4">
                @guest
                    @if(!\App\Models\Administrador::exists())
                        <a href="{{ route('admin.register.form') }}" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-500 shadow animate-pulse">
                            Configurar Sistema
                        </a>
                    @endif

                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm">
                        Iniciar Sesión
                    </a>
                    <a href="#" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500 shadow">
                        Registrarse
                    </a>
                @endguest

                @auth
                    <div class="flex items-center space-x-3">
                        <span class="text-sm font-medium text-gray-700">
                            Hola, <span class="{{ auth()->user()->tipo_usuario === 'admin' ? 'text-red-600 font-bold' : 'text-blue-600' }}">{{ auth()->user()->username }}</span>
                        </span>
                        
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 shadow transition">
                                Cerrar Sesión
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    <main class="container mx-auto px-6 py-8 flex-grow">
        @if(session('success'))
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

</body>
</html>