@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Validación de Transacciones QR Pendientes</h2>
    <p class="text-gray-500 text-sm">Corrobora las transferencias bancarias de tus clientes para autorizar los pedidos.</p>
</div>

<div class="bg-white rounded-xl border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-sm">
            <thead class="bg-gray-50 border-b text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="p-4">ID Orden</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Monto Solicitado</th>
                    <th class="p-4">Comprobante QR</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @forelse($pagos as $pago)
                    <tr class="hover:bg-gray-50/80 transition">
                        <td class="p-4 font-bold text-gray-900">#{{ $pago->venta->id ?? $pago->id }}</td>
                        <td class="p-4">
                            <p class="font-bold text-gray-800">{{ $pago->user->persona->nombre }} {{ $pago->user->persona->apellidos }}</p>
                            <span class="text-xs text-gray-400">{{ $pago->user->email }}</span>
                        </td>
                        <td class="p-4 font-black text-gray-900">Bs {{ number_format($pago->monto, 2) }}</td>
                        <td class="p-4">
                            @if($pago->comprobante_url)
                                <a href="{{ asset('storage/' . $pago->comprobante_url) }}" target="_blank" class="text-blue-600 hover:underline flex items-center font-semibold text-xs">
                                    👁️ Ver Comprobante Full
                                </a>
                            @else
                                <span class="text-red-500 text-xs">Sin captura subida</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="abrirCajaModal({{ $pago->id }}, {{ $pago->monto }}, '{{ $pago->user->persona->nombre }}')" class="bg-gray-900 hover:bg-black text-white text-xs font-bold py-1.5 px-4 rounded-lg shadow-sm transition">
                                Evaluar Solicitud
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">No existen comprobantes QR pendientes de validación en este momento.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="cajaModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="text-md font-bold text-gray-800">Dictamen de Transmisión Bancaria</h3>
            <button onclick="cerrarCajaModal()" class="text-gray-400 text-2xl font-bold focus:outline-none">&times;</button>
        </div>
        <form id="cajaForm" method="POST" action="">
            @csrf
            <div class="p-6 space-y-4">
                <p class="text-xs text-gray-500">Asegúrate de comprobar en el extracto de tu app bancaria que el monto de <strong class="text-gray-900 font-bold" id="montoModal"></strong> abonado por <span id="clienteModal" class="font-semibold text-gray-800"></span> sea real.</p>
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Dictamen Final *</label>
                    <select name="accion" id="accion" onchange="alternarRechazo()" required class="w-full rounded-lg border-gray-300 border p-2 text-sm bg-white">
                        <option value="aprobar">Aprobar Pago y Liberar Orden</option>
                        <option value="rechazar">Rechazar Comprobante (Falso / Inválido)</option>
                    </select>
                </div>

                <div id="campo_rechazo" class="hidden">
                    <label class="block text-xs font-bold text-red-700 uppercase">Motivo de Rechazo (Se notificará al cliente) *</label>
                    <textarea name="motivo_rechazo" id="motivo_rechazo" rows="2" placeholder="Ej: El número de operación no figura en el extracto." class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase">Observaciones Internas</label>
                    <textarea name="observaciones" rows="2" placeholder="Ej: Depósito verificado mediante Banco Unión." class="mt-1 block w-full rounded-lg border-gray-300 border p-2 text-sm"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-2">
                <button type="button" onclick="cerrarCajaModal()" class="px-4 py-2 bg-gray-200 rounded font-medium text-xs text-gray-800">Cerrar</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded text-xs transition">Aplicar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirCajaModal(id, monto, cliente) {
        document.getElementById('cajaForm').action = `/gestion/pagos/${id}/verificar`;
        document.getElementById('montoModal').innerText = "Bs " + monto.toFixed(2);
        document.getElementById('clienteModal').innerText = cliente;
        document.getElementById('cajaModal').classList.remove('hidden');
    }

    function cerrarCajaModal() {
        document.getElementById('cajaModal').classList.add('hidden');
    }

    function alternarRechazo() {
        const accion = document.getElementById('accion').value;
        const campo = document.getElementById('campo_rechazo');
        const input = document.getElementById('motivo_rechazo');

        if(accion === 'rechazar') {
            campo.classList.remove('hidden');
            input.required = true;
        } else {
            campo.classList.add('hidden');
            input.required = false;
        }
    }
</script>
@endsection