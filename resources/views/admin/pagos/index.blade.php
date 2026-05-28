@extends('layouts.app')

@section('content')
<!-- ENCABEZADO CON BOTÓN DE ACCIÓN -->
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4 max-w-7xl mx-auto">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Validación de Transacciones QR</h1>
        <p class="text-gray-500 text-sm mt-1">Corrobora las transferencias bancarias de tus clientes para autorizar los pedidos.</p>
    </div>
</div>

<!-- TABLA DE CONTROL DE PAGOS -->
<div class="bg-white rounded-2xl shadow-xl max-w-7xl mx-auto overflow-hidden border border-[#343c4c]/10 z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-xs font-black tracking-widest">
                <tr>
                    <th class="p-4">ID Orden</th>
                    <th class="p-4">Cliente</th>
                    <th class="p-4">Monto Solicitado</th>
                    <th class="p-4 text-center">Comprobante QR</th>
                    <th class="p-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                @forelse($pagos as $pago)
                    <tr class="hover:bg-[#f4f4f4]/50 transition-colors">
                        <td class="p-4 font-black text-lg text-[#343c4c] tracking-wide uppercase">
                            #{{ $pago->venta->id ?? $pago->id }}
                        </td>
                        <td class="p-4">
                            <p class="font-bold text-[#343c4c] uppercase">{{ $pago->user->persona->nombre }} {{ $pago->user->persona->apellidos }}</p>
                            <span class="text-[11px] font-semibold text-gray-400">{{ $pago->user->email }}</span>
                        </td>
                        <td class="p-4 font-black text-xl text-[#0464a4]">
                            Bs {{ number_format($pago->monto, 2) }}
                        </td>
                        <td class="p-4 text-center">
                            @if($pago->comprobante_url)
                                <a href="{{ asset('storage/' . $pago->comprobante_url) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-[#f4f4f4] text-[#0464a4] hover:bg-[#0464a4] hover:text-white rounded-lg font-bold text-xs transition-colors border border-[#0464a4]/20 uppercase tracking-wider">
                                    👁️ Ver Comprobante
                                </a>
                            @else
                                <span class="inline-block px-3 py-1 bg-[#dc043c]/10 text-[#dc043c] text-[10px] font-black rounded-md uppercase tracking-wider border border-[#dc043c]/20">
                                    Sin captura subida
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="abrirCajaModal({{ $pago->id }}, {{ $pago->monto }}, '{{ addslashes($pago->user->persona->nombre . ' ' . $pago->user->persona->apellidos) }}')" 
                                class="bg-[#dcb47c] hover:bg-[#343c4c] text-[#343c4c] hover:text-white text-xs font-black uppercase tracking-wider py-2.5 px-5 rounded-xl shadow-md transition-colors">
                                Evaluar Solicitud
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-400 bg-white">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="block font-bold text-base text-[#343c4c]/60">No existen comprobantes QR pendientes de validación en este momento.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL DE EVALUACIÓN TIPO FCB -->
<div id="cajaModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-[#343c4c]/10 transform transition-all">
        <!-- Cabecera Modal -->
        <div class="px-6 py-4 bg-[#343c4c] text-white border-b-4 border-[#dcb47c] flex justify-between items-center">
            <h3 class="font-black uppercase tracking-wider text-sm flex items-center">
                💳 Dictamen de Transacción
            </h3>
            <button onclick="cerrarCajaModal()" class="text-white hover:text-[#dc043c] text-2xl font-bold transition-colors focus:outline-none">&times;</button>
        </div>
        
        <form id="cajaForm" method="POST" action="">
            @csrf
            <div class="p-6 space-y-5">
                <!-- Info Box -->
                <div class="p-4 bg-[#f4f4f4] border border-[#dcb47c]/30 rounded-xl">
                    <p class="text-xs text-[#343c4c]/80 leading-relaxed">
                        Asegúrate de comprobar en el extracto de tu app bancaria que el monto de <strong class="text-[#0464a4] font-black text-sm" id="montoModal"></strong> abonado por <span id="clienteModal" class="font-bold text-[#343c4c] uppercase"></span> sea real antes de aprobar.
                    </p>
                </div>
                
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Dictamen Final *</label>
                    <select name="accion" id="accion" onchange="alternarRechazo()" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer">
                        <option value="aprobar">✅ Aprobar Pago y Liberar Orden</option>
                        <option value="rechazar">❌ Rechazar Comprobante (Falso / Inválido)</option>
                    </select>
                </div>

                <div id="campo_rechazo" class="hidden transition-all">
                    <label class="block text-[10px] font-black text-[#dc043c] uppercase tracking-widest mb-1.5">Motivo de Rechazo *</label>
                    <textarea name="motivo_rechazo" id="motivo_rechazo" rows="2" placeholder="Ej: El número de operación no figura en el extracto. El stock será liberado." 
                        class="w-full bg-[#dc043c]/5 border border-[#dc043c]/20 rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#dc043c] text-[#343c4c]"></textarea>
                    <p class="text-[9px] font-bold text-[#dc043c] mt-1 uppercase tracking-wider">El cliente recibirá esta notificación.</p>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Observaciones Internas</label>
                    <textarea name="observaciones" rows="2" placeholder="Ej: Depósito verificado mediante Banco Unión." 
                        class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c]"></textarea>
                </div>
            </div>

            <!-- Footer del Modal -->
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end space-x-2">
                <button type="button" onclick="cerrarCajaModal()" 
                    class="px-4 py-2.5 bg-gray-200 text-[#343c4c] rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="btnSubmitModal" class="px-5 py-2.5 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-wider rounded-xl shadow-md transition-colors text-xs">
                    Aplicar Dictamen
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function abrirCajaModal(id, monto, cliente) {
        document.getElementById('cajaForm').action = `/gestion/pagos/${id}/verificar`;
        document.getElementById('montoModal').innerText = "Bs " + monto.toFixed(2);
        document.getElementById('clienteModal').innerText = cliente;
        
        // Reset form state
        document.getElementById('accion').value = 'aprobar';
        alternarRechazo();

        document.getElementById('cajaModal').classList.remove('hidden');
    }

    function cerrarCajaModal() {
        document.getElementById('cajaModal').classList.add('hidden');
    }

    function alternarRechazo() {
        const accion = document.getElementById('accion').value;
        const campo = document.getElementById('campo_rechazo');
        const input = document.getElementById('motivo_rechazo');
        const btnSubmit = document.getElementById('btnSubmitModal');

        if(accion === 'rechazar') {
            campo.classList.remove('hidden');
            input.required = true;
            btnSubmit.classList.remove('bg-[#0464a4]');
            btnSubmit.classList.add('bg-[#dc043c]');
        } else {
            campo.classList.add('hidden');
            input.required = false;
            btnSubmit.classList.remove('bg-[#dc043c]');
            btnSubmit.classList.add('bg-[#0464a4]');
        }
    }
</script>
@endsection