@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto my-10">
    
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-[#343c4c]/10">
        
        <div class="bg-[#343c4c] px-8 py-6 border-b-4 border-[#dcb47c] text-center md:text-left flex flex-col md:flex-row justify-between items-center">
            <div>
                <h1 class="text-2xl lg:text-3xl font-black text-[#f4f4f4] tracking-tight uppercase">Configuración Inicial</h1>
                <p class="text-[#dcb47c] text-sm mt-1 font-bold tracking-wider">Inicialización del Perfil Administrador (Root)</p>
            </div>
            <div class="hidden md:block">
                <svg class="w-12 h-12 text-[#f4f4f4]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-[#dc043c]/10 border-l-4 border-[#dc043c] p-6 m-8 mb-0 rounded-r-xl">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-[#dc043c] mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-black text-[#dc043c] uppercase tracking-wider text-sm">Errores de Validación</span>
                </div>
                <ul class="list-disc pl-7 text-sm font-medium text-[#343c4c] space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.register.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 p-8 lg:p-10">
                
                <div class="space-y-5">
                    <h3 class="text-lg font-black text-[#343c4c] uppercase tracking-widest border-b-2 border-[#f4f4f4] pb-3 flex items-center mb-6">
                        <svg class="w-5 h-5 mr-3 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Datos Personales
                    </h3>
                    
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombre(s) *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Apellidos *</label>
                        <input type="text" name="apellidos" value="{{ old('apellidos') }}" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">C.I. *</label>
                            <input type="text" name="ci" value="{{ old('ci') }}" required placeholder="Ej: 8344122" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Celular</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Dirección</label>
                        <textarea name="direccion" rows="2" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] resize-none">{{ old('direccion') }}</textarea>
                    </div>
                </div>

                <div class="bg-[#f4f4f4]/50 p-6 rounded-2xl border border-[#343c4c]/5 space-y-5 h-fit">
                    <h3 class="text-lg font-black text-[#343c4c] uppercase tracking-widest border-b-2 border-white pb-3 flex items-center mb-6">
                        <svg class="w-5 h-5 mr-3 text-[#dc043c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Credenciales de Acceso
                    </h3>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Usuario (Nickname) *</label>
                        <input type="text" name="username" value="{{ old('username') }}" required placeholder="Ej: admin_root" class="w-full bg-white border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c] shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Correo Corporativo *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@esports.com" class="w-full bg-white border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c] shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Contraseña Maestra *</label>
                        <input type="password" name="password" required class="w-full bg-white border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c] shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Confirmar Contraseña *</label>
                        <input type="password" name="password_confirmation" required class="w-full bg-white border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c] shadow-sm">
                    </div>
                </div>
            </div>

            <div class="px-8 py-6 bg-[#f4f4f4] border-t border-[#343c4c]/10 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[11px] font-bold text-[#343c4c]/50 uppercase tracking-widest text-center md:text-left">
                    Verifica todos los datos antes de proceder.
                </p>
                <button type="submit" class="w-full md:w-auto bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3.5 px-8 rounded-xl shadow-lg transition-colors text-sm">
                    Inicializar Administrador
                </button>
            </div>
        </form>
    </div>
</div>
@endsection