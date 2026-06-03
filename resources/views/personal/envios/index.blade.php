@extends('layouts.app')

@section('content')
<!-- Leaflet & Routing Machine -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<div class="max-w-7xl mx-auto my-6">
    
    <div class="mb-8 flex flex-col gap-2 border-b-2 border-[#f4f4f4] pb-5">
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Logística y Despachos</h1>
        <p class="text-[#343c4c]/60 text-sm font-medium">Gestiona la salida de almacén de todas las órdenes confirmadas, incluyendo envíos a domicilio y recojos en tienda local.</p>
    </div>

    <!-- BARRA DE FILTROS -->
    <div class="mb-8 flex flex-wrap items-center gap-4 bg-white p-4 rounded-2xl border border-[#343c4c]/10 shadow-sm">
        <div class="flex-1 min-w-[200px] relative">
            <svg class="absolute left-3 top-3.5 w-4 h-4 text-[#343c4c]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" id="filtro_cliente" placeholder="Buscar por cliente..." class="w-full bg-[#f4f4f4] border-none rounded-xl py-3 pl-10 pr-4 text-sm font-bold text-[#343c4c] focus:ring-2 focus:ring-[#0464a4]">
        </div>
        
        <div class="w-full sm:w-auto">
            <select id="filtro_tipo" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm font-bold text-[#343c4c] focus:ring-2 focus:ring-[#0464a4] cursor-pointer">
                <option value="todos">🚚 Todos los Tipos</option>
                <option value="envio">Solo Envíos a Domicilio/Agencia</option>
                <option value="recojo">Solo Recojos en Local</option>
            </select>
        </div>

        <div class="w-full sm:w-auto">
            <select id="filtro_estado" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm font-bold text-[#343c4c] focus:ring-2 focus:ring-[#0464a4] cursor-pointer">
                <option value="todos">📦 Todos los Estados</option>
                <option value="preparando">Preparando</option>
                <option value="en_camino">En Camino / Agencia</option>
                <option value="problema">Problema Logístico</option>
            </select>
        </div>

        <div class="w-full sm:w-auto flex items-center">
            <label class="flex items-center cursor-pointer select-none bg-[#f4f4f4] p-3 rounded-xl">
                <div class="relative">
                    <input type="checkbox" id="filtro_entregados" class="sr-only">
                    <div class="block bg-gray-300 w-10 h-6 rounded-full transition-colors duration-300 ease-in-out" id="entregados_bg"></div>
                    <div class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform duration-300 ease-in-out"></div>
                </div>
                <div class="ml-3 text-sm font-black text-[#343c4c] uppercase tracking-widest">
                    Mostrar Entregados
                </div>
            </label>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="contenedor_ordenes">
        @forelse($ordenes as $orden)
            @php 
                $estado = $orden->estado_orden; 
                $esEntregado = str_contains($estado, 'Entregada');
                $tipoOrden = $orden->envio ? 'envio' : 'recojo';
                $categoriaEstado = 'otro';
                if(str_contains($estado, 'Preparando') || str_contains($estado, 'Listo')) $categoriaEstado = 'preparando';
                if(str_contains($estado, 'Tránsito') || str_contains($estado, 'Llegó')) $categoriaEstado = 'en_camino';
                if(str_contains($estado, 'Problema')) $categoriaEstado = 'problema';
                if($esEntregado) $categoriaEstado = 'entregado';
            @endphp
            
            <div class="tarjeta-orden bg-white rounded-3xl border border-[#343c4c]/10 shadow-xl overflow-hidden flex flex-col justify-between transition-all hover:-translate-y-1 hover:shadow-2xl {{ $esEntregado ? 'hidden' : '' }}" 
                data-cliente="{{ strtolower($orden->venta->user->persona->nombre . ' ' . $orden->venta->user->persona->apellidos) }}"
                data-tipo="{{ $tipoOrden }}"
                data-estado="{{ $categoriaEstado }}"
                data-esentregado="{{ $esEntregado ? 'true' : 'false' }}">
                
                <div class="bg-[#343c4c] px-4 py-3 border-b-4 {{ $orden->envio ? 'border-[#0464a4]' : 'border-[#dcb47c]' }} flex justify-between items-center">
                    <span class="text-xs font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1.5 {{ $orden->envio ? 'text-[#0464a4]' : 'text-[#dcb47c]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        #{{ $orden->id }}
                    </span>
                    
                    @if(str_contains($estado, 'Preparando'))
                        <span class="px-2 py-1 bg-[#dcb47c] text-[#343c4c] text-[8px] font-black rounded uppercase tracking-wider shadow-sm">Preparando</span>
                    @elseif(str_contains($estado, 'Tránsito'))
                        <span class="px-2 py-1 bg-[#0464a4] text-white text-[8px] font-black rounded uppercase tracking-wider shadow-sm">En Camino</span>
                    @elseif(str_contains($estado, 'Listo para Recojo'))
                        <span class="px-2 py-1 bg-purple-100 text-purple-800 text-[8px] font-black rounded uppercase tracking-wider border border-purple-200">Listo (Tienda)</span>
                    @elseif(str_contains($estado, 'Llegó'))
                        <span class="px-2 py-1 bg-[#0464a4]/10 text-[#0464a4] text-[8px] font-black rounded uppercase tracking-wider border border-[#0464a4]/20">En Agencia</span>
                    @elseif(str_contains($estado, 'Completada'))
                        <span class="px-2 py-1 bg-green-50 text-green-700 text-[8px] font-black rounded uppercase tracking-wider border border-green-200">Entregado</span>
                    @elseif(str_contains($estado, 'Problema'))
                        <span class="px-2 py-1 bg-[#dc043c] text-white text-[8px] font-black rounded uppercase tracking-wider shadow-sm">Problema</span>
                    @else
                        <span class="px-2 py-1 bg-[#f4f4f4] text-[#343c4c] text-[8px] font-black rounded uppercase tracking-wider border border-[#343c4c]/20">{{ $estado ?? 'Pendiente' }}</span>
                    @endif
                </div>
                
                <div class="p-4 relative flex-1">
                    @if($orden->envio)
                        <svg class="absolute top-4 right-4 w-12 h-12 text-[#0464a4]/10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    @else
                        <svg class="absolute top-4 right-4 w-12 h-12 text-[#dcb47c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    @endif

                    <h3 class="text-lg font-black text-[#343c4c] uppercase tracking-wide leading-tight relative z-10">{{ $orden->venta->user->persona->nombre }} {{ $orden->venta->user->persona->apellidos }}</h3>
                    
                    <p class="text-[11px] text-[#343c4c]/60 font-bold mb-5 uppercase tracking-widest flex items-center mt-1 relative z-10">
                        <svg class="w-3.5 h-3.5 mr-1.5 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        Telf: <span class="ml-1 text-[#343c4c]">{{ $orden->venta->user->persona->telefono ?? 'No especificado' }}</span>
                    </p>
                    
                    @if($orden->envio)
                        <div class="bg-[#f4f4f4] p-4 rounded-2xl border border-[#343c4c]/5 text-sm space-y-3">
                            @if(str_contains($orden->envio->ciudad_destino, 'Potosí'))
                                <span class="inline-block px-2 py-1 bg-[#dc043c] text-white text-[9px] font-black uppercase tracking-widest rounded mb-1">🛵 Requiere Delivery</span>
                            @else
                                <span class="inline-block px-2 py-1 bg-[#0464a4] text-white text-[9px] font-black uppercase tracking-widest rounded mb-1">🚚 Requiere Envío</span>
                            @endif
                            <div>
                                <strong class="text-[9px] uppercase tracking-widest text-[#0464a4] block mb-1">📍 Destino</strong>
                                <p class="font-bold text-[#343c4c] leading-tight">{{ $orden->envio->ciudad_destino }} <span class="text-[#343c4c]/50 text-xs ml-1 font-medium">({{ $orden->envio->zona_destino ?? 'Sin zona' }})</span></p>
                            </div>
                            <div>
                                <strong class="text-[9px] uppercase tracking-widest text-[#0464a4] block mb-1">🏠 Dirección Exacta</strong>
                                <p class="font-bold text-[#343c4c] leading-tight">{{ $orden->envio->direccion_destino }}</p>
                            </div>
                            @if(str_contains($orden->envio->ciudad_destino, 'Potosí') && $orden->envio->ruta)
                            <div class="mt-3">
                                <button type="button" onclick="abrirMapaRuta('{{ $orden->envio->ruta }}', '{{ $orden->venta->user->persona->nombre }}')" class="w-full flex items-center justify-center bg-white border-2 border-[#0464a4]/20 hover:border-[#0464a4] hover:bg-[#0464a4]/5 text-[#0464a4] font-black uppercase tracking-widest py-2 rounded-xl text-[10px] transition-colors shadow-sm">
                                    🗺️ Ver Ruta de Entrega
                                </button>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-[#dcb47c]/10 p-4 rounded-2xl border border-[#dcb47c]/30 text-sm space-y-2 text-center h-[130px] flex flex-col justify-center items-center">
                            <span class="inline-block px-2 py-1 bg-[#343c4c] text-[#dcb47c] text-[9px] font-black uppercase tracking-widest rounded mb-2 shadow-sm">🏪 Retiro Local</span>
                            <p class="font-black text-[#343c4c] leading-tight uppercase">El cliente pasará a recoger su pedido por la Tienda en Potosí.</p>
                        </div>
                    @endif
                </div>

                <form action="{{ route('personal.envios.update', $orden->id) }}" method="POST" class="form-estado px-4 py-4 bg-[#f4f4f4]/50 border-t border-[#f4f4f4] space-y-3">
                    @csrf
                    @method('PUT')
                    
                    @php
                        $nivelActual = 0;
                        if(str_contains($estado, 'Preparando')) $nivelActual = 1;
                        elseif(str_contains($estado, 'Tránsito') || str_contains($estado, 'Listo')) $nivelActual = 2;
                        elseif(str_contains($estado, 'Llegó')) $nivelActual = 3;
                        elseif(str_contains($estado, 'Entregada') || str_contains($estado, 'Completada')) $nivelActual = 4;
                    @endphp
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Actualizar Estado</label>
                        <select name="estado_logistico" required class="w-full bg-white border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer shadow-sm">
                            <option value="preparando" {{ str_contains($estado, 'Preparando') ? 'selected' : '' }} {{ $nivelActual > 1 ? 'disabled class=text-gray-300' : '' }}>📦 Preparando Empaque</option>
                            
                            @if($orden->envio)
                                @if(str_contains($orden->envio->ciudad_destino, 'Potosí'))
                                    <option value="en_camino" {{ str_contains($estado, 'Tránsito') ? 'selected' : '' }} {{ $nivelActual > 2 ? 'disabled class=text-gray-300' : '' }}>🛵 En Camino a tu domicilio</option>
                                    <option value="llego_destino" {{ str_contains($estado, 'Llegó') ? 'selected' : '' }} {{ $nivelActual > 3 ? 'disabled class=text-gray-300' : '' }}>📍 Llegó a tu ubicación</option>
                                @else
                                    <option value="en_camino" {{ str_contains($estado, 'Tránsito') ? 'selected' : '' }} {{ $nivelActual > 2 ? 'disabled class=text-gray-300' : '' }}>🚚 En Camino / Despachado</option>
                                    <option value="llego_destino" {{ str_contains($estado, 'Llegó') ? 'selected' : '' }} {{ $nivelActual > 3 ? 'disabled class=text-gray-300' : '' }}>🏢 Llegó al Destino (Agencia)</option>
                                @endif
                            @else
                                <option value="listo_tienda" {{ str_contains($estado, 'Listo') ? 'selected' : '' }} {{ $nivelActual > 2 ? 'disabled class=text-gray-300' : '' }}>🏪 Listo para Recojo en Tienda</option>
                            @endif
                            
                            <option value="entregado" {{ str_contains($estado, 'Entregada') ? 'selected' : '' }}>✅ Completado / Entregado</option>
                            <option value="fallido" {{ str_contains($estado, 'Problema') ? 'selected' : '' }}>❌ Fallido / Retenido</option>
                        </select>
                    </div>

                    @if($orden->envio)
                        @php 
                            $guiaAuto = $orden->envio->codigo_seguimiento ? $orden->envio->codigo_seguimiento : 'ESP-' . $orden->id . '-' . date('ymd');
                        @endphp
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Guía Automática</label>
                            <input type="text" name="codigo_seguimiento" value="{{ $guiaAuto }}" readonly
                                class="w-full bg-[#e8e8e8] border-none rounded-xl p-2.5 text-xs focus:ring-0 font-black text-[#343c4c]/60 cursor-not-allowed shadow-inner">
                        </div>

                        @if(str_contains($orden->envio->ciudad_destino, 'Potosí'))
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Empresa / Transporte</label>
                                <input type="text" name="responsable_entrega" value="Delivery E-SPORTS" readonly class="w-full bg-[#0464a4]/10 border-none rounded-xl p-2.5 text-xs focus:ring-0 font-black text-[#0464a4] cursor-not-allowed shadow-inner">
                            </div>
                        @else
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Empresa / Transporte</label>
                                <select name="responsable_entrega" class="transporte-select w-full bg-white border-none rounded-xl p-2.5 text-xs focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] shadow-sm cursor-pointer" data-destino="{{ $orden->envio->ciudad_destino }}" data-selected="{{ $orden->envio->responsable_entrega }}">
                                    <option value="">Selecciona Empresa...</option>
                                </select>
                            </div>
                        @endif
                    @endif

                    <button type="submit" class="w-full mt-1 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-3 rounded-xl shadow-md transition-all text-[10px] transform hover:-translate-y-0.5">
                        Guardar Actualización
                    </button>
                </form>
            </div>
        @empty
            <div class="col-span-full bg-white p-16 text-center rounded-3xl border-2 border-dashed border-[#dcb47c] shadow-lg relative z-10">
                <svg class="w-24 h-24 mx-auto text-[#dcb47c] mb-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                <h3 class="text-2xl font-black text-[#343c4c] uppercase tracking-wide">Órdenes al Día</h3>
                <p class="text-[#343c4c]/60 mt-2 font-medium">No hay pedidos confirmados que requieran tu atención en este momento.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // LÓGICA DE FILTROS
        const filtroCliente = document.getElementById('filtro_cliente');
        const filtroTipo = document.getElementById('filtro_tipo');
        const filtroEstado = document.getElementById('filtro_estado');
        const filtroEntregados = document.getElementById('filtro_entregados');
        const entregadosBg = document.getElementById('entregados_bg');
        const entregadosDot = document.querySelector('.dot');
        const tarjetas = document.querySelectorAll('.tarjeta-orden');

        // RESTAURAR ESTADO DE FILTROS DESDE SESSION STORAGE
        if(sessionStorage.getItem('desp_filtroCliente')) filtroCliente.value = sessionStorage.getItem('desp_filtroCliente');
        if(sessionStorage.getItem('desp_filtroTipo')) filtroTipo.value = sessionStorage.getItem('desp_filtroTipo');
        if(sessionStorage.getItem('desp_filtroEstado')) filtroEstado.value = sessionStorage.getItem('desp_filtroEstado');
        if(sessionStorage.getItem('desp_filtroEntregados')) {
            filtroEntregados.checked = sessionStorage.getItem('desp_filtroEntregados') === 'true';
            if(filtroEntregados.checked) {
                entregadosBg.classList.replace('bg-gray-300', 'bg-[#0464a4]');
                entregadosDot.classList.add('translate-x-4');
            }
        }

        function aplicarFiltros() {
            const clienteVal = filtroCliente.value.toLowerCase().trim();
            const tipoVal = filtroTipo.value;
            const estadoVal = filtroEstado.value;
            const mostrarEntregados = filtroEntregados.checked;

            // GUARDAR ESTADO DE FILTROS EN SESSION STORAGE
            sessionStorage.setItem('desp_filtroCliente', filtroCliente.value);
            sessionStorage.setItem('desp_filtroTipo', filtroTipo.value);
            sessionStorage.setItem('desp_filtroEstado', filtroEstado.value);
            sessionStorage.setItem('desp_filtroEntregados', filtroEntregados.checked);

            tarjetas.forEach(tarjeta => {
                const tCliente = tarjeta.getAttribute('data-cliente');
                const tTipo = tarjeta.getAttribute('data-tipo');
                const tEstado = tarjeta.getAttribute('data-estado');
                const tEsEntregado = tarjeta.getAttribute('data-esentregado') === 'true';

                let coincide = true;

                if (clienteVal !== '' && !tCliente.includes(clienteVal)) coincide = false;
                if (tipoVal !== 'todos' && tTipo !== tipoVal) coincide = false;
                if (estadoVal !== 'todos' && tEstado !== estadoVal) coincide = false;
                
                // Si es entregado y el toggle está apagado, lo ocultamos independientemente del resto
                if (tEsEntregado && !mostrarEntregados) coincide = false;

                if (coincide) {
                    tarjeta.classList.remove('hidden');
                } else {
                    tarjeta.classList.add('hidden');
                }
            });
        }

        filtroCliente.addEventListener('input', aplicarFiltros);
        filtroTipo.addEventListener('change', aplicarFiltros);
        filtroEstado.addEventListener('change', aplicarFiltros);
        
        filtroEntregados.addEventListener('change', function() {
            if(this.checked) {
                entregadosBg.classList.replace('bg-gray-300', 'bg-[#0464a4]');
                entregadosDot.classList.add('translate-x-4');
            } else {
                entregadosBg.classList.replace('bg-[#0464a4]', 'bg-gray-300');
                entregadosDot.classList.remove('translate-x-4');
            }
            aplicarFiltros();
        });

        // Aplicar filtros inmediatamente al cargar la página
        aplicarFiltros();

        // AJAX FORM SUBMIT
        const forms = document.querySelectorAll('.form-estado');
        forms.forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                const originalText = btn.innerText;
                btn.innerText = 'Guardando...';
                btn.disabled = true;

                try {
                    const formData = new FormData(form);
                    const res = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    const data = await res.json();
                    if(data.success) {
                        btn.innerText = '¡Guardado! ✓';
                        btn.classList.replace('bg-[#0464a4]', 'bg-green-600');
                        btn.classList.replace('hover:bg-[#343c4c]', 'hover:bg-green-700');
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                        
                        const select = form.querySelector('select[name="estado_logistico"]');
                        const currentIndex = select.selectedIndex;
                        for(let i = 0; i < currentIndex; i++) {
                            select.options[i].disabled = true;
                            select.options[i].classList.add('text-gray-300');
                        }
                        
                        setTimeout(() => {
                            btn.innerText = originalText;
                            btn.disabled = false;
                            btn.classList.replace('bg-green-600', 'bg-[#0464a4]');
                            btn.classList.replace('hover:bg-green-700', 'hover:bg-[#343c4c]');
                        }, 3000);
                    } else {
                        throw new Error(data.message || 'Error desconocido');
                    }
                } catch(e) {
                    btn.innerText = originalText;
                    btn.disabled = false;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire('Error', e.message || 'Error de conexión', 'error');
                    } else {
                        alert(e.message);
                    }
                }
            });
        });

        // LÓGICA DE SELECT DINÁMICO DE TRANSPORTE
        const empresasDestino = {
            'sucre': ['Transtin Dil Rey', '6 de Agosto', 'Emperador', 'Trans Potosí'],
            'la paz': ['Trans Copacabana S.A.', 'El Dorado', 'Trans Illimani', 'Bolívar'],
            'cochabamba': ['Danubio', 'Bolívar', 'El Dorado', 'Trans Copacabana'],
            'santa cruz': ['Flota Urkupiña', 'Trans Copacabana S.A.', 'Amador', 'Bolívar'],
            'oruro': ['Trans Azul', 'Bustillo', 'El Dorado'],
            'tarija': ['San Lorenzo', 'Expreso del Sur'],
            'villazon': ['Boquerón', 'Trans Villazón'],
            'tupiza': ['Trans Tupiza', '10 de Noviembre']
        };
        const empresasDefault = ['Transporte Local Rápidos', 'Sindicato 10 de Noviembre', 'Encomiendas del Sur'];

        const selectsTransporte = document.querySelectorAll('.transporte-select');
        selectsTransporte.forEach(select => {
            const destino = select.getAttribute('data-destino').toLowerCase();
            const seleccionado = select.getAttribute('data-selected');
            
            let opciones = empresasDefault;
            for (const ciudad in empresasDestino) {
                if (destino.includes(ciudad)) {
                    opciones = empresasDestino[ciudad];
                    break;
                }
            }

            // Agregamos opciones al select
            opciones.forEach(empresa => {
                const opt = document.createElement('option');
                opt.value = empresa;
                opt.textContent = empresa;
                if (seleccionado === empresa) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });

            // Si hay un seleccionado que no estaba en la lista, lo agregamos para no perder datos
            if (seleccionado && !opciones.includes(seleccionado)) {
                const opt = document.createElement('option');
                opt.value = seleccionado;
                opt.textContent = seleccionado + ' (Registrado)';
                opt.selected = true;
                select.appendChild(opt);
            }
        });
    });
