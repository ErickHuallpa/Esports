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
            <form action="{{ route('cliente.register.store') }}" method="POST" class="h-full flex flex-col justify-center px-8 lg:px-14 py-4" id="registerForm" novalidate>
                @csrf
                <div class="text-center mb-4">
                    <h1 class="text-2xl lg:text-3xl font-black text-[#343c4c] tracking-tight">Crear Cuenta</h1>
                    <div class="h-1 w-12 bg-[#0464a4] mx-auto mt-2 rounded-full"></div>
                </div>

                {{-- Errores del backend --}}
                @if ($errors->any())
                    <div class="mb-3 bg-red-50 border-l-4 border-red-500 p-3 rounded-lg text-xs">
                        <ul class="list-disc pl-4 space-y-0.5 text-red-700 font-semibold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-x-3 gap-y-1 overflow-y-auto h-[62%] custom-scrollbar pr-2">

                    {{-- USUARIO --}}
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Usuario *</label>
                        <input type="text" name="username" id="reg_username"
                            value="{{ old('username') }}"
                            minlength="4" maxlength="80"
                            pattern="[a-zA-Z0-9_]+"
                            placeholder="min. 4 chars, solo letras/números/_"
                            required
                            autocomplete="username"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('username') border-red-500 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_username"></p>
                    </div>

                    {{-- NOMBRES --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Nombres *</label>
                        <input type="text" name="nombre" id="reg_nombre"
                            value="{{ old('nombre') }}"
                            minlength="2" maxlength="100"
                            placeholder="Ej: Juan Carlos"
                            required
                            autocomplete="given-name"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('nombre') border-red-500 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_nombre"></p>
                    </div>

                    {{-- APELLIDOS --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Apellidos *</label>
                        <input type="text" name="apellidos" id="reg_apellidos"
                            value="{{ old('apellidos') }}"
                            minlength="2" maxlength="100"
                            placeholder="Ej: Pérez López"
                            required
                            autocomplete="family-name"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('apellidos') border-red-500 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_apellidos"></p>
                    </div>

                    {{-- C.I. --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">C.I. *</label>
                        <input type="text" name="ci" id="reg_ci"
                            value="{{ old('ci') }}"
                            minlength="5" maxlength="20"
                            placeholder="Ej: 1234567 LP"
                            required
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('ci') border-red-500 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_ci"></p>
                    </div>

                    {{-- TELÉFONO --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Teléfono</label>
                        <input type="tel" name="telefono" id="reg_telefono"
                            value="{{ old('telefono') }}"
                            maxlength="8"
                            inputmode="numeric"
                            placeholder="Ej: 71234567"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('telefono') border-red-500 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_telefono"></p>
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-span-2">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Correo Electrónico *</label>
                        <input type="email" name="email" id="reg_email"
                            value="{{ old('email') }}"
                            maxlength="150"
                            placeholder="Ej: juan@gmail.com"
                            required
                            autocomplete="email"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('email') border-red-500 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_email"></p>
                    </div>

                    {{-- CONTRASEÑA --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Contraseña *</label>
                        <input type="password" name="password" id="reg_password"
                            minlength="8"
                            placeholder="Mín. 8 chars, A, a, 1, @"
                            required
                            autocomplete="new-password"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('password') border-red-500 bg-red-50 @enderror">
                        {{-- Indicador de fortaleza --}}
                        <div class="flex gap-1 mt-1" id="strength_bars">
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb1"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb2"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb3"></div>
                            <div class="h-1 flex-1 rounded bg-gray-200" id="sb4"></div>
                        </div>
                        <p class="text-[10px] font-bold mt-0.5 hidden" id="strength_label"></p>
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_password"></p>
                    </div>

                    {{-- CONFIRMAR CONTRASEÑA --}}
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[9px] font-black text-[#343c4c] uppercase tracking-widest mb-0.5">Confirmar *</label>
                        <input type="password" name="password_confirmation" id="reg_password_confirm"
                            minlength="8"
                            placeholder="Repite la contraseña"
                            required
                            autocomplete="new-password"
                            class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors @error('password') border-red-500 bg-red-50 @enderror">
                        <p class="field-error text-[10px] text-red-600 font-bold mt-0.5 hidden" id="err_confirm"></p>
                    </div>
                </div>

                <button type="submit" id="btnRegister"
                    class="w-full mt-4 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-colors text-sm disabled:opacity-50 disabled:cursor-not-allowed">
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
        // ── PANEL TOGGLE ──────────────────────────────────────────────────────
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
        if (hasRegisterErrors || urlParams.has('register')) openRegister();

        // ── VALIDACIÓN EN TIEMPO REAL ─────────────────────────────────────────

        /**
         * Muestra o limpia un mensaje de error bajo un campo.
         */
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

        // ── VALIDADOR: NOMBRE / APELLIDO ──────────────────────────────────────
        function validarNombre(value, campo) {
            value = value.trim();
            if (!value) return `El ${campo} es obligatorio.`;
            if (value.length < 2) return `El ${campo} debe tener al menos 2 letras.`;
            if (/[0-9]/.test(value)) return `El ${campo} no puede contener números ("${value.match(/[0-9]/)[0]}" no permitido).`;
            if (/[^\p{L}\s\-]/u.test(value)) return `El ${campo} solo puede contener letras. Sin números ni símbolos.`;
            if (/(.)\1{2,}/u.test(value)) return `El ${campo} contiene letras repetidas inválidas.`;
            return null;
        }

        // ── VALIDADOR: C.I. ───────────────────────────────────────────────────
        function validarCI(value) {
            value = value.trim().toUpperCase();
            if (!value) return 'El C.I. es obligatorio.';
            if (!/^[0-9]{5,8}([- ][A-Z0-9]{1,4})?$/.test(value))
                return 'Formato de C.I. inválido. Ej: 1234567 o 1234567 LP';
            return null;
        }

        // ── VALIDADOR: TELÉFONO ───────────────────────────────────────────────
        function validarTelefono(value) {
            if (!value) return null; // Opcional
            const limpio = value.replace(/[\s\-]/g, '');
            if (!/^\d+$/.test(limpio)) return 'El teléfono solo debe contener números.';
            if (limpio.length !== 8) return `El teléfono debe tener exactamente 8 dígitos (tiene ${limpio.length}).`;
            if (!/^[2367]/.test(limpio)) return 'Prefijo inválido. Celulares: 6x, 7x. Fijos: 2x, 3x.';
            return null;
        }

        // ── VALIDADOR: EMAIL ──────────────────────────────────────────────────
        function validarEmail(value) {
            if (!value) return 'El correo electrónico es obligatorio.';
            // Regex estricta: requiere dominio con TLD real (texto@dominio.com)
            if (!/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(value))
                return 'Correo inválido. Formato requerido: nombre@dominio.com';
            return null;
        }

        // ── VALIDADOR: CONTRASEÑA ─────────────────────────────────────────────
        function validarPassword(value) {
            const errores = [];
            if (value.length < 8)          errores.push('8 caracteres mínimo');
            if (!/[A-Z]/.test(value))      errores.push('1 mayúscula');
            if (!/[a-z]/.test(value))      errores.push('1 minúscula');
            if (!/[0-9]/.test(value))      errores.push('1 número');
            if (!/[@#$!%*?&_.\-+=^,;:~`|<>()\[\]{}]/.test(value))
                                            errores.push('1 carácter especial (@, #, $, !, %)');
            return errores.length ? `Falta: ${errores.join(', ')}.` : null;
        }

        // ── INDICADOR DE FORTALEZA DE CONTRASEÑA ──────────────────────────────
        function actualizarFortaleza(value) {
            let score = 0;
            if (value.length >= 8)          score++;
            if (/[A-Z]/.test(value))        score++;
            if (/[0-9]/.test(value))        score++;
            if (/[@#$!%*?&_.\-+=^]/.test(value)) score++;

            const bars  = ['sb1','sb2','sb3','sb4'];
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

        // ── BIND EVENTOS ──────────────────────────────────────────────────────
        const fields = [
            {
                id: 'reg_nombre', errId: 'err_nombre',
                fn: v => validarNombre(v, 'nombre')
            },
            {
                id: 'reg_apellidos', errId: 'err_apellidos',
                fn: v => validarNombre(v, 'apellido')
            },
            {
                id: 'reg_ci', errId: 'err_ci',
                fn: validarCI
            },
            {
                id: 'reg_telefono', errId: 'err_telefono',
                fn: validarTelefono
            },
            {
                id: 'reg_email', errId: 'err_email',
                fn: validarEmail
            },
            {
                id: 'reg_username', errId: 'err_username',
                fn: v => {
                    if (!v) return 'El usuario es obligatorio.';
                    if (v.length < 4) return 'El usuario debe tener al menos 4 caracteres.';
                    if (!/^[a-zA-Z0-9_]+$/.test(v)) return 'Solo letras, números y guión bajo (_). Sin espacios.';
                    return null;
                }
            },
        ];

        fields.forEach(({ id, errId, fn }) => {
            const input = document.getElementById(id);
            const errEl = document.getElementById(errId);
            if (!input || !errEl) return;

            // Validar al perder el foco
            input.addEventListener('blur', () => {
                setFieldError(input, errEl, fn(input.value));
            });
            // Limpiar error mientras escribe (después del primer blur)
            input.addEventListener('input', () => {
                if (!errEl.classList.contains('hidden')) {
                    setFieldError(input, errEl, fn(input.value));
                }
            });
        });

        // Contraseña: validación + fortaleza en tiempo real
        const passInput    = document.getElementById('reg_password');
        const passErrEl    = document.getElementById('err_password');
        const confirmInput = document.getElementById('reg_password_confirm');
        const confirmErrEl = document.getElementById('err_confirm');

        if (passInput) {
            passInput.addEventListener('input', () => {
                actualizarFortaleza(passInput.value);
                if (!passErrEl.classList.contains('hidden'))
                    setFieldError(passInput, passErrEl, validarPassword(passInput.value));
                // Re-validar confirmación si ya fue tocada
                if (!confirmErrEl.classList.contains('hidden'))
                    validarConfirmacion();
            });
            passInput.addEventListener('blur', () => {
                setFieldError(passInput, passErrEl, validarPassword(passInput.value));
            });
        }

        function validarConfirmacion() {
            const err = (passInput.value !== confirmInput.value)
                ? 'Las contraseñas no coinciden.'
                : null;
            setFieldError(confirmInput, confirmErrEl, err);
        }

        if (confirmInput) {
            confirmInput.addEventListener('blur', validarConfirmacion);
            confirmInput.addEventListener('input', () => {
                if (!confirmErrEl.classList.contains('hidden')) validarConfirmacion();
            });
        }

        // Teléfono: solo aceptar dígitos al escribir
        const telInput = document.getElementById('reg_telefono');
        if (telInput) {
            telInput.addEventListener('input', () => {
                // Eliminar cualquier carácter que no sea dígito
                telInput.value = telInput.value.replace(/[^0-9]/g, '').slice(0, 8);
            });
        }

        // ── PREVENIR SUBMIT SI HAY ERRORES FRONTEND ───────────────────────────
        const registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', (e) => {
                let hasErrors = false;

                // Validar todos los campos antes de enviar
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

                if (hasErrors) {
                    e.preventDefault();
                    // Scroll al primer error
                    const firstErr = registerForm.querySelector('.border-red-400');
                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            });
        }
    });
</script>
@endsection