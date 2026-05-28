@extends('layouts.app')

@section('content')
<style>
    .auth-wrapper {
        position: relative;
        overflow: hidden;
        min-height: 550px;
        height: 80vh;
        max-height: 600px;
    }
    
    .form-container {
        position: absolute;
        top: 0;
        height: 100%;
        transition: all 0.7s ease-in-out;
    }
    .sign-in-container {
        left: 0;
        width: 50%;
        z-index: 2;
    }
    .sign-up-container {
        left: 0;
        width: 50%;
        opacity: 0;
        z-index: 1;
    }
    .overlay-container {
        position: absolute;
        top: 0;
        left: 50%;
        width: 50%;
        height: 100%;
        overflow: hidden;
        transition: transform 0.7s ease-in-out;
        z-index: 100;
    }
    .overlay {
        position: relative;
        left: -100%;
        height: 100%;
        width: 200%;
        transform: translateX(0);
        transition: transform 0.7s ease-in-out;
    }
    .overlay-panel {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 0 40px;
        text-align: center;
        top: 0;
        height: 100%;
        width: 50%;
        transform: translateX(0);
        transition: transform 0.7s ease-in-out;
    }
    .overlay-left {
        transform: translateX(-20%);
    }
    .overlay-right {
        right: 0;
        transform: translateX(0);
    }
    .auth-wrapper.right-panel-active .sign-in-container {
        transform: translateX(100%);
        opacity: 0;
    }
    .auth-wrapper.right-panel-active .sign-up-container {
        transform: translateX(100%);
        opacity: 1;
        z-index: 5;
        animation: show 0.7s;
    }
    .auth-wrapper.right-panel-active .overlay-container {
        transform: translateX(-100%);
    }
    .auth-wrapper.right-panel-active .overlay {
        transform: translateX(50%);
    }
    .auth-wrapper.right-panel-active .overlay-left {
        transform: translateX(0);
    }
    .auth-wrapper.right-panel-active .overlay-right {
        transform: translateX(20%);
    }
    @keyframes show {
        0%, 49.99% { opacity: 0; z-index: 1; }
        50%, 100% { opacity: 1; z-index: 5; }
    }
    @media (max-width: 1024px) {
        .auth-wrapper { min-height: auto; height: auto; max-height: none; display: flex; flex-direction: column; }
        .form-container { position: static; width: 100%; opacity: 1; transform: none !important; animation: none !important; height: auto; padding: 20px 0;}
        .overlay-container { display: none; }
        .sign-up-container { display: none; }
        .auth-wrapper.right-panel-active .sign-up-container { display: block; }
        .auth-wrapper.right-panel-active .sign-in-container { display: none; }
    }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f4; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #dcb47c; border-radius: 10px; }
</style>