</script>

<!-- Modal Mapa Ruta -->
<div id="modalRuta" class="fixed inset-0 bg-[#343c4c]/80 backdrop-blur-sm hidden flex items-center justify-center" style="z-index: 9999;">
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col" style="width: 90%; max-width: 56rem;">
        <div class="bg-[#343c4c] px-6 py-4 flex justify-between items-center border-b-4 border-[#0464a4]">
            <h3 class="text-white font-black uppercase tracking-widest flex items-center">
                <span class="text-2xl mr-2">🗺️</span> Ruta de Entrega
            </h3>
            <button onclick="cerrarMapaRuta()" class="text-white/60 hover:text-white transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <div class="p-4 flex-1 relative bg-[#f4f4f4]">
            <div id="mapaEnrutamiento" class="w-full rounded-2xl shadow-inner border border-[#343c4c]/10" style="min-height: 500px; z-index: 1;"></div>
        </div>
    </div>
</div>

<script>
    let mapRuta = null;
    let controlRuta = null;

    function abrirMapaRuta(coordenadasStr, clienteNombre) {
        document.getElementById('modalRuta').classList.remove('hidden');
        const coords = coordenadasStr.split(',');
        const lat = parseFloat(coords[0]);
        const lng = parseFloat(coords[1]);

        if(!mapRuta) {
            mapRuta = L.map('mapaEnrutamiento').setView([-19.569050, -65.764490], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mapRuta);
        } else {
            if(controlRuta) {
                mapRuta.removeControl(controlRuta);
            }
        }

        // Trazar ruta
        controlRuta = L.Routing.control({
            waypoints: [
                L.latLng(-19.569050438738856, -65.76449008843426), // Origen (Tienda)
                L.latLng(lat, lng) // Destino
            ],
            routeWhileDragging: false,
            addWaypoints: false,
            fitSelectedRoutes: true,
            showAlternatives: false,
            lineOptions: {
                styles: [{color: '#0464a4', opacity: 0.8, weight: 6}]
            },
            createMarker: function(i, wp, nWps) {
                if (i === 0) {
                    return L.marker(wp.latLng).bindPopup('<b>🏢 Tienda Esports</b><br>Punto de partida').openPopup();
                } else if (i === nWps - 1) {
                    return L.marker(wp.latLng).bindPopup(`<b>📍 Destino</b><br>Cliente: ${clienteNombre}`);
                }
            }
        }).addTo(mapRuta);

        // Ajustar el mapa al modal
        setTimeout(() => {
            mapRuta.invalidateSize();
        }, 300);
    }

    function cerrarMapaRuta() {
        document.getElementById('modalRuta').classList.add('hidden');
        if(controlRuta) {
            mapRuta.removeControl(controlRuta);
            controlRuta = null;
        }
    }
</script>
@endsection