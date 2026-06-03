@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<div class="relative w-full min-h-screen pb-20 overflow-hidden">
    
    <div class="absolute inset-x-0 bottom-0 w-full h-[250px] md:h-[400px] pointer-events-none z-0" 
         style="background-image: url('{{ asset('img/cesped.png') }}'); background-position: bottom center; background-repeat: repeat-x; background-size: auto 100%; opacity: 1;">
    </div>

    <div class="absolute inset-0 bg-gradient-to-b from-[#f4f4f4] via-[#f4f4f4]/80 to-transparent pointer-events-none z-0"></div>

    <div class="relative z-10 max-w-6xl mx-auto pt-6 px-4 sm:px-6 lg:px-8 my-6">
        
        <div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Finalizar Compra</h1>
                <p class="text-[#343c4c]/60 text-sm mt-1 font-medium">Completa tus datos de envío y procesa tu pago de forma segura.</p>
            </div>
            <a href="{{ route('home') }}#catalogo" class="inline-flex items-center text-[#343c4c]/60 hover:text-[#dc043c] font-black uppercase tracking-widest text-[10px] transition-colors bg-white px-5 py-2.5 rounded-xl shadow-sm border border-[#343c4c]/5 hover:border-[#dc043c]/20">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Seguir Comprando
            </a>
        </div>

        <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-[#343c4c]/10">
                        <div class="bg-[#343c4c] px-8 py-5 border-b-4 border-[#0464a4]">
                            <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                                <svg class="w-5 h-5 mr-3 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                1. Método de Entrega
                            </h3>
                        </div>

                        <div class="p-8 space-y-6">
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">Selecciona la modalidad *</label>
                                <select id="metodo_entrega" name="metodo_entrega" onchange="alternarLogistica()" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-4 text-sm focus:ring-4 focus:ring-[#0464a4]/20 font-bold text-[#343c4c] cursor-pointer shadow-inner transition-all">
                                    <option value="tienda" {{ old('metodo_entrega') == 'tienda' ? 'selected' : '' }}>🏪 Recoger en Tienda Física (Potosí - Gratis)</option>
                                    <option value="delivery" {{ old('metodo_entrega') == 'delivery' ? 'selected' : '' }}>🛵 Servicio de Delivery a Domicilio (Potosí - Bs 5.00)</option>
                                    <option value="envio" {{ old('metodo_entrega') == 'envio' ? 'selected' : '' }}>📦 Envío por Encomienda (Resto de Bolivia)</option>
                                </select>
                            </div>

                            <div id="campos_delivery" class="hidden grid-cols-1 md:grid-cols-2 gap-5 pt-4 border-t-2 border-[#f4f4f4]">
                                <div class="md:col-span-2">
                                    <div class="bg-[#0464a4]/5 border border-[#0464a4]/20 p-5 rounded-2xl flex items-start space-x-4 shadow-sm">
                                        <span class="text-2xl drop-shadow-sm">🛵</span>
                                        <p class="text-xs text-[#0464a4] font-bold leading-relaxed pt-1">Se añadirá un recargo fijo de Bs 5.00 al total de tu compra por el servicio de entrega dentro de la ciudad de Potosí. Ubica el punto de entrega en el mapa.</p>
                                    </div>
                                    <input type="hidden" name="ciudad_delivery" value="Potosí">
                                </div>
                                <div class="md:col-span-2">
                                    <div class="flex justify-between items-end mb-2">
                                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest">Selecciona tu Ubicación en el Mapa *</label>
                                        <button type="button" onclick="usarUbicacionActual()" class="text-[10px] font-bold text-white bg-[#0464a4] px-3 py-1.5 rounded-lg flex items-center shadow-sm hover:bg-[#343c4c] transition">
                                            📍 Usar mi ubicación actual
                                        </button>
                                    </div>
                                    <div id="mapaDelivery" class="w-full h-64 rounded-xl border-2 border-[#f4f4f4] z-0 relative"></div>
                                    <p class="text-xs text-gray-500 mt-1">Puedes arrastrar el marcador rojo a tu ubicación exacta.</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Zona / Barrio (Puedes escribir o usar el mapa) *</label>
                                    <input type="text" name="zona_destino" id="zona_destino" required value="{{ old('zona_destino') }}" placeholder="Ej: San Clemente" 
                                        class="w-full bg-white border-2 border-[#f4f4f4] rounded-xl p-3.5 text-sm focus:border-[#0464a4] focus:ring-0 font-bold text-[#343c4c] transition-colors shadow-sm">
                                    <input type="hidden" name="coordenadas" id="coordenadas" value="-19.5836,-65.7531">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Referencias (Nro de casa, color de puerta, etc) *</label>
                                    <textarea name="direccion_delivery" id="direccion_delivery" rows="2" placeholder="Ej: Casa blanca de 2 pisos, portón negro, timbre derecho..." 
                                        class="w-full bg-white border-2 border-[#f4f4f4] rounded-xl p-3.5 text-sm focus:border-[#0464a4] focus:ring-0 font-bold text-[#343c4c] resize-none transition-colors shadow-sm">{{ old('direccion_delivery') }}</textarea>
                                </div>
                            </div>

                            <div id="campos_encomienda" class="hidden grid-cols-1 gap-5 pt-4 border-t-2 border-[#f4f4f4]">
                                <div class="bg-[#dcb47c]/10 border border-[#dcb47c]/30 p-6 rounded-2xl shadow-sm">
                                    <p class="text-sm font-black text-[#343c4c] mb-2 uppercase tracking-wider flex items-center">
                                        <span class="mr-2 text-xl">📦</span> Modalidad Encomienda
                                    </p>
                                    <p class="text-xs text-[#343c4c]/80 mb-5 font-medium leading-relaxed">El paquete será despachado a través de empresas de transporte. Deberás recogerlo personalmente en la Terminal de Buses o Agencia correspondiente a tu ciudad.</p>

                                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">¿Cómo deseas pagar el transporte? *</label>
                                    <select id="pago_envio" name="pago_envio" onchange="alternarLogistica()" class="w-full bg-white border-2 border-[#dcb47c]/50 rounded-xl p-3.5 text-sm focus:border-[#dcb47c] focus:ring-0 font-bold text-[#343c4c] shadow-sm cursor-pointer transition-colors">
                                        <option value="destino" {{ old('pago_envio') == 'destino' ? 'selected' : '' }}>Pago en Destino (Pagarás a la empresa al recoger tu caja)</option>
                                        <option value="pagado" {{ old('pago_envio') == 'pagado' ? 'selected' : '' }}>Pagar ahora (Añadir Bs 25.00 estimados a tu cobro total)</option>
                                    </select>
                                </div>


                                <div>
                                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Ciudad de Destino en Bolivia *</label>
                                    <select name="ciudad_encomienda" id="ciudad_encomienda" class="w-full bg-white border-2 border-[#f4f4f4] rounded-xl p-3.5 text-sm focus:border-[#0464a4] focus:ring-0 font-bold text-[#343c4c] transition-colors shadow-sm">
                                        <option value="">Seleccione una ciudad...</option>
                                        <option value="La Paz">La Paz</option>
                                        <option value="El Alto">El Alto</option>
                                        <option value="Cochabamba">Cochabamba</option>
                                        <option value="Santa Cruz">Santa Cruz de la Sierra</option>
                                        <option value="Oruro">Oruro</option>
                                        <option value="Sucre">Sucre</option>
                                        <option value="Tarija">Tarija</option>
                                        <option value="Beni">Trinidad (Beni)</option>
                                        <option value="Pando">Cobija (Pando)</option>
                                        <option value="Tupiza">Tupiza</option>
                                        <option value="Villazón">Villazón</option>
                                        <option value="Uyuni">Uyuni</option>
                                        <option value="Llallagua">Llallagua</option>
                                    </select>
                                    <p class="text-[10px] font-bold text-[#dc043c] mt-2 uppercase">⚠️ Nota: Solo se envía a las ciudades mencionadas. El pago es estrictamente mediante QR o Depósito Interbancario.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-[#343c4c]/10">
                        <div class="bg-[#343c4c] px-8 py-5 border-b-4 border-[#dc043c]">
                            <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                                <svg class="w-5 h-5 mr-3 text-[#dc043c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                2. Forma de Pago
                            </h3>
                        </div>

                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($tipoPagos as $tp)
                                    <label class="relative cursor-pointer group">
                                        <input type="radio" name="tipo_pago_id" value="{{ $tp->id }}" data-nombre="{{ $tp->nombre }}" onchange="alternarPasarela('{{ $tp->nombre }}')" required class="peer sr-only" {{ old('tipo_pago_id') == $tp->id ? 'checked' : '' }}>
                                        <div class="border-2 border-[#f4f4f4] rounded-2xl p-5 hover:border-[#0464a4]/50 transition-all peer-checked:border-[#0464a4] peer-checked:bg-[#0464a4]/5 flex items-center space-x-4 shadow-sm group-hover:shadow-md bg-white">
                                            <div class="w-6 h-6 rounded-full border-2 border-[#343c4c]/20 flex items-center justify-center peer-checked:border-[#0464a4] transition-colors bg-white">
                                                <div class="w-3 h-3 rounded-full bg-[#0464a4] opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                            </div>
                                            <div>
                                                <span class="block font-black text-sm text-[#343c4c] uppercase tracking-wider">{{ $tp->nombre }}</span>
                                                <span class="block text-[10px] font-bold text-[#343c4c]/50 mt-1 uppercase tracking-widest">{{ $tp->descripcion }}</span>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>

                            <div id="pasarela_qr" class="hidden bg-[#f4f4f4]/50 rounded-3xl p-8 border-2 border-dashed border-[#dcb47c] text-center space-y-6">
                                <p class="text-sm font-black text-[#343c4c] uppercase tracking-widest flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    Escanea el código QR por el monto exacto
                                </p>
                                
                                <div class="w-64 h-64 mx-auto bg-white p-4 rounded-3xl shadow-xl flex items-center justify-center transform hover:scale-105 transition-transform duration-300">
                                    <img src="{{ asset('qr/Qr.png') }}" alt="QR Interbancario" class="max-w-full max-h-full rounded-xl">
                                </div>
                                
                                <div class="max-w-md mx-auto text-left bg-white p-6 rounded-2xl shadow-md border border-[#343c4c]/5">
                                    <label class="block text-[10px] font-black text-[#dc043c] uppercase tracking-widest mb-3 text-center">Sube tu captura de depósito *</label>
                                    <input type="file" name="comprobante" id="comprobante" accept="image/*" 
                                        class="block w-full text-xs text-[#343c4c] font-medium file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-[#0464a4] file:text-white file:cursor-pointer hover:file:bg-[#343c4c] transition-colors bg-[#f4f4f4] rounded-xl cursor-pointer">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-[#343c4c]/10 sticky top-6">
                        <div class="bg-[#343c4c] px-8 py-5 border-b-4 border-[#dcb47c]">
                            <h3 class="text-sm font-black text-white uppercase tracking-widest text-center">Resumen de Orden</h3>
                        </div>

                        <div class="p-8">
                            <div class="divide-y divide-[#f4f4f4] max-h-60 overflow-y-auto mb-8 pr-2 custom-scrollbar">
                                @foreach($cartItems as $item)
                                    <div class="py-4 flex justify-between items-center text-sm group">
                                        <div class="pr-4 flex items-center">
                                            <div class="w-12 h-12 bg-white rounded-lg flex-shrink-0 mr-3 border border-gray-100 overflow-hidden shadow-sm">
                                                @if($item['imagen_url'])
                                                    <img src="{{ asset('storage/' . $item['imagen_url']) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-50 text-[#343c4c]/20">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-black text-[#343c4c] line-clamp-2 uppercase leading-tight group-hover:text-[#0464a4] transition-colors">{{ $item['nombre'] }}</p>
                                                <span class="text-[10px] font-black text-[#dcb47c] tracking-widest mt-1 block">Cant: {{ $item['cantidad'] }} @if($item['talla'])| Talla: {{ $item['talla'] }}@endif</span>
                                            </div>
                                        </div>
                                        <span class="font-black text-[#0464a4] whitespace-nowrap text-lg">Bs {{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @php
                                $subtotalArticulos = 0;
                                foreach($cartItems as $i) $subtotalArticulos += $i['precio'] * $i['cantidad'];
                            @endphp

                            <div class="mb-8 bg-[#f4f4f4]/50 p-5 rounded-2xl border border-[#f4f4f4]">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-3">¿Tienes un código de descuento?</label>
                                <div class="flex space-x-2">
                                    <input type="text" id="cupon_input" placeholder="Ej: ESPORTS10" class="w-full uppercase font-bold text-[#343c4c] rounded-xl border-2 border-transparent bg-white p-3.5 text-sm focus:border-[#0464a4] focus:ring-0 shadow-sm transition-colors">
                                    <button type="button" onclick="aplicarCuponAJAX()" class="bg-[#343c4c] hover:bg-[#dcb47c] text-white hover:text-[#343c4c] font-black uppercase tracking-wider px-6 rounded-xl text-xs transition-colors shadow-md">Aplicar</button>
                                </div>
                                <p id="cupon_mensaje" class="text-[10px] font-black mt-3 hidden uppercase tracking-wider"></p>
                            </div>

                            <div class="border-t-2 border-[#f4f4f4] pt-6 space-y-4 text-sm">
                                <div class="flex justify-between text-[#343c4c] font-bold items-center">
                                    <span class="uppercase text-[10px] tracking-widest">Subtotal Artículos:</span>
                                    <span class="text-base">Bs {{ number_format($subtotalArticulos, 2) }}</span>
                                </div>

                                <div id="fila_descuento" class="flex justify-between text-[#dc043c] font-black items-center hidden bg-[#dc043c]/5 p-2 rounded-lg -mx-2 px-2 border border-[#dc043c]/10">
                                    <span class="uppercase text-[10px] tracking-widest">Descuento aplicado:</span>
                                    <span id="descuento_display" class="text-lg">- Bs 0.00</span>
                                </div>

                                <div class="flex justify-between text-[#0464a4] font-black items-center">
                                    <span class="uppercase text-[10px] tracking-widest">Logística / Encomienda:</span>
                                    <span id="costo_envio_display" class="text-base">Bs 0.00</span>
                                </div>

                                <div class="flex flex-col items-center justify-center border-t-4 border-[#dcb47c] pt-6 mt-6 bg-[#f4f4f4] rounded-2xl pb-6 shadow-inner">
                                    <span class="text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Total a depositar</span>
                                    <span class="text-5xl font-black text-[#dc043c] drop-shadow-md" id="total_final_display">Bs {{ number_format($subtotalArticulos, 2) }}</span>
                                </div>
                            </div>

                            <input type="hidden" id="cupon_id" name="cupon_id" value="">
                            <input type="hidden" id="descuento_oculto" name="descuento_aplicado" value="0">

                            <button type="submit" class="w-full mt-8 relative group overflow-hidden bg-[#dc043c] text-white font-black uppercase tracking-widest py-5 rounded-2xl shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                                <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                                <span class="relative flex items-center justify-center text-sm drop-shadow-md">
                                    <svg class="w-5 h-5 mr-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Confirmar y Procesar Compra
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Scrollbar minimalista para el resumen de orden */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f4; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #dcb47c; border-radius: 10px; }

    /* Animación de brillo para el botón de compra */
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>

<script>
    const subtotalBase = {{ $subtotalArticulos }};
    let montoDescuentoActivo = 0;

    async function aplicarCuponAJAX() {
        const input = document.getElementById('cupon_input').value;
        const mensaje = document.getElementById('cupon_mensaje');
        const filaDesc = document.getElementById('fila_descuento');
        const descDisplay = document.getElementById('descuento_display');
        const inputOculto = document.getElementById('descuento_oculto');
        const inputCuponId = document.getElementById('cupon_id');

        if (input.trim() === '') return;

        try {
            const res = await fetch('{{ route('cliente.validarCupon') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ codigo: input })
            });

            const data = await res.json();

            mensaje.classList.remove('hidden', 'text-[#dc043c]', 'text-[#0464a4]');
            mensaje.innerText = data.mensaje;

            if (data.valido) {
                mensaje.classList.add('text-[#0464a4]');
                filaDesc.classList.remove('hidden');
                document.getElementById('cupon_input').readOnly = true;

                if (data.cupon.tipo === 'porcentaje') {
                    montoDescuentoActivo = subtotalBase * (data.cupon.valor / 100);
                } else {
                    montoDescuentoActivo = data.cupon.valor;
                }

                if (montoDescuentoActivo > subtotalBase) montoDescuentoActivo = subtotalBase;

                descDisplay.innerText = `- Bs ${montoDescuentoActivo.toFixed(2)}`;
                inputOculto.value = montoDescuentoActivo;
                inputCuponId.value = data.cupon.id;
                alternarLogistica();
            } else {
                mensaje.classList.add('text-[#dc043c]');
                filaDesc.classList.add('hidden');
                montoDescuentoActivo = 0;
                inputOculto.value = 0;
                inputCuponId.value = '';
                document.getElementById('cupon_input').readOnly = false;
                alternarLogistica();
            }
        } catch (error) {
            console.error("Fallo de red:", error);
        }
    }

    let map;
    let marker;

    function initMap() {
        if (!map) {
            // Coordenadas centrales de Potosí por defecto
            map = L.map('mapaDelivery').setView([-19.5836, -65.7531], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            marker = L.marker([-19.5836, -65.7531], {draggable: true}).addTo(map);
            
            // Al terminar de arrastrar el pin, obtener nombre de la zona
            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                document.getElementById('coordenadas').value = position.lat + ',' + position.lng;
                obtenerNombreZona(position.lat, position.lng);
            });
            
            // Al hacer clic en el mapa, mover pin
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.getElementById('coordenadas').value = e.latlng.lat + ',' + e.latlng.lng;
                obtenerNombreZona(e.latlng.lat, e.latlng.lng);
            });
        }
    }

    function usarUbicacionActual() {
        if (navigator.geolocation) {
            Swal.fire({title: 'Ubicando...', text: 'Obteniendo tu ubicación actual', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); }});
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    if(map && marker) {
                        map.setView([lat, lng], 16);
                        marker.setLatLng([lat, lng]);
                        document.getElementById('coordenadas').value = lat + ',' + lng;
                        obtenerNombreZona(lat, lng);
                        Swal.close();
                    }
                },
                function(error) {
                    Swal.fire('Error', 'No pudimos obtener tu ubicación. Por favor mueve el marcador manualmente.', 'error');
                }
            );
        } else {
            alert("Tu navegador no soporta geolocalización.");
        }
    }

    async function obtenerNombreZona(lat, lng) {
        try {
            const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
            const data = await res.json();
            if(data && data.address) {
                // Intentar sacar el barrio o suburbio
                const zona = data.address.neighbourhood || data.address.suburb || data.address.road || data.address.city || 'Potosí';
                if(zona) {
                    document.getElementById('zona_destino').value = zona;
                }
            } else {
                document.getElementById('zona_destino').value = 'Ubicación seleccionada en mapa';
            }
        } catch(e) {
            console.error("Error al obtener zona:", e);
            document.getElementById('zona_destino').value = 'Ubicación seleccionada en mapa';
        }
    }

    function alternarLogistica() {
        const metodo = document.getElementById('metodo_entrega').value;
        const panelDelivery = document.getElementById('campos_delivery');
        const panelEncomienda = document.getElementById('campos_encomienda');
        const pagoEnvioEncomienda = document.getElementById('pago_envio') ? document.getElementById('pago_envio').value : '';
        const zonaDelivery = document.getElementById('zona_destino');
        const direccionDelivery = document.getElementById('direccion_delivery');
        const ciudadEncomienda = document.getElementById('ciudad_encomienda');

        let tarifaAgregada = 0;

        if (metodo === 'tienda') {
            panelDelivery.classList.add('hidden');
            panelDelivery.classList.remove('grid');
            panelEncomienda.classList.add('hidden');
            panelEncomienda.classList.remove('grid');
            zonaDelivery.required = false;
            direccionDelivery.required = false;
            ciudadEncomienda.required = false;
            tarifaAgregada = 0;
        } else if (metodo === 'delivery') {
            panelDelivery.classList.remove('hidden');
            panelDelivery.classList.add('grid');
            panelEncomienda.classList.add('hidden');
            panelEncomienda.classList.remove('grid');
            zonaDelivery.required = true;
            direccionDelivery.required = true;
            ciudadEncomienda.required = false;
            tarifaAgregada = 5;
            // Inicializar mapa si no se hizo
            setTimeout(initMap, 200);
        } else if (metodo === 'envio') {
            panelDelivery.classList.add('hidden');
            panelDelivery.classList.remove('grid');
            panelEncomienda.classList.remove('hidden');
            panelEncomienda.classList.add('grid');
            zonaDelivery.required = false;
            direccionDelivery.required = false;
            ciudadEncomienda.required = true;

            if (pagoEnvioEncomienda === 'pagado') {
                tarifaAgregada = 25;
            } else {
                tarifaAgregada = 0;
            }
        }

        // Bloquear opción de Efectivo si es envío por encomienda
        const radiosPago = document.querySelectorAll('input[name="tipo_pago_id"]');
        radiosPago.forEach(radio => {
            if (metodo === 'envio' && radio.dataset.nombre.toLowerCase() === 'efectivo') {
                radio.disabled = true;
                radio.parentElement.classList.add('opacity-50', 'pointer-events-none', 'grayscale');
                if (radio.checked) {
                    radio.checked = false;
                    const qrRadio = Array.from(radiosPago).find(r => r.dataset.nombre.toLowerCase() === 'qr');
                    if(qrRadio) {
                        qrRadio.checked = true;
                        alternarPasarela(qrRadio.dataset.nombre);
                    }
                }
            } else {
                radio.disabled = false;
                radio.parentElement.classList.remove('opacity-50', 'pointer-events-none', 'grayscale');
            }
        });

        document.getElementById('costo_envio_display').innerText = `Bs ${tarifaAgregada.toFixed(2)}`;
        const totalFinal = subtotalBase - montoDescuentoActivo + tarifaAgregada;
        document.getElementById('total_final_display').innerText = `Bs ${totalFinal.toFixed(2)}`;
    }

    function alternarPasarela(nombreMetodo) {
        const pasarela = document.getElementById('pasarela_qr');
        const comprobante = document.getElementById('comprobante');

        if (nombreMetodo === 'QR') {
            pasarela.classList.remove('hidden');
            comprobante.required = true;
        } else {
            pasarela.classList.add('hidden');
            comprobante.required = false;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        alternarLogistica();
        const checkedRadio = document.querySelector('input[name="tipo_pago_id"]:checked');
        if (checkedRadio) {
            alternarPasarela(checkedRadio.dataset.nombre);
        }

        const form = document.getElementById('checkoutForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const checkedRadio = document.querySelector('input[name="tipo_pago_id"]:checked');
            
            if(checkedRadio && checkedRadio.dataset.nombre.toLowerCase() === 'qr') {
                const totalText = document.getElementById('total_final_display').innerText;
                Swal.fire({
                    title: '¿Confirmar Pago QR?',
                    html: `Revisamos que subiste tu comprobante.<br><br><b>Monto exacto a transferir:</b> <span class="text-2xl text-red-600 font-black">${totalText}</span><br><br>¿Confirmas que realizaste la transferencia por este monto exacto?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#dc043c',
                    cancelButtonColor: '#343c4c',
                    confirmButtonText: 'Sí, enviar comprobante',
                    cancelButtonText: 'Revisar de nuevo'
                }).then((result) => {
                    if (result.isConfirmed) {
                        procesarFormularioAJAX(form);
                    }
                });
            } else {
                procesarFormularioAJAX(form);
            }
        });
    });

    function procesarFormularioAJAX(form) {
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="relative flex items-center justify-center text-sm drop-shadow-md"><svg class="w-5 h-5 mr-3 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Procesando...</span>';
        btn.disabled = true;

        const formData = new FormData(form);
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
            if (response.ok) {
                if (data.success) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: data.message,
                        icon: 'success',
                        allowOutsideClick: false,
                        confirmButtonColor: '#0464a4'
                    }).then(() => {
                        window.location.href = data.redirect || '/';
                    });
                } else {
                    throw new Error(data.message || 'Error al procesar la orden');
                }
            } else if (response.status === 422) {
                let erroresHTML = Object.values(data.errors).flat().map(err => `<li>${err}</li>`).join('');
                Swal.fire({
                    title: 'Por favor revisa los datos',
                    html: `<ul class="text-left text-sm text-[#dc043c] list-disc pl-5 space-y-1">${erroresHTML}</ul>`,
                    icon: 'warning',
                    confirmButtonColor: '#343c4c'
                });
                btn.innerHTML = originalText;
                btn.disabled = false;
            } else {
                throw new Error(data.message || 'Error en el servidor');
            }
        })
        .catch(error => {
            Swal.fire('Error', error.message, 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endsection