<div class="relative w-full min-h-[calc(100vh-160px)] flex justify-center items-center overflow-hidden -mx-6 px-6 -mt-8 pt-8">
    
    <div class="absolute inset-x-0 bottom-0 w-full h-[250px] md:h-[400px] pointer-events-none z-0" 
         style="background-image: url('{{ asset('img/cesped.png') }}'); background-position: bottom center; background-repeat: repeat-x; background-size: auto 100%; opacity: 1;">
    </div>
    
    <div class="absolute inset-0 bg-gradient-to-b from-[#f4f4f4] via-[#f4f4f4]/60 to-transparent pointer-events-none z-0"></div>

    <div class="relative z-10 auth-wrapper bg-white rounded-3xl shadow-[0_20px_50px_rgba(52,60,76,0.3)] w-full max-w-5xl" id="auth-wrapper">
        
        <div class="form-container sign-up-container">
            <form action="{{ route('cliente.register.store') }}" method="POST" class="h-full flex flex-col justify-center px-8 lg:px-14 py-4">
                @csrf
                <div class="text-center mb-4">
                    <h1 class="text-2xl lg:text-3xl font-black text-[#343c4c] tracking-tight">Crear Cuenta</h1>
                    <div class="h-1 w-12 bg-[#0464a4] mx-auto mt-2 rounded-full"></div>
                </div>

                <div class="grid grid-cols-2 gap-3 overflow-y-auto h-[60%] custom-scrollbar pr-2">
                    
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Usuario *</label>
                        <input type="text" name="username" value="{{ old('username') }}" required class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Nombres *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" required class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">C.I. *</label>
                        <input type="text" name="ci" value="{{ old('ci') }}" required class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Correo Electrónico *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Contraseña *</label>
                        <input type="password" name="password" required class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Confirmar *</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-[#f4f4f4] border-none rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>
                </div>

                <button type="submit" class="w-full mt-4 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-colors text-sm">
                    Registrarse
                </button>
                
                <button type="button" class="mt-3 text-[#0464a4] font-bold text-xs lg:hidden underline" id="btnShowLoginMobile">
                    ¿Ya tienes cuenta? Inicia Sesión
                </button>
            </form>
        </div>

        <div class="form-container sign-in-container">
            <form action="{{ route('login.store') }}" method="POST" class="h-full flex flex-col justify-center px-8 lg:px-16">
                @csrf
                <div class="text-center mb-6">
                    <img src="{{ asset('logo/logo.png') }}" class="h-12 lg:h-16 mx-auto mb-3 object-contain drop-shadow-md" alt="Logo">
                    <h1 class="text-2xl lg:text-3xl font-black text-[#343c4c] tracking-tight">Iniciar Sesión</h1>
                    <div class="h-1 w-12 bg-[#dc043c] mx-auto mt-2 rounded-full"></div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Usuario o Correo Electrónico</label>
                        <input type="text" name="login" value="{{ old('login') }}" required placeholder="ejemplo@correo.com" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Contraseña</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c]">
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-[#dc043c] bg-[#f4f4f4] border-none rounded focus:ring-[#dc043c]">
                            <span class="ml-2 text-xs font-bold text-[#343c4c]/70">Mantener sesión</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full mt-6 bg-[#dc043c] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-colors text-sm">
                    Ingresar
                </button>

                <button type="button" class="mt-3 text-[#dc043c] font-bold text-xs lg:hidden underline" id="btnShowRegisterMobile">
                    ¿No tienes cuenta? Regístrate
                </button>
            </form>
        </div>

        <div class="overlay-container hidden lg:block">
            <div class="overlay bg-[#343c4c] border-l-4 border-r-4 border-[#dcb47c]">
                
                <div class="overlay-panel overlay-left">
                    <h1 class="text-3xl lg:text-4xl font-black text-[#f4f4f4] mb-3">¡De Vuelta!</h1>
                    <p class="text-xs lg:text-sm text-[#f4f4f4]/80 mb-6 px-6 font-medium">Si ya eres parte de nuestro equipo, ingresa con tus credenciales para continuar.</p>
                    <button class="bg-transparent border-2 border-[#dcb47c] text-[#dcb47c] hover:bg-[#dcb47c] hover:text-[#343c4c] font-black uppercase tracking-widest py-2.5 px-8 rounded-full transition-colors shadow-lg text-sm" id="btnShowLogin">
                        Iniciar Sesión
                    </button>
                </div>

                <div class="overlay-panel overlay-right">
                    <h1 class="text-3xl lg:text-4xl font-black text-[#f4f4f4] mb-3">¿Eres Nuevo?</h1>
                    <p class="text-xs lg:text-sm text-[#f4f4f4]/80 mb-6 px-6 font-medium">Regístrate y comienza tu viaje en E-SPORTS. Obtén acceso rápido a promociones y envíos.</p>
                    <button class="bg-transparent border-2 border-[#dc043c] text-[#dc043c] hover:bg-[#dc043c] hover:text-white font-black uppercase tracking-widest py-2.5 px-8 rounded-full transition-colors shadow-lg text-sm" id="btnShowRegister">
                        Regístrate
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const wrapper = document.getElementById('auth-wrapper');
        const btnShowRegister = document.getElementById('btnShowRegister');
        const btnShowLogin = document.getElementById('btnShowLogin');
        const btnShowRegisterMobile = document.getElementById('btnShowRegisterMobile');
        const btnShowLoginMobile = document.getElementById('btnShowLoginMobile');
        const openRegister = () => wrapper.classList.add('right-panel-active');
        const openLogin = () => wrapper.classList.remove('right-panel-active');
        if(btnShowRegister) btnShowRegister.addEventListener('click', openRegister);
        if(btnShowRegisterMobile) btnShowRegisterMobile.addEventListener('click', openRegister);
        if(btnShowLogin) btnShowLogin.addEventListener('click', openLogin);
        if(btnShowLoginMobile) btnShowLoginMobile.addEventListener('click', openLogin);
        const hasRegisterErrors = {{ (old('nombre') || old('username') || $errors->has('username')) ? 'true' : 'false' }};
        const urlParams = new URLSearchParams(window.location.search);
        const forceRegister = urlParams.has('register');
        if (hasRegisterErrors || forceRegister) {
            openRegister();
        }
    });
</script>
@endsection