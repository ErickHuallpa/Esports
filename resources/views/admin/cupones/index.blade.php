@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4 max-w-7xl mx-auto">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Gestión de Cupones</h1>
        <p class="text-gray-500 text-sm mt-1">Administra los códigos promocionales de uso único para el checkout.</p>
    </div>

    <div class="flex flex-col items-end space-y-3">
        <button onclick="abrirCrearModal()"
            class="inline-flex justify-center items-center bg-[#0464a4] hover:bg-[#343c4c] text-white text-sm font-black uppercase tracking-wider py-3 px-6 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
            ➕ Nuevo Cupón
        </button>
        <div class="flex gap-2">
            <a href="?estado=disponibles" class="px-4 py-2 text-xs font-bold rounded-xl uppercase tracking-wider {{ $estado === 'disponibles' ? 'bg-[#0464a4] text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Disponibles</a>
            <a href="?estado=usados" class="px-4 py-2 text-xs font-bold rounded-xl uppercase tracking-wider {{ $estado === 'usados' ? 'bg-[#0464a4] text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Usados</a>
            <a href="?estado=todos" class="px-4 py-2 text-xs font-bold rounded-xl uppercase tracking-wider {{ $estado === 'todos' ? 'bg-[#0464a4] text-white shadow-md' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">Todos</a>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-xl max-w-7xl mx-auto overflow-hidden border border-[#343c4c]/10 z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-xs font-black tracking-widest">
                <tr>
                    <th class="p-4">Código Promocional</th>
                    <th class="p-4 text-center">Descuento</th>
                    <th class="p-4 text-center">Compra Mínima</th>
                    <th class="p-4 text-center">Estado de Uso</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                @forelse($cupones as $cupon)
                    <tr class="hover:bg-[#f4f4f4]/50 transition-colors">
                        <td class="p-4 font-black text-lg text-[#343c4c] tracking-wide uppercase">{{ $cupon->codigo }}</td>
                        <td class="p-4 text-center font-black text-xl text-[#0464a4]">- Bs {{ number_format($cupon->valor, 0) }}</td>
                        <td class="p-4 text-center text-sm font-bold text-gray-500">
                            {{ $cupon->monto_minimo > 0 ? 'Mín. Bs ' . number_format($cupon->monto_minimo, 0) : 'Sin mínimo' }}
                        </td>
                        <td class="p-4 text-center">
                            @if(!$cupon->activo)
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black rounded-md uppercase tracking-wider border border-gray-200">
                                    Inactivo
                                </span>
                            @elseif($cupon->usado)
                                <span class="inline-block px-3 py-1 bg-[#dc043c]/10 text-[#dc043c] text-[10px] font-black rounded-md uppercase tracking-wider border border-[#dc043c]/20">
                                    Usado
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black rounded-md uppercase tracking-wider border border-green-200">
                                    Disponible
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" onclick="abrirEditarModal({{ $cupon->toJson() }})"
                                    class="text-[#0464a4] bg-[#0464a4]/10 hover:bg-[#0464a4] hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                                    Editar
                                </button>
                                @if($cupon->usado)
                                <button type="button"
                                        onclick="abrirConfirmModal('{{ route('admin.cupones.reactivar', $cupon->id) }}', 'PATCH', '¿Restaurar Cupón?', 'Este cupón volverá a estar disponible para que cualquier cliente pueda usarlo.', 'Sí, Restaurar', 'bg-green-600 hover:bg-green-700', '♻️')"
                                        class="text-green-700 bg-green-50 hover:bg-green-600 hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                                        Restaurar
                                    </button>
                                @endif
                                <button type="button"
                                    onclick="abrirConfirmModal('{{ route('admin.cupones.destroy', $cupon->id) }}', 'DELETE', '¿Eliminar Cupón?', 'Esta acción es permanente y el cupón ya no aparecerá en el sistema.', 'Sí, Eliminar', 'bg-[#dc043c] hover:bg-[#a8002a]', '🗑️')"
                                    class="text-[#dc043c] bg-[#dc043c]/10 hover:bg-[#dc043c] hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-gray-400 bg-white">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m11 3v5a2 2 0 01-2 2H7a2 2 0 01-2-2v-5m14 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 11H4"></path>
                            </svg>
                            <span class="block font-bold text-base text-[#343c4c]/60">No existen cupones registrados actualmente.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="crearModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border border-[#343c4c]/10 transform transition-all">
        <div class="px-6 py-4 bg-[#343c4c] text-white border-b-4 border-[#dcb47c] flex justify-between items-center">
            <h3 id="modalTitle" class="font-black uppercase tracking-wider text-sm flex items-center">
                🎟️ Registrar Nuevo Cupón
            </h3>
            <button type="button" onclick="document.getElementById('crearModal').classList.add('hidden')" class="text-white hover:text-[#dc043c] text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form action="{{ route('admin.cupones.store') }}" method="POST" id="cuponForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Código del Cupón *</label>
                    <input type="text" id="codigo" name="codigo" placeholder="Ej: MILAN2026" maxlength="10"
                        class="val-input w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] uppercase tracking-wider" required>
                    <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_codigo"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Monto Descuento *</label>
                        <input type="number" id="valor" name="valor" step="5" min="5" max="250" placeholder="Ej: 15"
                            class="val-input w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-black text-[#343c4c]" required>
                        <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_valor"></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Compra Mínima *</label>
                        <input type="number" id="monto_minimo" name="monto_minimo" step="1" min="0" placeholder="Ej: 100 (0 = Sin mín)"
                            class="val-input w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]" required>
                        <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_minimo"></p>
                    </div>
                </div>

                <div id="toggleActivoContainer" class="hidden">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" id="activo" name="activo" value="1" class="form-checkbox h-5 w-5 text-[#0464a4] rounded focus:ring-[#0464a4]">
                        <span class="text-sm font-bold text-[#343c4c]">Cupón Activo</span>
                    </label>
                </div>

                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-900 flex items-start space-x-2">
                    <span class="text-sm">⚠️</span>
                    <p class="text-[11px] font-medium leading-tight">
                        <strong>Restricción de Seguridad:</strong> Este cupón se registrará bajo la modalidad de <strong>Uso Único</strong>. Una vez que un cliente lo valide y concrete su orden, el código se inhabilitará automáticamente en el sistema.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('crearModal').classList.add('hidden')" 
                    class="px-4 py-2.5 bg-gray-200 text-[#343c4c] rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="btnSubmit" class="px-5 py-2.5 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-wider rounded-xl shadow-md transition-colors text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                    Guardar Cupón
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Confirmación Elegante -->
<div id="confirmModal" class="fixed inset-0 z-[60] hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden border border-[#343c4c]/10 transform transition-all scale-95 opacity-0" id="confirmModalContent">
        <div class="p-6 text-center">
            <div id="confirmIcon" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 text-gray-600 mb-4 text-3xl">
                ⚠️
            </div>
            <h3 id="confirmTitle" class="text-xl font-black text-[#343c4c] mb-2">¿Confirmar?</h3>
            <p id="confirmMessage" class="text-sm text-gray-500 font-medium mb-6">Mensaje de confirmación</p>
            <div class="flex justify-center space-x-3">
                <button type="button" onclick="cerrarConfirmModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-bold text-sm transition-colors uppercase tracking-wider">
                    Cancelar
                </button>
                <form id="confirmForm" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="_method" id="confirmMethod" value="PATCH">
                    <button type="submit" id="confirmBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-black text-sm uppercase tracking-wider transition-colors shadow-md">
                        Aceptar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function valCodigo(v) {
        if(!v.trim()) return "El código es obligatorio.";
        if(v.trim().length > 10) return "Máximo 10 caracteres.";
        return null;
    }

    function valValor(v) {
        if(!v) return "Obligatorio.";
        const num = parseInt(v);
        if(isNaN(num) || num < 5) return "Mínimo 5 Bs.";
        if(num > 250) return "Máximo 250 Bs.";
        if(num % 5 !== 0) return "Debe ser de 5 en 5.";
        return null;
    }

    function valMinimo(v, valor) {
        if(!v) return "Obligatorio (use 0).";
        const num = parseInt(v);
        const val = parseInt(valor);
        if(isNaN(num) || num < 0) return "No puede ser negativo.";
        if(!isNaN(val) && num > 0 && num <= val) return "Mínimo debe ser > al desc.";
        return null;
    }

    function checkForm() {
        const codInput = document.getElementById('codigo');
        const valInput = document.getElementById('valor');
        const minInput = document.getElementById('monto_minimo');
        
        const cErr = valCodigo(codInput.value);
        const vErr = valValor(valInput.value);
        const mErr = valMinimo(minInput.value, valInput.value);

        setFieldError(codInput, document.getElementById('err_codigo'), cErr);
        setFieldError(valInput, document.getElementById('err_valor'), vErr);
        setFieldError(minInput, document.getElementById('err_minimo'), mErr);

        const btn = document.getElementById('btnSubmit');
        if(btn) btn.disabled = (cErr !== null || vErr !== null || mErr !== null);
    }

    function setFieldError(inputEl, errEl, msg) {
        if(!inputEl || !errEl) return;
        if(msg) {
            errEl.innerText = msg;
            errEl.classList.remove('hidden');
            inputEl.classList.add('border-red-400', 'bg-red-50');
            inputEl.classList.remove('border-green-400', 'bg-green-50', 'border-transparent');
        } else {
            errEl.classList.add('hidden');
            if(inputEl.value && inputEl.value.trim().length > 0) {
                inputEl.classList.remove('border-red-400', 'bg-red-50', 'border-transparent');
                inputEl.classList.add('border-green-400', 'bg-green-50');
            } else {
                inputEl.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
                inputEl.classList.add('border-transparent');
            }
        }
    }

    document.querySelectorAll('.val-input').forEach(el => {
        el.addEventListener('input', checkForm);
        el.addEventListener('change', checkForm);
    });

    window.abrirCrearModal = function() {
        const form = document.getElementById('cuponForm');
        if(form) {
            form.reset();
            form.action = "{{ route('admin.cupones.store') }}";
            document.getElementById('formMethod').value = 'POST';
        }
        
        document.getElementById('modalTitle').innerText = '🎟️ Registrar Nuevo Cupón';
        document.getElementById('btnSubmit').innerText = 'Guardar Cupón';
        document.getElementById('toggleActivoContainer').classList.add('hidden');
        
        document.querySelectorAll('.val-input').forEach(el => {
            el.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            el.classList.add('border-transparent');
        });
        document.querySelectorAll('[id^="err_"]').forEach(el => el.classList.add('hidden'));
        
        const btn = document.getElementById('btnSubmit');
        if(btn) btn.disabled = true;

        document.getElementById('crearModal').classList.remove('hidden');
    };

    window.abrirEditarModal = function(cupon) {
        const form = document.getElementById('cuponForm');
        if(form) {
            form.action = `/cupones/${cupon.id}`;
            document.getElementById('formMethod').value = 'PUT';
        }

        document.getElementById('modalTitle').innerText = '✏️ Editar Cupón';
        document.getElementById('btnSubmit').innerText = 'Guardar Cambios';
        document.getElementById('toggleActivoContainer').classList.remove('hidden');

        document.getElementById('codigo').value = cupon.codigo;
        document.getElementById('valor').value = parseInt(cupon.valor);
        document.getElementById('monto_minimo').value = parseInt(cupon.monto_minimo);
        document.getElementById('activo').checked = cupon.activo;

        document.querySelectorAll('.val-input').forEach(el => {
            el.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            el.classList.add('border-transparent');
        });
        document.querySelectorAll('[id^="err_"]').forEach(el => el.classList.add('hidden'));
        
        checkForm();

        document.getElementById('crearModal').classList.remove('hidden');
    };

    window.abrirConfirmModal = function(actionUrl, method, title, message, btnText, btnColorClass, icon) {
        const modal = document.getElementById('confirmModal');
        const content = document.getElementById('confirmModalContent');
        
        document.getElementById('confirmForm').action = actionUrl;
        document.getElementById('confirmMethod').value = method;
        document.getElementById('confirmTitle').innerText = title;
        document.getElementById('confirmMessage').innerText = message;
        
        const btn = document.getElementById('confirmBtn');
        btn.innerText = btnText;
        btn.className = `px-4 py-2 text-white rounded-xl font-black text-sm uppercase tracking-wider transition-colors shadow-md ${btnColorClass}`;
        
        document.getElementById('confirmIcon').innerHTML = icon;

        modal.classList.remove('hidden');
        // Animar entrada
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    };

    window.cerrarConfirmModal = function() {
        const modal = document.getElementById('confirmModal');
        const content = document.getElementById('confirmModalContent');
        
        // Animar salida
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    };
</script>
@endsection