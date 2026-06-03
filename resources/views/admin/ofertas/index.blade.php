@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4 max-w-7xl mx-auto">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Promociones y Descuentos</h1>
        <p class="text-gray-500 text-sm mt-1">Configura rebajas temporales para los productos del catálogo.</p>
    </div>
    <button onclick="abrirCrearModal()" 
        class="inline-flex justify-center items-center bg-[#0464a4] hover:bg-[#343c4c] text-white text-sm font-black uppercase tracking-wider py-3 px-6 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
        🎯 Lanzar Nueva Oferta
    </button>
</div>

<div class="bg-white rounded-2xl shadow-xl max-w-7xl mx-auto overflow-hidden border border-[#343c4c]/10 z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-xs font-black tracking-widest">
                <tr>
                    <th class="p-4">Producto en Promoción</th>
                    <th class="p-4 text-center">Descuento (%)</th>
                    <th class="p-4 text-center">Vigencia (Inicio - Fin)</th>
                    <th class="p-4 text-center">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                @forelse($ofertas as $of)
                    @php 
                        $activa = now()->between($of->fecha_inicio, $of->fecha_fin);
                    @endphp
                    <tr class="hover:bg-[#f4f4f4]/50 transition-colors">
                        <td class="p-4 font-black text-base text-[#343c4c] tracking-wide uppercase line-clamp-2" title="{{ $of->producto->nombre }}">
                            {{ $of->producto->nombre }}
                        </td>
                        <td class="p-4 text-center font-black text-xl text-[#dc043c]">
                            -{{ intval($of->porcentaje_descuento) }}%
                        </td>
                        <td class="p-4 text-center text-xs font-bold text-[#343c4c]/70 uppercase tracking-wider">
                            {{ \Carbon\Carbon::parse($of->fecha_inicio)->format('d/m/Y') }} <br>
                            <span class="text-[#dcb47c]">AL</span> <br>
                            {{ \Carbon\Carbon::parse($of->fecha_fin)->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-center">
                            @if($activa)
                                <span class="inline-block px-3 py-1 bg-green-50 text-green-700 text-[10px] font-black rounded-md uppercase tracking-wider border border-green-200">
                                    Activa Ahora
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black rounded-md uppercase tracking-wider border border-gray-200">
                                    Expirada / Pendiente
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" onclick="abrirEditarModal({{ $of->toJson() }})"
                                    class="text-[#0464a4] bg-[#0464a4]/10 hover:bg-[#0464a4] hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                                    Editar
                                </button>
                                <form action="{{ route('admin.ofertas.destroy', $of->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de finalizar esta promoción inmediatamente? El producto volverá a su precio original en el catálogo.')" 
                                        class="text-[#dc043c] bg-[#dc043c]/10 hover:bg-[#dc043c] hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                                        Cancelar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-gray-400 bg-white">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm0 0h4m-4 0H8m11 3v5a2 2 0 01-2 2H7a2 2 0 01-2-2v-5m14 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 11H4"></path>
                            </svg>
                            <span class="block font-bold text-base text-[#343c4c]/60">No existen campañas de descuento registradas actualmente.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="crearModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-[#343c4c]/10 transform transition-all flex flex-col max-h-[95vh]">
        <div class="px-6 py-4 bg-[#343c4c] text-white border-b-4 border-[#dcb47c] flex justify-between items-center shrink-0">
            <h3 id="modalTitle" class="font-black uppercase tracking-wider text-sm flex items-center">
                🎯 Registrar Promoción
            </h3>
            <button type="button" onclick="cerrarCrearModal()" class="text-white hover:text-[#dc043c] text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form action="{{ route('admin.ofertas.store') }}" method="POST" id="ofertaForm" class="flex flex-col overflow-hidden">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <div class="p-6 space-y-5 overflow-y-auto">
                <div class="relative">
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Producto Objetivo *</label>
                    <input type="text" id="search_prod" placeholder="🔍 Escribe para buscar (ej: teclado, marca...)" class="w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4]">
                    <div id="dropdown_container" class="absolute z-10 w-full mt-1 hidden bg-white shadow-xl rounded-xl border border-gray-200">
                        <select name="producto_id" id="producto_id" required size="4" class="val-input w-full border-none rounded-xl p-2 text-sm focus:ring-0 font-bold text-[#343c4c] cursor-pointer outline-none">
                            <option value="" class="hidden">Seleccione el producto...</option>
                            @foreach($productosDisponibles as $prod)
                                <option value="{{ $prod->id }}" class="p-2 hover:bg-[#0464a4] hover:text-white rounded">{{ $prod->nombre }} (Bs {{ $prod->precio_venta }})</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_producto"></p>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Porcentaje de Descuento (%) *</label>
                    <div class="relative">
                        <input type="number" name="porcentaje_descuento" id="porcentaje" min="5" max="80" step="5" placeholder="Ej: 20 (Mín: 5%, Máx: 80%, de 5 en 5)" required
                            class="val-input w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 pl-10 text-sm focus:ring-2 focus:ring-[#0464a4] font-black text-[#dc043c]">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-[#dc043c] font-black">%</span>
                    </div>
                    <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_porcentaje"></p>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Fecha de Inicio *</label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" required min="{{ date('Y-m-d') }}"
                            class="val-input w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                        <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_inicio"></p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Fecha de Cierre *</label>
                        <input type="date" name="fecha_fin" id="fecha_fin" required min="{{ date('Y-m-d') }}"
                            class="val-input w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                        <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_fin"></p>
                    </div>
                </div>

                <!-- Atajos de Fechas -->
                <div class="flex items-center justify-end space-x-2">
                    <span class="text-[10px] font-black text-[#343c4c] uppercase">Atajos:</span>
                    <button type="button" onclick="setAtajoFecha(7)" class="px-2 py-1 bg-gray-200 hover:bg-[#dcb47c] hover:text-white text-xs font-bold rounded-lg transition-colors">1 Sem</button>
                    <button type="button" onclick="setAtajoFecha(15)" class="px-2 py-1 bg-gray-200 hover:bg-[#dcb47c] hover:text-white text-xs font-bold rounded-lg transition-colors">15 Días</button>
                    <button type="button" onclick="setAtajoFecha(30)" class="px-2 py-1 bg-gray-200 hover:bg-[#dcb47c] hover:text-white text-xs font-bold rounded-lg transition-colors">1 Mes</button>
                    <button type="button" onclick="setAtajoFecha(90)" class="px-2 py-1 bg-gray-200 hover:bg-[#dcb47c] hover:text-white text-xs font-bold rounded-lg transition-colors">3 Meses</button>
                </div>

                <div class="p-3 bg-[#0464a4]/10 border border-[#0464a4]/20 rounded-xl text-[#0464a4] flex items-start space-x-2">
                    <span class="text-sm">ℹ️</span>
                    <p class="text-[11px] font-medium leading-tight text-[#343c4c]">
                        <strong>Aviso Automático:</strong> Esta oferta alterará el precio final del catálogo de forma inmediata y se aplicará también dentro del carrito de compras de los usuarios durante las fechas especificadas.
                    </p>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 flex justify-end space-x-2 shrink-0">
                <button type="button" onclick="cerrarCrearModal()" 
                    class="px-4 py-2.5 bg-gray-200 text-[#343c4c] rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-gray-300 transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="btnSubmit" class="px-5 py-2.5 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-wider rounded-xl shadow-md transition-colors text-xs disabled:opacity-50 disabled:cursor-not-allowed">
                    Activar Oferta
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Interacción del Buscador
    const searchInput = document.getElementById('search_prod');
    const dropdown = document.getElementById('dropdown_container');
    const select = document.getElementById('producto_id');

    searchInput.addEventListener('focus', function() {
        dropdown.classList.remove('hidden');
    });

    searchInput.addEventListener('input', function(e) {
        dropdown.classList.remove('hidden');
        const query = e.target.value.toLowerCase();
        const options = select.options;
        for(let i = 0; i < options.length; i++) {
            if(options[i].value === "") continue; // skip placeholder
            const text = options[i].text.toLowerCase();
            if(text.includes(query)) {
                options[i].style.display = '';
            } else {
                options[i].style.display = 'none';
            }
        }
    });

    select.addEventListener('change', function() {
        if(this.value) {
            const selectedText = this.options[this.selectedIndex].text;
            searchInput.value = selectedText;
            dropdown.classList.add('hidden');
            checkForm();
        }
    });

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });

    // Atajos de Fechas
    function setAtajoFecha(dias) {
        let start = document.getElementById('fecha_inicio').value;
        if(!start) {
            start = new Date().toISOString().split('T')[0];
            document.getElementById('fecha_inicio').value = start;
        }
        const endDate = new Date(start);
        endDate.setDate(endDate.getDate() + dias);
        document.getElementById('fecha_fin').value = endDate.toISOString().split('T')[0];
        checkForm();
    }

    // Validaciones
    function valProducto(v) {
        if(!v) return "Debe seleccionar un producto.";
        return null;
    }

    function valPorcentaje(v) {
        if(!v) return "El porcentaje es obligatorio.";
        const num = parseInt(v);
        if(isNaN(num)) return "Debe ser un número.";
        if(num < 5) return "El descuento mínimo es 5%.";
        if(num > 80) return "El descuento máximo es 80%.";
        if(num % 5 !== 0) return "Debe ser múltiplo de 5 (5, 10, 15...).";
        return null;
    }

    function valFechaInicio(v) {
        if(!v) return "La fecha de inicio es obligatoria.";
        const today = new Date().toISOString().split('T')[0];
        if(v < today) return "No puede ser anterior a hoy.";
        return null;
    }

    function valFechaFin(inicio, fin) {
        if(!fin) return "La fecha de cierre es obligatoria.";
        if(inicio && fin <= inicio) return "Debe ser posterior al inicio.";
        return null;
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

    function checkForm() {
        const prodInput = document.getElementById('producto_id');
        const percInput = document.getElementById('porcentaje');
        const startInput = document.getElementById('fecha_inicio');
        const endInput = document.getElementById('fecha_fin');

        const pErr = valProducto(prodInput.value);
        const percErr = valPorcentaje(percInput.value);
        const startErr = valFechaInicio(startInput.value);
        const endErr = valFechaFin(startInput.value, endInput.value);

        setFieldError(document.getElementById('search_prod'), document.getElementById('err_producto'), pErr);
        setFieldError(percInput, document.getElementById('err_porcentaje'), percErr);
        setFieldError(startInput, document.getElementById('err_inicio'), startErr);
        setFieldError(endInput, document.getElementById('err_fin'), endErr);

        const btn = document.getElementById('btnSubmit');
        if(btn) btn.disabled = (pErr !== null || percErr !== null || startErr !== null || endErr !== null);
    }

    document.querySelectorAll('.val-input').forEach(el => {
        el.addEventListener('input', checkForm);
        el.addEventListener('change', checkForm);
    });
    
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        // Al cambiar inicio, el fin debe recalcularse (por ejemplo setearle un mínimo)
        const finInput = document.getElementById('fecha_fin');
        if(this.value) {
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            finInput.min = nextDay.toISOString().split('T')[0];
        }
        checkForm();
    });

    window.abrirCrearModal = function() {
        const form = document.getElementById('ofertaForm');
        if(form) {
            form.reset();
            form.action = "{{ route('admin.ofertas.store') }}";
            document.getElementById('formMethod').value = 'POST';
        }
        document.getElementById('modalTitle').innerText = '🎯 Registrar Promoción';
        document.getElementById('btnSubmit').innerText = 'Activar Oferta';
        document.getElementById('search_prod').value = '';
        document.getElementById('dropdown_container').classList.add('hidden');
        
        // Mostrar todas las opciones del select
        const options = document.getElementById('producto_id').options;
        for(let i = 0; i < options.length; i++) {
            options[i].style.display = '';
        }

        // Limpiar estados
        document.getElementById('search_prod').classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
        document.getElementById('search_prod').classList.add('border-transparent');

        document.querySelectorAll('.val-input').forEach(el => {
            el.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            el.classList.add('border-transparent');
        });
        document.querySelectorAll('[id^="err_"]').forEach(el => el.classList.add('hidden'));
        
        const btn = document.getElementById('btnSubmit');
        if(btn) btn.disabled = true;

        document.getElementById('crearModal').classList.remove('hidden');
    };

    window.abrirEditarModal = function(oferta) {
        const form = document.getElementById('ofertaForm');
        if(form) {
            form.action = `/ofertas/${oferta.id}`;
            document.getElementById('formMethod').value = 'PUT';
        }
        
        document.getElementById('modalTitle').innerText = '✏️ Editar Promoción';
        document.getElementById('btnSubmit').innerText = 'Guardar Cambios';
        
        // Rellenar datos
        const prodSelect = document.getElementById('producto_id');
        
        // Ensure the option exists (it might not be in $productosDisponibles if it already has an offer,
        // so we need to add it temporarily if not found)
        let optionExists = false;
        for(let i=0; i<prodSelect.options.length; i++){
            if(prodSelect.options[i].value == oferta.producto_id){
                optionExists = true;
                break;
            }
        }
        if(!optionExists && oferta.producto) {
            const opt = document.createElement('option');
            opt.value = oferta.producto_id;
            opt.className = "p-2 hover:bg-[#0464a4] hover:text-white rounded";
            opt.text = `${oferta.producto.nombre} (Bs ${oferta.producto.precio_venta})`;
            prodSelect.add(opt);
        }

        prodSelect.value = oferta.producto_id;
        document.getElementById('search_prod').value = prodSelect.options[prodSelect.selectedIndex].text;
        document.getElementById('porcentaje').value = parseInt(oferta.porcentaje_descuento);
        document.getElementById('fecha_inicio').value = String(oferta.fecha_inicio).substring(0, 10);
        document.getElementById('fecha_fin').value = String(oferta.fecha_fin).substring(0, 10);
        
        document.getElementById('dropdown_container').classList.add('hidden');
        
        // Mostrar todas las opciones del select
        const options = document.getElementById('producto_id').options;
        for(let i = 0; i < options.length; i++) {
            options[i].style.display = '';
        }

        // Limpiar estados y ejecutar validación
        document.getElementById('search_prod').classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
        document.getElementById('search_prod').classList.add('border-transparent');

        document.querySelectorAll('.val-input').forEach(el => {
            el.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            el.classList.add('border-transparent');
        });
        document.querySelectorAll('[id^="err_"]').forEach(el => el.classList.add('hidden'));
        
        checkForm();

        document.getElementById('crearModal').classList.remove('hidden');
    };

    window.cerrarCrearModal = function() {
        document.getElementById('crearModal').classList.add('hidden');
    };
</script>
@endsection