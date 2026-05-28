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
                    
                    <form action="{{ route('perfil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-8 p-5 bg-[#f4f4f4]/50 border border-[#f4f4f4] rounded-2xl flex items-center space-x-5">
                            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center overflow-hidden border-2 border-[#dcb47c] shadow-sm flex-shrink-0">
                                @if($user->foto_perfil)
                                    <img src="{{ asset('storage/' . $user->foto_perfil) }}" class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-[#343c4c]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                @endif
                            </div>
                            <div class="flex-grow">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Cambiar Foto de Perfil</label>
                                <input type="file" name="foto_perfil" accept="image/*" 
                                    class="w-full text-xs text-[#343c4c] font-medium file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-[#343c4c] file:text-white file:cursor-pointer hover:file:bg-[#0464a4] transition-all bg-white rounded-xl shadow-sm border border-[#343c4c]/5 p-1">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Cédula de Identidad (C.I.) *</label>
                                <input type="text" name="ci" value="{{ old('ci', $user->persona->ci) }}" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombre de Usuario *</label>
                                <input type="text" name="username" value="{{ old('username', $user->username) }}" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombres *</label>
                                <input type="text" name="nombre" value="{{ old('nombre', $user->persona->nombre) }}" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Apellidos *</label>
                                <input type="text" name="apellidos" value="{{ old('apellidos', $user->persona->apellidos) }}" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Correo Electrónico *</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Teléfono Móvil</label>
                                <input type="text" name="telefono" value="{{ old('telefono', $user->persona->telefono) }}" 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', optional($user->persona->fecha_nacimiento)->format('Y-m-d')) }}" 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Dirección Habitual</label>
                                <input type="text" name="direccion" value="{{ old('direccion', $user->persona->direccion) }}" 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t-2 border-[#f4f4f4]">
                            <button type="submit" class="bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3.5 px-8 rounded-xl shadow-lg transition-all text-xs transform hover:-translate-y-0.5">
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
                    
                    <form action="{{ route('perfil.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Contraseña Actual *</label>
                                <input type="password" name="current_password" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] font-bold text-[#343c4c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nueva Contraseña *</label>
                                <input type="password" name="password" required 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] font-bold text-[#343c4c]">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Confirmar Nueva *</label>
                                <input type="password" name="password_confirmation" required 
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
@endsection