@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-6">
    
    <div class="mb-8 flex flex-col md:flex-row justify-between md:items-end gap-5 border-b-2 border-[#f4f4f4] pb-5">
        <div>
            <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Mi Cuenta</h1>
            <p class="text-[#343c4c]/60 text-sm font-medium mt-1">Gestiona tu información personal, foto de perfil y opciones de seguridad.</p>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        
        <div class="w-full lg:w-1/4">
            <div class="bg-white rounded-3xl border border-[#343c4c]/10 shadow-xl overflow-hidden sticky top-6">
                <div class="p-6 text-center bg-[#f4f4f4]/30 border-b border-[#f4f4f4]">
                    <div class="w-24 h-24 mx-auto bg-white rounded-full flex items-center justify-center text-[#343c4c] font-black text-3xl uppercase overflow-hidden border-4 border-[#dcb47c] shadow-md mb-4 relative group">
                        @if($user->foto_perfil)
                            <img src="{{ asset('storage/' . $user->foto_perfil) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300" alt="Foto de perfil">
                        @else
                            {{ substr($user->persona->nombre, 0, 1) }}{{ substr($user->persona->apellidos, 0, 1) }}
                        @endif
                    </div>
                    <p class="font-black text-xl text-[#343c4c] leading-tight">{{ $user->username }}</p>
                    <span class="inline-block mt-2 text-[9px] font-black uppercase tracking-widest text-white bg-[#0464a4] px-3 py-1 rounded-md shadow-sm">
                        {{ $user->rol->nombre }}
                    </span>
                </div>

                <nav class="p-4 space-y-2">
                    <a href="{{ route('perfil.edit') }}" class="flex items-center px-4 py-3 bg-[#0464a4] text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-md transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Datos Personales
                    </a>
                    <a href="{{ route('cliente.pedidos') }}" class="flex items-center px-4 py-3 text-[#343c4c]/70 hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-xl text-xs font-bold uppercase tracking-widest transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Mis Pedidos
                    </a>
                    <a href="{{ route('cliente.resenas') }}" class="flex items-center px-4 py-3 text-[#343c4c]/70 hover:bg-[#f4f4f4] hover:text-[#0464a4] rounded-xl text-xs font-bold uppercase tracking-widest transition-colors">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        Mis Reseñas
                    </a>
                </nav>
            </div>
        </div>

        <div class="w-full lg:w-3/4 space-y-8">
            
            <div class="bg-white rounded-3xl shadow-xl border border-[#343c4c]/10 overflow-hidden">
                <div class="bg-[#343c4c] px-8 py-5 border-b-4 border-[#dcb47c] flex justify-between items-center">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        Información del Perfil
                    </h2>
                </div>
                
                <div class="p-8">
                    <p class="text-xs text-[#343c4c]/60 font-bold mb-6">Actualiza tu foto y tus datos para mantener tu cuenta al día y evitar problemas logísticos en tus envíos.</p>
                    
                    <form id="perfilForm" action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-8 p-5 bg-[#f4f4f4]/50 border border-[#f4f4f4] rounded-2xl flex items-center space-x-5">
                            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center overflow-hidden border-2 border-[#dcb47c] shadow-sm flex-shrink-0">
                                @if($user->foto_perfil)
                                    <img id="previewImage" src="{{ asset('storage/' . $user->foto_perfil) }}" class="w-full h-full object-cover">
                                @else
                                    <img id="previewImage" src="" class="w-full h-full object-cover hidden">
                                    <svg id="defaultImage" class="w-8 h-8 text-[#343c4c]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Cambiar Foto de Perfil</label>
                                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*" 
                                    class="w-full text-xs text-[#343c4c] font-medium file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-[#343c4c] file:text-white file:cursor-pointer hover:file:bg-[#0464a4] transition-all bg-white rounded-xl shadow-sm border border-[#343c4c]/5 p-1">
                                @error('foto_perfil') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Cédula de Identidad (C.I.) *</label>
                                <input type="text" name="ci" value="{{ old('ci', $user->persona->ci) }}" required maxlength="15" pattern="[0-9]+[A-Za-z]*" title="Solo números, puede terminar en letra"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                                @error('ci') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombre de Usuario *</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}" required maxlength="30" pattern="[A-Za-z0-9_]+" title="Solo letras, números y guión bajo"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                                @error('username') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombres *</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $user->persona->nombre) }}" required maxlength="50" minlength="2" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se admiten letras"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                                @error('nombre') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Apellidos *</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $user->persona->apellidos) }}" required maxlength="50" minlength="2" pattern="[A-Za-zñÑáéíóúÁÉÍÓÚ\s]+" title="Solo se admiten letras"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                                @error('apellidos') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Correo Electrónico *</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required maxlength="100"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                                @error('email') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div class="flex flex-col">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Teléfono Móvil</label>
                                @php
                                    $countries = [
                                        ['code' => '+591', 'flag' => 'bo', 'format' => '#### ####', 'max' => 9],
                                        ['code' => '+54',  'flag' => 'ar', 'format' => '# ## ####-####', 'max' => 14],
                                        ['code' => '+56',  'flag' => 'cl', 'format' => '# #### ####', 'max' => 11],
                                        ['code' => '+51',  'flag' => 'pe', 'format' => '### ### ###', 'max' => 11],
                                        ['code' => '+',    'flag' => 'un', 'format' => '###############', 'max' => 15],
                                    ];
                                    
                                    $phoneNum = $user->persona->telefono ?? '';
                                    $selectedCountry = $countries[0];
                                    
                                    if ($phoneNum) {
                                        foreach ($countries as $c) {
                                            if ($c['code'] !== '+' && str_starts_with($phoneNum, $c['code'])) {
                                                $selectedCountry = $c;
                                                $phoneNum = substr($phoneNum, strlen($c['code']));
                                                break;
                                            }
                                        }
                                        if ($selectedCountry['code'] === '+') {
                                            if(str_starts_with($phoneNum, '+')) $phoneNum = substr($phoneNum, 1);
                                        }
                                    }
                                @endphp
                                <div class="flex relative" id="phone_wrapper">
                                    <div id="custom_select_btn" class="flex items-center bg-[#e4e4e4] border-none rounded-l-xl p-3.5 text-sm font-bold text-[#343c4c] w-[105px] cursor-pointer select-none border-r border-[#343c4c]/10">
                                        <img src="https://flagcdn.com/w20/{{ $selectedCountry['flag'] === 'un' ? 'xx' : $selectedCountry['flag'] }}.png" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/e/ef/International_Flag_of_Planet_Earth.svg'" class="w-5 h-auto mr-2 shadow-sm" id="selected_flag">
                                        <span id="selected_code" class="text-xs">{{ $selectedCountry['code'] }}</span>
                                        <svg class="w-3 h-3 ml-auto text-[#343c4c]/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                    </div>
                                    
                                    <div id="custom_select_list" class="hidden absolute top-[110%] left-0 w-[140px] bg-white border border-[#343c4c]/10 rounded-xl shadow-xl z-50 overflow-hidden py-1">
                                        @foreach($countries as $c)
                                        <div class="flex items-center px-4 py-2 hover:bg-[#f4f4f4] cursor-pointer country-option transition-colors" data-code="{{ $c['code'] }}" data-flag="{{ $c['flag'] }}" data-format="{{ $c['format'] }}" data-max="{{ $c['max'] }}">
                                            <img src="https://flagcdn.com/w20/{{ $c['flag'] === 'un' ? 'xx' : $c['flag'] }}.png" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/e/ef/International_Flag_of_Planet_Earth.svg'" class="w-5 h-auto mr-2 shadow-sm">
                                            <span class="text-xs font-bold text-[#343c4c]">{{ $c['code'] }}</span>
                                        </div>
                                        @endforeach
                                    </div>

                                    <input type="text" id="phone_display" value="{{ old('telefono', $phoneNum) }}" maxlength="{{ $selectedCountry['max'] }}" placeholder="Ej: 71234567"
                                        class="w-full bg-[#f4f4f4] border-none rounded-r-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] outline-none" data-format="{{ $selectedCountry['format'] }}">
                                    <input type="hidden" name="telefono" id="phone_hidden" value="{{ old('telefono', $user->persona->telefono) }}">
                                </div>
                                @error('telefono') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->persona->fecha_nacimiento)->format('Y-m-d')) }}" max="{{ date('Y-m-d', strtotime('-13 years')) }}"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                                @error('fecha_nacimiento') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Dirección Habitual</label>
                                <input type="text" name="direccion" value="{{ old('direccion', $user->persona->direccion) }}" maxlength="150"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                                @error('direccion') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t-2 border-[#f4f4f4]">
                            <button type="submit" id="btnGuardarPerfil" disabled class="bg-[#0464a4] disabled:bg-[#343c4c]/40 disabled:cursor-not-allowed hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3.5 px-8 rounded-xl shadow-lg transition-all text-xs transform hover:-translate-y-0.5 disabled:transform-none">
                                Guardar Información
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-xl border border-[#343c4c]/10 overflow-hidden">
                <div class="bg-[#343c4c] px-8 py-5 border-b-4 border-[#dc043c]">
                    <h2 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#dc043c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Seguridad de la Cuenta
                    </h2>
                </div>
                
                <div class="p-8">
                    <p class="text-xs text-[#343c4c]/60 font-bold mb-6">Protege tu cuenta actualizando regularmente tu contraseña de acceso.</p>
                    
                    <form id="passwordForm" action="{{ route('perfil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Contraseña Actual *</label>
                                <input type="password" name="current_password" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] font-bold text-[#343c4c]">
                                @error('current_password') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nueva Contraseña *</label>
                                <input type="password" id="nueva_password" name="password" required minlength="8"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] font-bold text-[#343c4c]">
                                
                                <div class="mt-2 h-1.5 w-full bg-[#f4f4f4] rounded-full overflow-hidden">
                                    <div id="password-strength-bar" class="h-full w-0 transition-all duration-300"></div>
                                </div>
                                <p id="password-strength-text" class="text-[10px] font-black uppercase tracking-widest mt-1 text-[#343c4c]/50">Esperando...</p>

                                @error('password') <p class="text-xs text-[#dc043c] mt-1 font-bold">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Confirmar Nueva *</label>
                                <input type="password" name="password_confirmation" required minlength="8"
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] font-bold text-[#343c4c]">
                            </div>
                        </div>
                        
                        <div class="mt-8 pt-4 border-t-2 border-[#f4f4f4]">
                            <button type="submit" class="bg-[#dc043c] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3.5 px-8 rounded-xl shadow-lg transition-all text-xs transform hover:-translate-y-0.5">
                                Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Image Preview
    const fotoInput = document.getElementById('foto_perfil');
    const previewImage = document.getElementById('previewImage');
    const defaultImage = document.getElementById('defaultImage');

    if(fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('hidden');
                    if(defaultImage) defaultImage.classList.add('hidden');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // 2. Dirty Form Check
    const perfilForm = document.getElementById('perfilForm');
    const btnGuardarPerfil = document.getElementById('btnGuardarPerfil');
    
    if(perfilForm && btnGuardarPerfil) {
        const formElements = Array.from(perfilForm.elements).filter(el => el.name && !['_token', '_method'].includes(el.name));
        const originalState = {};
        formElements.forEach(el => {
            originalState[el.name] = el.type === 'file' ? '' : el.value;
        });

        function checkFormChanged() {
            let isChanged = false;
            formElements.forEach(el => {
                if (el.type === 'file') {
                    if(el.files.length > 0) isChanged = true;
                } else {
                    if (el.value !== originalState[el.name]) isChanged = true;
                }
            });
            btnGuardarPerfil.disabled = !isChanged;
        }

        formElements.forEach(el => {
            el.addEventListener('input', checkFormChanged);
            el.addEventListener('change', checkFormChanged);
        });
    }

    // 5. Phone Formatter Custom
    const btnDrop = document.getElementById('custom_select_btn');
    const listDrop = document.getElementById('custom_select_list');
    const countryOptions = document.querySelectorAll('.country-option');
    
    const selectedFlag = document.getElementById('selected_flag');
    const selectedCode = document.getElementById('selected_code');
    const phoneDisplay = document.getElementById('phone_display');
    const phoneHidden = document.getElementById('phone_hidden');

    if (btnDrop && listDrop && phoneDisplay) {
        btnDrop.addEventListener('click', (e) => {
            e.stopPropagation();
            listDrop.classList.toggle('hidden');
        });

        document.addEventListener('click', () => {
            listDrop.classList.add('hidden');
        });

        countryOptions.forEach(opt => {
            opt.addEventListener('click', (e) => {
                e.stopPropagation();
                
                const code = opt.getAttribute('data-code');
                const flag = opt.getAttribute('data-flag');
                const format = opt.getAttribute('data-format');
                const max = opt.getAttribute('data-max');

                selectedCode.innerText = code;
                selectedFlag.src = `https://flagcdn.com/w20/${flag === 'un' ? 'xx' : flag}.png`;
                
                phoneDisplay.setAttribute('data-format', format);
                phoneDisplay.setAttribute('maxlength', max);

                listDrop.classList.add('hidden');
                phoneDisplay.focus();
                formatPhone();
            });
        });

        function formatPhone() {
            let raw = phoneDisplay.value.replace(/[^0-9]/g, '');
            let format = phoneDisplay.getAttribute('data-format');
            let maxLen = parseInt(phoneDisplay.getAttribute('maxlength') || 15);
            let currentCode = selectedCode.innerText;
            
            let formatted = '';
            let rawIndex = 0;
            
            if (currentCode !== '+') {
                for (let i = 0; i < format.length; i++) {
                    if (rawIndex >= raw.length) break;
                    if (format[i] === '#') {
                        formatted += raw[rawIndex];
                        rawIndex++;
                    } else {
                        formatted += format[i];
                    }
                }
                if (rawIndex < raw.length && formatted.length < maxLen) {
                    formatted += raw.substring(rawIndex, rawIndex + (maxLen - formatted.length));
                }
            } else {
                formatted = raw.substring(0, maxLen);
            }

            phoneDisplay.value = formatted;
            
            if (raw.length > 0) {
                phoneHidden.value = currentCode + raw;
            } else {
                phoneHidden.value = '';
            }
            
            phoneHidden.dispatchEvent(new Event('input', { bubbles: true }));
        }

        phoneDisplay.addEventListener('input', formatPhone);
        formatPhone();
    }

    // 3. Password Strength Meter
    const pwdInput = document.getElementById('nueva_password');
    const pwdBar = document.getElementById('password-strength-bar');
    const pwdText = document.getElementById('password-strength-text');

    if(pwdInput) {
        pwdInput.addEventListener('input', function() {
            const val = this.value;
            if (!val) {
                pwdBar.style.width = '0%';
                pwdText.innerText = 'Esperando...';
                pwdText.className = 'text-[10px] font-black uppercase tracking-widest mt-1 text-[#343c4c]/50';
                return;
            }

            let strength = 0;
            if (val.length >= 8) strength += 1;
            if (val.match(/[A-Z]/)) strength += 1;
            if (val.match(/[0-9]/)) strength += 1;
            if (val.match(/[^A-Za-z0-9]/)) strength += 1;

            if (strength <= 1 || val.length < 6) {
                pwdBar.style.width = '33%';
                pwdBar.className = 'h-full bg-[#dc043c] transition-all duration-300';
                pwdText.innerText = 'Débil';
                pwdText.className = 'text-[10px] font-black uppercase tracking-widest mt-1 text-[#dc043c]';
            } else if (strength === 2 || strength === 3) {
                pwdBar.style.width = '66%';
                pwdBar.className = 'h-full bg-[#dcb47c] transition-all duration-300';
                pwdText.innerText = 'Media';
                pwdText.className = 'text-[10px] font-black uppercase tracking-widest mt-1 text-[#dcb47c]';
            } else {
                pwdBar.style.width = '100%';
                pwdBar.className = 'h-full bg-green-500 transition-all duration-300';
                pwdText.innerText = 'Fuerte';
                pwdText.className = 'text-[10px] font-black uppercase tracking-widest mt-1 text-green-500';
            }
        });
    }
    // 4. AJAX Form Submissions
    function manejarEnvioAJAX(formId, btnOriginalText) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            btn.innerHTML = '<span class="relative flex items-center justify-center text-sm drop-shadow-md"><svg class="w-5 h-5 mr-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Procesando...</span>';
            btn.disabled = true;

            const formData = new FormData(form);
            if (formId === 'perfilForm' && document.getElementById('foto_perfil').files.length === 0) {
                formData.delete('foto_perfil');
            }

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                form.querySelectorAll('.error-text').forEach(el => el.remove());

                if (response.ok) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.message || 'Actualizado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#0464a4'
                    }).then(() => {
                        if (formId === 'passwordForm') {
                            form.reset();
                            const bar = document.getElementById('password-strength-bar');
                            const txt = document.getElementById('password-strength-text');
                            if(bar) bar.style.width = '0%';
                            if(txt) txt.innerText = 'Esperando...';
                        }
                        if (formId === 'perfilForm') {
                            const fotoInput = document.getElementById('foto_perfil');
                            if(fotoInput) fotoInput.value = '';
                            
                            // Re-calculate original state to disable button again
                            const formElements = Array.from(form.elements).filter(el => el.name && !['_token', '_method'].includes(el.name));
                            formElements.forEach(el => {
                                if (el.type !== 'file') {
                                    originalState[el.name] = el.value;
                                }
                            });
                            btn.disabled = true;
                        }
                    });
                } else if (response.status === 422) {
                    let erroresStr = '';
                    for (let field in data.errors) {
                        erroresStr += `<li>${data.errors[field][0]}</li>`;
                        let input = form.querySelector(`[name="${field}"]`);
                        if (input) {
                            let errorP = document.createElement('p');
                            errorP.className = 'error-text text-xs text-[#dc043c] mt-1 font-bold';
                            errorP.innerText = data.errors[field][0];
                            input.parentNode.appendChild(errorP);
                        }
                    }
                    Swal.fire({
                        title: 'Datos Inválidos',
                        html: `<ul class="text-left text-sm text-[#dc043c] list-disc pl-5">${erroresStr}</ul>`,
                        icon: 'warning',
                        confirmButtonColor: '#343c4c'
                    });
                } else {
                    throw new Error(data.message || 'Error en el servidor');
                }
            })
            .catch(error => {
                Swal.fire('Error', error.message, 'error');
            })
            .finally(() => {
                btn.innerHTML = btnOriginalText;
                if (formId === 'passwordForm') btn.disabled = false;
            });
        });
    }

    manejarEnvioAJAX('perfilForm', 'Guardar Información');
    manejarEnvioAJAX('passwordForm', 'Actualizar Contraseña');
});
</script>
@endsection