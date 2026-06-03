@extends('layouts.app')

@section('content')
<!-- SweetAlert2 CDN para Notificaciones Elegantes de Éxito o Suspensión -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
         style="background-image: url('{{ asset('img/cesped.png') }}'); background-position: bottom center; background-repeat: repeat-x; background-size: auto 100%; opacity: 0.8;">
    </div>
    
    <div class="absolute inset-0 bg-gradient-to-b from-[#f4f4f4] via-[#f4f4f4]/60 to-transparent pointer-events-none z-0"></div>

    <div class="relative z-10 auth-wrapper bg-white/90 backdrop-blur-xl rounded-[2.5rem] shadow-2xl border border-white/50 w-full max-w-5xl" id="auth-wrapper">
        
        <!-- ============================== -->
        <!--        REGISTRO FORM           -->
        <!-- ============================== -->
        <div class="form-container sign-up-container">
            <form action="{{ route('cliente.register.store') }}" method="POST" class="h-full flex flex-col justify-center px-8 lg:px-14 py-4" id="registerForm" autocomplete="off" novalidate>
                @csrf
                <div class="text-center mb-4">
                    <h1 class="text-2xl lg:text-3xl font-black text-[#343c4c] tracking-tight">Crear Cuenta</h1>
                    <div class="h-1 w-12 bg-[#0464a4] mx-auto mt-2 rounded-full"></div>
                </div>

                <div class="grid grid-cols-2 gap-x-3 gap-y-1 overflow-y-auto h-[62%] custom-scrollbar pr-2">
                    {{-- USUARIO --}}
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Usuario *</label>
                        <input type="text" name="username" id="reg_username" value="{{ old('username') }}" minlength="4" maxlength="80"
                            placeholder="min. 4 chars, solo letras/números/_" required autocomplete="off"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('username') border-red-400 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 @if(!$errors->has('username')) hidden @endif" id="err_username">
                            {{ $errors->first('username') }}
                        </p>
                    </div>

                    {{-- NOMBRES --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Nombres *</label>
                        <input type="text" name="nombre" id="reg_nombre" value="{{ old('nombre') }}" minlength="2" maxlength="100"
                            placeholder="Ej: Juan Carlos" required autocomplete="off"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('nombre') border-red-400 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 @if(!$errors->has('nombre')) hidden @endif" id="err_nombre">
                            {{ $errors->first('nombre') }}
                        </p>
                    </div>

                    {{-- APELLIDOS --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Apellidos *</label>
                        <input type="text" name="apellidos" id="reg_apellidos" value="{{ old('apellidos') }}" minlength="2" maxlength="100"
                            placeholder="Ej: Pérez López" required autocomplete="off"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('apellidos') border-red-400 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 @if(!$errors->has('apellidos')) hidden @endif" id="err_apellidos">
                            {{ $errors->first('apellidos') }}
                        </p>
                    </div>

                    {{-- C.I. (AQUÍ APARECERÁ EL ERROR SI ESTÁ REPETIDO) --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">C.I. *</label>
                        <input type="text" name="ci" id="reg_ci" value="{{ old('ci') }}" minlength="5" maxlength="20"
                            placeholder="Ej: 1234567 o 1234567 LP" required autocomplete="off"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('ci') border-red-400 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 @if(!$errors->has('ci')) hidden @endif" id="err_ci">
                            {{ $errors->first('ci') }}
                        </p>
                    </div>

                    {{-- TELÉFONO --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Teléfono</label>
                        <input type="tel" name="telefono" id="reg_telefono" value="{{ old('telefono') }}" maxlength="8" inputmode="numeric"
                            placeholder="Ej: 71234567" autocomplete="off"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('telefono') border-red-400 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 @if(!$errors->has('telefono')) hidden @endif" id="err_telefono">
                            {{ $errors->first('telefono') }}
                        </p>
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Correo Electrónico *</label>
                        <input type="email" name="email" id="reg_email" value="{{ old('email') }}" maxlength="150"
                            placeholder="Ej: juan@gmail.com" required autocomplete="off"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('email') border-red-400 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 @if(!$errors->has('email')) hidden @endif" id="err_email">
                            {{ $errors->first('email') }}
                        </p>
                    </div>

                    {{-- CONTRASEÑA CON OJO --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Contraseña *</label>
                        <div class="relative">
                            <input type="password" name="password" id="reg_password" minlength="6"
                                value="{{ old('password') }}" placeholder="Mín. 6 chars" required autocomplete="new-password"
                                class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 pr-10 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('password') border-red-400 bg-red-50 @enderror">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#0464a4] transition-colors toggle-password" data-target="reg_password">
                                <svg class="h-4 w-4 icon-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="h-4 w-4 icon-eye-off hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex gap-1 mt-1" id="strength_bars">
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb1"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb2"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb3"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb4"></div>
                        </div>
                        <p class="text-[10px] font-bold mt-0.5 hidden" id="strength_label"></p>
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 @if(!$errors->has('password')) hidden @endif" id="err_password">
                            {{ $errors->first('password') }}
                        </p>
                    </div>

                    {{-- CONFIRMAR CONTRASEÑA CON OJO --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Confirmar *</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="reg_password_confirm" minlength="6"
                                value="{{ old('password_confirmation') }}" placeholder="Repite la clave" required autocomplete="new-password"
                                class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 pr-10 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#0464a4] transition-colors toggle-password" data-target="reg_password_confirm">
                                <svg class="h-4 w-4 icon-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="h-4 w-4 icon-eye-off hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_confirm"></p>
                    </div>
                </div>

                <div class="flex flex-col space-y-2 mt-4">
                    <button type="button" id="btnValidate"
                        class="w-full bg-[#dcb47c] hover:bg-[#cba065] text-[#343c4c] font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-colors text-sm">
                        ✓ Validar Datos
                    </button>
                    
                    <button type="submit" id="btnRegister"
                        class="hidden w-full bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-colors text-sm">
                        Registrarse
                    </button>
                </div>

                <button type="button" class="mt-3 text-[#0464a4] font-bold text-xs lg:hidden underline" id="btnShowLoginMobile">
                    ¿Ya tienes cuenta? Inicia Sesión
                </button>
            </form>
        </div>

        <!-- ============================== -->
        <!--          LOGIN FORM            -->
        <!-- ============================== -->
        <div class="form-container sign-in-container">
            <form action="{{ route('login.store') }}" method="POST" class="h-full flex flex-col justify-center px-8 lg:px-16">
                @csrf
                <div class="text-center mb-6">
                    <img src="{{ asset('logo/logo.png') }}" class="h-12 lg:h-16 mx-auto mb-3 object-contain drop-shadow-md" alt="Logo">
                    <h1 class="text-2xl lg:text-3xl font-black text-[#343c4c] tracking-tight">Iniciar Sesión</h1>
                    <div class="h-1 w-12 bg-[#dc043c] mx-auto mt-2 rounded-full"></div>
                </div>
                
                <div class="space-y-4">
                    {{-- USUARIO LOGIN --}}
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Usuario o Correo Electrónico</label>
                        <input type="text" name="login" value="{{ old('login') }}" required placeholder="ejemplo@correo.com" 
                               class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c] @error('login') border-red-400 bg-red-50 @enderror">
                        @error('login')
                            <p class="text-[10px] text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- CONTRASEÑA LOGIN CON OJO --}}
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Contraseña</label>
                        <div class="relative">
                            <input type="password" name="password" id="login_password" required placeholder="••••••••" 
                                   class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 pr-10 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c] @error('password') @if(!old('nombre')) border-red-400 bg-red-50 @endif @enderror">
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-[#dc043c] transition-colors toggle-password" data-target="login_password">
                                <svg class="h-5 w-5 icon-eye" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="h-5 w-5 icon-eye-off hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        {{-- Solo mostrar si el error viene del login y no del registro --}}
                        @error('password')
                            @if(!old('nombre'))
                                <p class="text-[10px] text-red-600 font-bold mt-1">{{ $message }}</p>
                            @endif
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 text-[#dc043c] bg-[#f4f4f4] border-none rounded focus:ring-[#dc043c]">
                            <span class="ml-2 text-xs font-bold text-[#343c4c]/70">Mantener sesión</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="w-full mt-6 bg-gradient-to-r from-[#dc043c] to-[#a8002a] hover:scale-[1.02] hover:shadow-xl text-white font-black uppercase tracking-widest py-3.5 rounded-xl transition-all duration-300 text-sm">
                    Ingresar
                </button>

                <button type="button" class="mt-3 text-[#dc043c] font-bold text-xs lg:hidden underline" id="btnShowRegisterMobile">
                    ¿No tienes cuenta? Regístrate
                </button>
            </form>
        </div>

        <!-- OVERLAY PANELS (Para pantallas grandes) -->
        <div class="overlay-container hidden lg:block">
            <div class="overlay bg-gradient-to-br from-[#343c4c] to-[#1e232d] shadow-2xl z-20 relative">
                <div class="overlay-panel overlay-left">
                    <h1 class="text-3xl lg:text-4xl font-black text-[#f4f4f4] mb-3">¡De Vuelta!</h1>
                    <p class="text-xs lg:text-sm text-[#f4f4f4]/80 mb-6 px-6 font-medium">Si ya eres parte de nuestro equipo, ingresa con tus credenciales para continuar.</p>
                    <button class="bg-transparent border-2 border-[#dcb47c] text-[#dcb47c] hover:bg-[#dcb47c] hover:text-[#343c4c] font-black uppercase tracking-widest py-3 px-10 rounded-full transition-all duration-300 shadow-lg hover:scale-105 text-sm" id="btnShowLogin">
                        Iniciar Sesión
                    </button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1 class="text-3xl lg:text-4xl font-black text-[#f4f4f4] mb-3">¿Eres Nuevo?</h1>
                    <p class="text-xs lg:text-sm text-[#f4f4f4]/80 mb-6 px-6 font-medium">Regístrate y comienza tu viaje en E-SPORTS. Obtén acceso rápido a promociones y envíos.</p>
                    <button class="bg-transparent border-2 border-[#dc043c] text-[#dc043c] hover:bg-[#dc043c] hover:text-white font-black uppercase tracking-widest py-3 px-10 rounded-full transition-all duration-300 shadow-lg hover:scale-105 text-sm" id="btnShowRegister">
                        Regístrate
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {

        // ── MANEJO DE NOTIFICACIONES BACKEND (SWEETALERT2) ─────────────────────
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // NOTA: Se ha quitado el Swal.fire de $errors->any() para que los errores salgan inline y no molesten.

        @if(session('success_toast'))
            Toast.fire({ icon: 'success', title: "{{ session('success_toast') }}" });
        @endif

        @if(session('error_toast'))
            Toast.fire({ icon: 'error', title: "{{ session('error_toast') }}" });
        @endif
        
        @if(session('info_toast'))
            Toast.fire({ icon: 'info', title: "{{ session('info_toast') }}" });
        @endif


        // ── TOGGLE PASSWORD VISIBILITY (Boton Ojo) ────────────────────────────
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const iconEye = this.querySelector('.icon-eye');
                const iconEyeOff = this.querySelector('.icon-eye-off');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    iconEye.classList.add('hidden');
                    iconEyeOff.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    iconEye.classList.remove('hidden');
                    iconEyeOff.classList.add('hidden');
                }
            });
        });

        // ── PANEL TOGGLE Y MANTENER ABIERTO SI HAY ERROR ──────────────────────
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
        
        // LÓGICA CLAVE: Si el usuario acababa de enviar el formulario de registro (detectado porque existe el 'old(nombre)' o 'old(ci)'), forzamos mantener abierto el panel derecho
        const wasRegistering = {{ (old('nombre') || old('username') || old('ci') || old('email')) && !$errors->has('login') ? 'true' : 'false' }};
        const urlParams = new URLSearchParams(window.location.search);
        
        if (wasRegistering || urlParams.has('register')) {
            openRegister();
        }

        // ── VALIDACIÓN FRONTEND EN TIEMPO REAL ────────────────────────────────
        // Esta sección garantiza que los mensajes se pinten o limpien mientras el usuario escribe

        function setFieldError(inputEl, errorEl, msg) {
            if (msg) {
                inputEl.classList.add('border-red-400', 'bg-red-50');
                inputEl.classList.remove('border-green-400', 'bg-green-50');
                errorEl.textContent = msg;
                errorEl.classList.remove('hidden');
            } else {
                inputEl.classList.remove('border-red-400', 'bg-red-50');
                inputEl.classList.add('border-green-400', 'bg-green-50');
                errorEl.textContent = '';
                errorEl.classList.add('hidden');
            }
        }

        function validarNombre(value, campo) {
            value = value.trim();
            if (!value) return `El ${campo} es obligatorio.`;
            if (value.length < 2) return `El ${campo} debe tener al menos 2 letras.`;
            if (/[0-9]/.test(value)) return `El ${campo} no puede contener números.`;
            if (!/^[\p{L}\s\-]+$/u.test(value)) return `El ${campo} solo puede contener letras.`;
            return null;
        }

        function validarCI(value) {
            value = value.trim().toUpperCase();
            if (!value) return 'El C.I. es obligatorio.';
            if (!/^[0-9]{5,10}([- ][A-Z0-9]{1,4})?$/.test(value)) return 'Formato inválido. Ej: 1234567 o 1234567 LP';
            return null;
        }

        function validarTelefono(value) {
            if (!value) return null; 
            const limpio = value.replace(/[\s\-]/g, '');
            if (!/^\d+$/.test(limpio)) return 'Solo se permiten números.';
            if (limpio.length !== 8) return `Debe tener exactamente 8 dígitos.`;
            if (!/^[2367]/.test(limpio)) return 'Debe empezar en 2, 3, 6 o 7.';
            return null;
        }

        function validarEmail(value) {
            if (!value) return 'El correo electrónico es obligatorio.';
            if (!/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(value)) return 'Formato requerido: nombre@dominio.com';
            return null;
        }

        function validarPassword(value) {
            // Se flexibilizó: ahora solo exige mínimo 6 caracteres.
            // La fortaleza visual se seguirá mostrando.
            if (value.length < 6) return 'Debe tener mínimo 6 caracteres.';
            return null;
        }

        function actualizarFortaleza(value) {
            let score = 0;
            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[@#$!%*?&_.\-+=^]/.test(value)) score++;

            const bars = ['sb1','sb2','sb3','sb4'];
            const colors = ['bg-red-500','bg-orange-400','bg-yellow-400','bg-green-500'];
            const labels = ['Muy débil','Débil','Aceptable','Segura ✓'];
            const labelEl = document.getElementById('strength_label');

            bars.forEach((id, i) => {
                const el = document.getElementById(id);
                el.className = `h-1 flex-1 rounded ${i < score ? colors[score-1] : 'bg-gray-200'}`;
            });

            if (value.length > 0) {
                labelEl.textContent = labels[score - 1] || 'Muy débil';
                labelEl.className = `text-[10px] font-bold mt-0.5 ${score >= 4 ? 'text-green-600' : score >= 3 ? 'text-yellow-600' : 'text-red-600'}`;
                labelEl.classList.remove('hidden');
            } else {
                labelEl.classList.add('hidden');
            }
        }

        const fields = [
            { id: 'reg_nombre', errId: 'err_nombre', fn: v => validarNombre(v, 'nombre') },
            { id: 'reg_apellidos', errId: 'err_apellidos', fn: v => validarNombre(v, 'apellido') },
            { id: 'reg_ci', errId: 'err_ci', fn: validarCI },
            { id: 'reg_telefono', errId: 'err_telefono', fn: validarTelefono },
            { id: 'reg_email', errId: 'err_email', fn: validarEmail },
            { id: 'reg_username', errId: 'err_username', fn: v => {
                if (!v) return 'El usuario es obligatorio.';
                if (v.length < 4) return 'Mínimo 4 caracteres.';
                if (!/^[a-zA-Z0-9_]+$/.test(v)) return 'Solo letras, números y (_).';
                return null;
            }},
        ];

        fields.forEach(({ id, errId, fn }) => {
            const input = document.getElementById(id);
            const errEl = document.getElementById(errId);
            if (!input || !errEl) return;
            
            // Reemplaza el texto original de error (del backend) al escribir para mostrar en vivo
            input.addEventListener('blur', () => setFieldError(input, errEl, fn(input.value)));
            input.addEventListener('input', () => {
                if (!errEl.classList.contains('hidden')) setFieldError(input, errEl, fn(input.value));
            });
        });

        const passInput = document.getElementById('reg_password');
        const passErrEl = document.getElementById('err_password');
        const confirmInput = document.getElementById('reg_password_confirm');
        const confirmErrEl = document.getElementById('err_confirm');

        if (passInput) {
            if (passInput.value) actualizarFortaleza(passInput.value);
            passInput.addEventListener('input', () => {
                actualizarFortaleza(passInput.value);
                if (!passErrEl.classList.contains('hidden')) setFieldError(passInput, passErrEl, validarPassword(passInput.value));
                if (!confirmErrEl.classList.contains('hidden')) validarConfirmacion();
            });
            passInput.addEventListener('blur', () => setFieldError(passInput, passErrEl, validarPassword(passInput.value)));
        }

        function validarConfirmacion() {
            const err = (passInput.value !== confirmInput.value) ? 'Las contraseñas no coinciden.' : null;
            setFieldError(confirmInput, confirmErrEl, err);
        }

        if (confirmInput) {
            confirmInput.addEventListener('blur', validarConfirmacion);
            confirmInput.addEventListener('input', () => {
                if (!confirmErrEl.classList.contains('hidden')) validarConfirmacion();
            });
        }

        const telInput = document.getElementById('reg_telefono');
        if (telInput) {
            telInput.addEventListener('input', () => {
                telInput.value = telInput.value.replace(/[^0-9]/g, '').slice(0, 8);
            });
        }

        const registerForm = document.getElementById('registerForm');
        const btnValidate = document.getElementById('btnValidate');
        const btnRegister = document.getElementById('btnRegister');

        function runAllValidations() {
            let hasErrors = false;
            fields.forEach(({ id, errId, fn }) => {
                const input = document.getElementById(id);
                const errEl = document.getElementById(errId);
                if (!input || !errEl) return;
                const err = fn(input.value);
                setFieldError(input, errEl, err);
                if (err) hasErrors = true;
            });
            const passErr = validarPassword(passInput?.value || '');
            setFieldError(passInput, passErrEl, passErr);
            if (passErr) hasErrors = true;
            validarConfirmacion();
            if (passInput?.value !== confirmInput?.value) hasErrors = true;
            return !hasErrors;
        }

        if (btnValidate) {
            btnValidate.addEventListener('click', async () => {
                // 1. Validar primero la estructura en frontend
                if (!runAllValidations()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Datos Incompletos',
                        text: 'Por favor revisa los campos en rojo antes de continuar.',
                        confirmButtonColor: '#0464a4'
                    });
                    return;
                }

                // 2. Si pasa frontend, pedir validación de base de datos en vivo (AJAX)
                btnValidate.innerHTML = '<span class="animate-pulse">Validando...</span>';
                btnValidate.disabled = true;

                try {
                    const response = await fetch("{{ route('cliente.register.validar') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            username: document.getElementById('reg_username').value,
                            email: document.getElementById('reg_email').value,
                            ci: document.getElementById('reg_ci').value,
                            telefono: document.getElementById('reg_telefono').value
                        })
                    });

                    const data = await response.json();

                    if (data.valido) {
                        // Todo perfecto, el DB autoriza
                        btnValidate.classList.add('hidden');
                        btnRegister.classList.remove('hidden');
                        Toast.fire({ icon: 'success', title: 'Todos los datos son válidos. Ya puedes registrarte.' });
                    } else {
                        // El DB encontró campos duplicados
                        if (data.errores.username) setFieldError(document.getElementById('reg_username'), document.getElementById('err_username'), data.errores.username);
                        if (data.errores.email) setFieldError(document.getElementById('reg_email'), document.getElementById('err_email'), data.errores.email);
                        if (data.errores.ci) setFieldError(document.getElementById('reg_ci'), document.getElementById('err_ci'), data.errores.ci);
                        if (data.errores.telefono) setFieldError(document.getElementById('reg_telefono'), document.getElementById('err_telefono'), data.errores.telefono);

                        Swal.fire({
                            icon: 'error',
                            title: 'Datos ya registrados',
                            text: 'Algunos de los datos proporcionados ya están en uso por otro usuario.',
                            confirmButtonColor: '#dc043c'
                        });
                        resetValidationState();
                    }
                } catch (error) {
                    console.error("Error validando el registro:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo validar con el servidor. Inténtalo de nuevo.',
                        confirmButtonColor: '#dc043c'
                    });
                    resetValidationState();
                }

                btnValidate.disabled = false;
                if (!btnValidate.classList.contains('hidden')) {
                    btnValidate.innerHTML = '⚠️ Re-validar Datos';
                }
            });
        }

        // Si el usuario modifica cualquier campo después de haber validado, ocultar el botón de registro nuevamente
        const resetValidationState = () => {
            if(!btnRegister.classList.contains('hidden')) {
                btnRegister.classList.add('hidden');
                btnValidate.classList.remove('hidden');
                btnValidate.innerHTML = '⚠️ Re-validar Datos';
                btnValidate.classList.remove('bg-[#dcb47c]', 'hover:bg-[#cba065]');
                btnValidate.classList.add('bg-orange-500', 'text-white', 'hover:bg-orange-600');
            }
        };

        if(registerForm) {
            registerForm.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', resetValidationState);
            });

            registerForm.addEventListener('submit', (e) => {
                if (!runAllValidations()) {
                    e.preventDefault();
                    resetValidationState();
                }
            });
        }
    });
</script>
@endsection