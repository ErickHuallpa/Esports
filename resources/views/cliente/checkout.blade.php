@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Finalizar Compra (Checkout)</h1>
        <p class="text-gray-500 text-sm">Completa tus datos de envío y procesa tu pago electrónico.</p>
    </div>

    <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border p-6 shadow-sm space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2">1. Método de Entrega</h3>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Selecciona la modalidad *</label>
                        <select id="metodo_entrega" name="metodo_entrega" onchange="alternarLogistica()" required class="w-full rounded-lg border-gray-300 border p-2.5 text-sm bg-white focus:ring-blue-500">
                            <option value="tienda" {{ old('metodo_entrega') == 'tienda' ? 'selected' : '' }}>Recoger en Tienda Física (Potosí - Gratis)</option>
                            <option value="delivery" {{ old('metodo_entrega') == 'delivery' ? 'selected' : '' }}>Servicio de Delivery a Domicilio (Potosí - Bs 5.00)</option>
                            <option value="envio" {{ old('metodo_entrega') == 'envio' ? 'selected' : '' }}>Envío por Encomienda (Resto de Bolivia)</option>
                        </select>
                    </div>

                    <div id="campos_delivery" class="hidden grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div class="md:col-span-2">
                            <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 mb-2">
                                <p class="text-xs text-blue-800 font-medium">Se añadirá un recargo fijo de Bs 5.00 al total de tu compra por el servicio de entrega en Potosí.</p>
                            </div>
                            <input type="hidden" name="ciudad_delivery" value="Potosí">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Zona / Barrio *</label>
                            <input type="text" name="zona_destino" id="zona_destino" value="{{ old('zona_destino') }}" placeholder="Ej: San Clemente" class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Dirección Exacta de tu Casa *</label>
                            <textarea name="direccion_delivery" id="direccion_delivery" rows="2" placeholder="Ej: Calle Chayanta Nro 45, puerta azul..." class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-blue-500">{{ old('direccion_delivery') }}</textarea>
                        </div>
                    </div>

                    <div id="campos_encomienda" class="hidden grid-cols-1 gap-4 pt-2">
                        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <p class="text-sm font-bold text-amber-900 mb-1">📦 Modalidad Encomienda</p>
                            <p class="text-xs text-amber-800 mb-3">El paquete será despachado a través de empresas de transporte. Deberás recogerlo personalmente en la Terminal de Buses o Agencia correspondiente a tu ciudad.</p>

                            <label class="block text-xs font-bold text-amber-900 uppercase mb-2">¿Cómo deseas pagar el transporte? *</label>
                            <select id="pago_envio" name="pago_envio" onchange="alternarLogistica()" class="w-full rounded border-amber-300 bg-white p-2 text-sm focus:ring-amber-500">
                                <option value="destino" {{ old('pago_envio') == 'destino' ? 'selected' : '' }}>Pago en Destino (Pagarás a la empresa al recoger tu caja)</option>
                                <option value="pagado" {{ old('pago_envio') == 'pagado' ? 'selected' : '' }}>Pagar ahora (Añadir Bs 25.00 estimados a tu transferencia QR)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Ciudad / Municipio de Destino *</label>
                            <input type="text" name="ciudad_encomienda" id="ciudad_encomienda" value="{{ old('ciudad_encomienda') }}" placeholder="Ej: Betanzos, Tupiza, Oruro..." class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border p-6 shadow-sm space-y-4">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2">2. Forma de Pago</h3>

                    <div class="grid grid-cols-2 gap-4">
                        @foreach($tipoPagos as $tp)
                            <label class="border rounded-xl p-4 flex items-center space-x-3 cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                <input type="radio" name="tipo_pago_id" value="{{ $tp->id }}" data-nombre="{{ $tp->nombre }}" onchange="alternarPasarela('{{ $tp->nombre }}')" required class="text-blue-600 focus:ring-blue-500" {{ old('tipo_pago_id') == $tp->id ? 'checked' : '' }}>
                                <div>
                                    <span class="block font-bold text-sm text-gray-800">{{ $tp->nombre }}</span>
                                    <span class="block text-xs text-gray-400">{{ $tp->descripcion }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div id="pasarela_qr" class="hidden bg-blue-50/50 rounded-xl p-5 border border-blue-100 text-center space-y-4">
                        <p class="text-sm font-semibold text-blue-900">Escanea el código QR oficial por el monto exacto (Total a Pagar):</p>
                        <div class="w-48 h-48 mx-auto bg-white p-2 rounded-lg border shadow-sm flex items-center justify-center">
                            <img src="{{ asset('qr/Qr.png') }}" alt="QR Interbancario" class="max-w-full max-h-full">
                        </div>
                        <div class="max-w-md mx-auto text-left bg-white p-4 rounded-lg border">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Sube tu captura o comprobante de depósito *</label>
                            <input type="file" name="comprobante" id="comprobante" accept="image/*" class="mt-1 block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white file:cursor-pointer hover:file:bg-blue-700">
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-xl border p-6 shadow-sm sticky top-6">
                    <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">Resumen de Orden</h3>

                    <div class="divide-y max-h-48 overflow-y-auto mb-4 pr-1">
                        @foreach($cartItems as $item)
                            <div class="py-2.5 flex justify-between text-sm">
                                <div>
                                    <p class="font-bold text-gray-800 line-clamp-1">{{ $item['nombre'] }}</p>
                                    <span class="text-xs text-gray-400">Cant: {{ $item['cantidad'] }} @if($item['talla'])| Talla: {{ $item['talla'] }}@endif</span>
                                </div>
                                <span class="font-bold text-gray-700">Bs {{ number_format($item['precio'] * $item['cantidad'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $subtotalArticulos = 0;
                        foreach($cartItems as $i) $subtotalArticulos += $i['precio'] * $i['cantidad'];
                    @endphp

                    <div class="mb-4">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">¿Tienes un código de descuento?</label>
                        <div class="flex space-x-2">
                            <input type="text" id="cupon_input" placeholder="Ej: ESPORTS10" class="w-full uppercase rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500">
                            <button type="button" onclick="aplicarCuponAJAX()" class="bg-gray-900 hover:bg-black text-white font-bold px-4 rounded-lg text-xs transition">Aplicar</button>
                        </div>
                        <p id="cupon_mensaje" class="text-xs font-bold mt-1 hidden"></p>
                    </div>

                    <div class="border-t pt-4 space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal Artículos:</span>
                            <span class="font-semibold" id="subtotal_base_display">Bs {{ number_format($subtotalArticulos, 2) }}</span>
                        </div>

                        <div id="fila_descuento" class="flex justify-between text-red-500 font-bold hidden">
                            <span>Descuento aplicado:</span>
                            <span id="descuento_display">- Bs 0.00</span>
                        </div>

                        <div class="flex justify-between text-gray-600">
                            <span>Logística / Encomienda:</span>
                            <span class="font-semibold text-blue-600" id="costo_envio_display">Bs 0.00</span>
                        </div>

                        <div class="flex justify-between font-black text-lg text-gray-900 border-t pt-3">
                            <span>Total a depositar:</span>
                            <span class="text-green-600" id="total_final_display">Bs {{ number_format($subtotalArticulos, 2) }}</span>
                        </div>
                    </div>

                    <input type="hidden" id="cupon_id" name="cupon_id" value="">
                    <input type="hidden" id="descuento_oculto" name="descuento_aplicado" value="0">

                    <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl shadow-md transition">
                        Confirmar y Enviar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

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

            mensaje.classList.remove('hidden', 'text-red-500', 'text-green-600');
            mensaje.innerText = data.mensaje;

            if (data.valido) {
                mensaje.classList.add('text-green-600');
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
                mensaje.classList.add('text-red-500');
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