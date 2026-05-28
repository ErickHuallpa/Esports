@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto my-6">
    <div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Finalizar Compra</h1>
            <p class="text-[#343c4c]/60 text-sm mt-1 font-medium">Completa tus datos de envío y procesa tu pago de forma segura.</p>
        </div>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- COLUMNA IZQUIERDA: Logística y Pago -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- PANEL 1: MÉTODOS DE ENTREGA -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#343c4c]/10">
                    <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#0464a4]">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            1. Método de Entrega
                        </h3>
                    </div>

                    <div class="p-6 space-y-5">
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">Selecciona la modalidad *</label>
                            <select id="metodo_entrega" name="metodo_entrega" onchange="alternarLogistica()" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer shadow-inner">
                                <option value="tienda" {{ old('metodo_entrega') == 'tienda' ? 'selected' : '' }}>🏪 Recoger en Tienda Física (Potosí - Gratis)</option>
                                <option value="delivery" {{ old('metodo_entrega') == 'delivery' ? 'selected' : '' }}>🛵 Servicio de Delivery a Domicilio (Potosí - Bs 5.00)</option>
                                <option value="envio" {{ old('metodo_entrega') == 'envio' ? 'selected' : '' }}>📦 Envío por Encomienda (Resto de Bolivia)</option>
                            </select>
                        </div>

                        <!-- Opciones Delivery -->
                        <div id="campos_delivery" class="hidden grid-cols-1 md:grid-cols-2 gap-5 pt-3 border-t-2 border-[#f4f4f4]">
                            <div class="md:col-span-2">
                                <div class="bg-[#0464a4]/10 border border-[#0464a4]/20 p-4 rounded-xl flex items-start space-x-3">
                                    <span class="text-xl">🛵</span>
                                    <p class="text-xs text-[#0464a4] font-bold leading-relaxed">Se añadirá un recargo fijo de Bs 5.00 al total de tu compra por el servicio de entrega dentro de la ciudad de Potosí.</p>
                                </div>
                                <input type="hidden" name="ciudad_delivery" value="Potosí">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Zona / Barrio *</label>
                                <input type="text" name="zona_destino" id="zona_destino" value="{{ old('zona_destino') }}" placeholder="Ej: San Clemente" 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Dirección Exacta de tu Casa *</label>
                                <textarea name="direccion_delivery" id="direccion_delivery" rows="2" placeholder="Ej: Calle Chayanta Nro 45, puerta azul..." 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] resize-none">{{ old('direccion_delivery') }}</textarea>
                            </div>
                        </div>

                        <!-- Opciones Encomienda -->
                        <div id="campos_encomienda" class="hidden grid-cols-1 gap-5 pt-3 border-t-2 border-[#f4f4f4]">
                            <div class="bg-[#dcb47c]/20 border border-[#dcb47c]/40 p-5 rounded-xl">
                                <p class="text-sm font-black text-[#343c4c] mb-2 uppercase tracking-wider">📦 Modalidad Encomienda</p>
                                <p class="text-xs text-[#343c4c]/80 mb-4 font-medium">El paquete será despachado a través de empresas de transporte. Deberás recogerlo personalmente en la Terminal de Buses o Agencia correspondiente a tu ciudad.</p>

                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">¿Cómo deseas pagar el transporte? *</label>
                                <select id="pago_envio" name="pago_envio" onchange="alternarLogistica()" class="w-full bg-white border border-white rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#dcb47c] font-bold text-[#343c4c] shadow-sm cursor-pointer">
                                    <option value="destino" {{ old('pago_envio') == 'destino' ? 'selected' : '' }}>Pago en Destino (Pagarás a la empresa al recoger tu caja)</option>
                                    <option value="pagado" {{ old('pago_envio') == 'pagado' ? 'selected' : '' }}>Pagar ahora (Añadir Bs 25.00 estimados a tu cobro total)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Ciudad / Municipio de Destino *</label>
                                <input type="text" name="ciudad_encomienda" id="ciudad_encomienda" value="{{ old('ciudad_encomienda') }}" placeholder="Ej: Betanzos, Tupiza, Oruro..." 
                                    class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: FORMA DE PAGO -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#343c4c]/10">
                    <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#dc043c]">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                            <svg class="w-5 h-5 mr-2 text-[#dc043c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            2. Forma de Pago
                        </h3>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Radios de Pago Personalizados -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($tipoPagos as $tp)
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="tipo_pago_id" value="{{ $tp->id }}" data-nombre="{{ $tp->nombre }}" onchange="alternarPasarela('{{ $tp->nombre }}')" required class="peer sr-only" {{ old('tipo_pago_id') == $tp->id ? 'checked' : '' }}>
                                    <div class="border-2 border-[#f4f4f4] rounded-xl p-5 hover:bg-[#f4f4f4]/50 transition-all peer-checked:border-[#0464a4] peer-checked:bg-[#0464a4]/5 flex items-center space-x-4">
                                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center peer-checked:border-[#0464a4]">
                                            <div class="w-2.5 h-2.5 rounded-full bg-[#0464a4] opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                        </div>
                                        <div>
                                            <span class="block font-black text-sm text-[#343c4c] uppercase tracking-wider">{{ $tp->nombre }}</span>
                                            <span class="block text-xs font-medium text-[#343c4c]/50 mt-1">{{ $tp->descripcion }}</span>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <!-- Formulario de Subida QR -->
                        <div id="pasarela_qr" class="hidden bg-[#f4f4f4] rounded-2xl p-6 border-2 border-dashed border-[#dcb47c] text-center space-y-5">
                            <p class="text-sm font-black text-[#343c4c] uppercase tracking-widest">Escanea el código QR oficial por el monto exacto</p>
                            
                            <div class="w-56 h-56 mx-auto bg-white p-3 rounded-2xl shadow-md flex items-center justify-center transform hover:scale-105 transition-transform">
                                <img src="{{ asset('qr/Qr.png') }}" alt="QR Interbancario" class="max-w-full max-h-full">
                            </div>
                            
                            <div class="max-w-md mx-auto text-left bg-white p-5 rounded-xl shadow-sm border border-[#343c4c]/5">
                                <label class="block text-[10px] font-black text-[#dc043c] uppercase tracking-widest mb-2">Sube tu captura de depósito *</label>
                                <input type="file" name="comprobante" id="comprobante" accept="image/*" 
                                    class="block w-full text-xs text-[#343c4c] font-medium file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-black file:uppercase file:tracking-wider file:bg-[#0464a4] file:text-white file:cursor-pointer hover:file:bg-[#343c4c] transition-colors bg-[#f4f4f4] rounded-lg">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Resumen de Orden (Sticky) -->
            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#343c4c]/10 sticky top-6">
                    <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#dcb47c]">
                        <h3 class="text-sm font-black text-white uppercase tracking-widest text-center">Resumen de Orden</h3>
                    </div>

                    <div class="p-6">
                        <!-- Lista de Productos -->
                        <div class="divide-y divide-[#f4f4f4] max-h-60 overflow-y-auto mb-6 pr-2 custom-scrollbar">
                            @foreach($cartItems as $item)
                                <div class="py-3 flex justify-between items-center text-sm">
                                    <div class="pr-4">
                                        <p class="font-bold text-[#343c4c] line-clamp-1 uppercase">{{ $item['nombre'] }}</p>
                                        <span class="text-[10px] font-black text-[#dcb47c] tracking-widest">Cant: {{ $item['cantidad'] }} @if($item['talla'])| Talla: {{ $item['talla'] }}@endif</span>
                                    </div>
                                    <span class="font-black text-[#0464a4] whitespace-nowrap">Bs {{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        @php
                            $subtotalArticulos = 0;
                            foreach($cartItems as $i) $subtotalArticulos += $i['precio'] * $i['cantidad'];
                        @endphp

                        <!-- Aplicar Cupón -->
                        <div class="mb-6 bg-[#f4f4f4] p-4 rounded-xl">
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">¿Tienes un código de descuento?</label>
                            <div class="flex space-x-2">
                                <input type="text" id="cupon_input" placeholder="Ej: ESPORTS10" class="w-full uppercase font-bold text-[#343c4c] rounded-lg border-none p-3 text-sm focus:ring-2 focus:ring-[#0464a4]">
                                <button type="button" onclick="aplicarCuponAJAX()" class="bg-[#343c4c] hover:bg-[#dcb47c] text-white hover:text-[#343c4c] font-black uppercase tracking-wider px-5 rounded-lg text-xs transition-colors shadow-sm">Aplicar</button>
                            </div>
                            <p id="cupon_mensaje" class="text-[10px] font-black mt-2 hidden uppercase tracking-wider"></p>
                        </div>

                        <!-- Tabla de Totales -->
                        <div class="border-t-2 border-[#f4f4f4] pt-5 space-y-3 text-sm">
                            <div class="flex justify-between text-[#343c4c] font-bold">
                                <span>Subtotal Artículos:</span>
                                <span>Bs {{ number_format($subtotalArticulos, 2) }}</span>
                            </div>

                            <div id="fila_descuento" class="flex justify-between text-[#dc043c] font-black hidden">
                                <span>Descuento aplicado:</span>
                                <span id="descuento_display">- Bs 0.00</span>
                            </div>

                            <div class="flex justify-between text-[#0464a4] font-black">
                                <span>Logística / Encomienda:</span>
                                <span id="costo_envio_display">Bs 0.00</span>
                            </div>

                            <div class="flex flex-col items-center justify-center border-t-4 border-[#dcb47c] pt-5 mt-5 bg-[#f4f4f4] rounded-xl pb-5 shadow-inner">
                                <span class="text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Total a depositar</span>
                                <span class="text-4xl font-black text-[#dc043c] drop-shadow-sm" id="total_final_display">Bs {{ number_format($subtotalArticulos, 2) }}</span>
                            </div>
                        </div>

                        <input type="hidden" id="cupon_id" name="cupon_id" value="">
                        <input type="hidden" id="descuento_oculto" name="descuento_aplicado" value="0">

                        <button type="submit" class="w-full mt-6 bg-[#dc043c] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-4 rounded-xl shadow-lg transition-colors text-sm transform hover:-translate-y-0.5">
                            Procesar Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    /* Scrollbar minimalista para el resumen de orden */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f4; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #dcb47c; border-radius: 10px; }
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

        document.getElementById('costo_envio_display').innerText = `Bs ${tarifaAgregada.toFixed(2)}`;
        const totalFinal = subtotalBase - montoDescuentoActivo + tarifaAgregada;
        document.getElementById('total_final_display').innerText = `Bs ${totalFinal.toFixed(2)}`;
    }

    function alternarPasarela(nombreMetodo) {
        const pasarela = document.getElementById('pasarela_qr');
        const comprobante = document.getElementById('comprobante');

        // Los radios ahora tienen styling via Tailwind `peer`, solo nos ocupamos de mostrar el div del QR
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
    });
</script>
@endsection