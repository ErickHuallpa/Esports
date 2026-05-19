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
                        <select id="metodo_entrega" name="metodo_entrega" onchange="alternarLogistica()" required class="w-full rounded-lg border-gray-300 border p-2.5 text-sm bg-white">
                            <option value="tienda" {{ old('metodo_entrega') == 'tienda' ? 'selected' : '' }}>Recoger en Tienda Física (Gratis)</option>
                            <option value="delivery" {{ old('metodo_entrega') == 'delivery' ? 'selected' : '' }}>Servicio de Delivery Local (Potosí)</option>
                            <option value="envio" {{ old('metodo_entrega') == 'envio' ? 'selected' : '' }}>Envio a otros Municipios (Betanzos, Tupiza, etc.)</option>
                        </select>
                    </div>

                    <div id="campos_envio" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Ciudad / Municipio de Destino *</label>
                            <input type="text" name="ciudad_destino" id="ciudad_destino" value="{{ old('ciudad_destino') }}" placeholder="Ej: Tupiza" class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase">Zona / Barrio</label>
                            <input type="text" name="zona_destino" value="{{ old('zona_destino') }}" placeholder="Ej: San Gerardo" class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase">Dirección de Entrega Exacta *</label>
                            <textarea name="direccion_envio" id="direccion_envio" rows="2" placeholder="Ej: Calle Chayanta Nro 45..." class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm">{{ old('direccion_envio') }}</textarea>
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
                        <p class="text-sm font-semibold text-blue-900">Escanea el código QR oficial para procesar tu transferencia:</p>
                        <div class="w-48 h-48 mx-auto bg-white p-2 rounded-lg border shadow-sm flex items-center justify-center">
                            <img src="{{ asset('qr/qr.jpg') }}" alt="QR Interbancario" class="max-w-full max-h-full">
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
                    
                    <div class="divide-y max-h-60 overflow-y-auto mb-4 pr-1">
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
                        $subtotal = 0;
                        foreach($cartItems as $i) $subtotal += $i['precio'] * $i['cantidad'];
                    @endphp

                    <div class="border-t pt-4 space-y-2 text-sm">
                        <div class="flex justify-between font-black text-lg text-gray-900 border-t pt-2">
                            <span>Total a pagar:</span>
                            <span>Bs {{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow-md transition">
                        Confirmar y Enviar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function alternarLogistica() {
        const metodo = document.getElementById('metodo_entrega').value;
        const panel = document.getElementById('campos_envio');
        
        if(metodo === 'tienda') {
            panel.classList.add('hidden');
        } else {
            panel.classList.remove('hidden');
        }
    }

    function alternarPasarela(nombreMetodo) {
        const pasarela = document.getElementById('pasarela_qr');
        if(nombreMetodo === 'QR') {
            pasarela.classList.remove('hidden');
        } else {
            pasarela.classList.add('hidden');
        }
    }

    // Se dispara cuando la página recarga para recordar el estado de los componentes
    document.addEventListener("DOMContentLoaded", function() {
        alternarLogistica();
        
        const checkedRadio = document.querySelector('input[name="tipo_pago_id"]:checked');
        if(checkedRadio) {
            alternarPasarela(checkedRadio.dataset.nombre);
        }
    });
</script>
@endsection