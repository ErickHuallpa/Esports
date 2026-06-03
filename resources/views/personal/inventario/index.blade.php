@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto my-6">
    <!-- ENCABEZADO -->
    <div class="mb-8 flex flex-col gap-2 border-b-2 border-[#f4f4f4] pb-5">
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Ingreso a Almacén</h1>
        <p class="text-[#343c4c]/60 text-sm font-medium">Registra la llegada de mercadería (Suministros). El sistema protege el precio de venta actual y calcula el margen de utilidad en tiempo real.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- COLUMNA IZQUIERDA: Selector y Tabla -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- PANEL 1: SELECTOR DE SUMINISTRO -->
            <div class="bg-white rounded-2xl border border-[#343c4c]/10 shadow-xl overflow-hidden">
                <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#dcb47c]">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        1. Selector de Suministro
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="md:col-span-3 relative" id="dropdown_container">
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Buscar Variante de Producto</label>
                            
                            <input type="text" id="buscador_variante" placeholder="Buscar por nombre, marca, talla, color..." autocomplete="off"
                                class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                            
                            <!-- Dropdown list -->
                            <div id="lista_variantes" class="absolute z-50 w-full mt-1 bg-white border border-[#343c4c]/10 rounded-xl shadow-2xl max-h-64 overflow-y-auto hidden">
                                @foreach($variantes as $v)
                                    @if($v->producto->proveedor)
                                        <div class="opcion-variante flex items-center p-3 hover:bg-[#f4f4f4] cursor-pointer border-b border-[#343c4c]/5 transition"
                                            data-id="{{ $v->id }}"
                                            data-nombre="{{ $v->producto->nombre }} ({{ $v->talla }} / {{ $v->color }})"
                                            data-costo="{{ $v->producto->precio_compra }}"
                                            data-venta="{{ $v->producto->precio_venta }}"
                                            data-provid="{{ $v->producto->proveedor_id }}"
                                            data-provnombre="{{ $v->producto->proveedor->nombre_empresa }}"
                                            data-search="{{ strtolower($v->producto->nombre . ' ' . $v->producto->marca . ' ' . $v->talla . ' ' . $v->color) }}">
                                            
                                            <div class="flex-shrink-0 mr-3">
                                                @php $fotos = json_decode($v->producto->imagen_url, true) ?? []; @endphp
                                                @if(count($fotos) > 0)
                                                    <img src="{{ asset('storage/' . $fotos[0]) }}" class="w-10 h-10 object-cover rounded-lg border border-[#343c4c]/10">
                                                @else
                                                    <div class="w-10 h-10 bg-[#f4f4f4] rounded-lg flex items-center justify-center text-[#343c4c]/40">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-black text-[#343c4c] truncate">[{{ $v->producto->marca ?? 'E-Sports' }}] {{ $v->producto->nombre }}</p>
                                                <p class="text-[10px] font-bold text-[#0464a4]">Talla: {{ $v->talla ?? 'N/A' }} | Color: {{ $v->color ?? 'N/A' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] text-[#343c4c]/50 font-bold uppercase">Venta Actual</p>
                                                <p class="text-xs font-black text-[#dc043c]">Bs {{ number_format($v->producto->precio_venta, 2) }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="flex items-center p-3 opacity-50 bg-[#dc043c]/5 border-b border-[#343c4c]/5">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="w-10 h-10 bg-[#dc043c]/10 rounded-lg flex items-center justify-center text-[#dc043c]">⚠️</div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-black text-[#dc043c] truncate">{{ $v->producto->nombre }}</p>
                                                <p class="text-[10px] font-bold text-[#dc043c]">Sin Proveedor Asignado</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <!-- Hidden input to store selected value -->
                            <input type="hidden" id="variante_selector_hidden" value="">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Cantidad que entra</label>
                            <input type="number" id="cantidad_selector" min="1" max="10000" value="1" 
                                class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm text-center focus:ring-2 focus:ring-[#0464a4] font-black text-[#343c4c]">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nuevo Costo (Bs)</label>
                            <input type="number" id="costo_selector" min="0.1" max="100000" step="0.01" placeholder="0.00" 
                                class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm text-right focus:ring-2 focus:ring-[#0464a4] font-black text-[#dc043c]">
                        </div>

                        <div class="flex items-end">
                            <button type="button" onclick="agregarAlLote()" class="w-full bg-[#0464a4] hover:bg-[#343c4c] text-white font-black py-3.5 px-4 rounded-xl shadow-md text-xs uppercase tracking-widest transition-all transform hover:-translate-y-0.5">
                                Añadir al Lote
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL 2: TABLA DE LOTE -->
            <div class="bg-white rounded-2xl border border-[#343c4c]/10 shadow-xl overflow-hidden">
                <form action="{{ route('personal.inventario.store') }}" method="POST" id="inventarioForm">
                    @csrf
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-[10px] font-black tracking-widest">
                                <tr>
                                    <th class="p-4">Artículo / Variante</th>
                                    <th class="p-4">Proveedor</th>
                                    <th class="p-4 text-center">Cantidad</th>
                                    <th class="p-4 text-right">Costo Unit.</th>
                                    <th class="p-4 text-center">Margen Comercial</th>
                                    <th class="p-4 text-center">X</th>
                                </tr>
                            </thead>
                            <tbody id="lote_tbody" class="divide-y divide-[#f4f4f4] text-[#343c4c]">
                                <tr id="empty_row">
                                    <td colspan="6" class="p-12 text-center text-[#343c4c]/40 font-bold bg-[#f4f4f4]/50">
                                        No has añadido productos a esta nota de ingreso todavía.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Resumen y Confirmación -->
        <div class="space-y-8">
            <div class="bg-white rounded-2xl border border-[#343c4c]/10 shadow-xl overflow-hidden sticky top-6">
                <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#0464a4]">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        2. Confirmación
                    </h3>
                </div>
                
                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">Proveedores en el Lote</label>
                        <div id="proveedores_detectados_lista" class="text-xs space-y-2 bg-[#f4f4f4] p-4 rounded-xl border border-[#343c4c]/5 text-[#343c4c]/50 font-bold">
                            Ningún producto añadido.
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-2">Observaciones de Recepción</label>
                        <textarea name="motivo_general" rows="2" placeholder="Ej: Ingreso según Factura de Importación #9234" 
                            class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-medium text-[#343c4c] resize-none"></textarea>
                    </div>

                    <div class="border-t-2 border-[#f4f4f4] pt-6 flex flex-col items-center justify-center space-y-2 bg-white">
                        <span class="text-[10px] font-black text-[#343c4c]/50 uppercase tracking-widest">Inversión Lote Total</span>
                        <span class="text-4xl font-black text-[#0464a4] drop-shadow-sm" id="total_lote_display">Bs 0.00</span>
                    </div>

                    <button type="submit" id="btnGuardarLote" disabled class="w-full mt-4 bg-[#f4f4f4] text-[#343c4c]/40 font-black py-4 rounded-xl shadow-sm transition-all pointer-events-none text-xs uppercase tracking-widest">
                        Procesar e Incrementar
                    </button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SECCIÓN INFERIOR: Auditoría Kárdex -->
    <div class="mt-12">
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-xl font-black text-[#343c4c] uppercase tracking-tight">Últimos Ingresos Registrados</h3>
                <span class="ml-3 px-2 py-0.5 bg-[#dcb47c] text-[#343c4c] text-[10px] font-black rounded uppercase tracking-widest hidden sm:inline-block">Auditoría Kárdex</span>
            </div>
            
            <div class="flex items-center space-x-2 bg-white p-2 rounded-xl border shadow-sm">
                <input type="date" id="kardex_fecha_inicio" class="text-xs border-none bg-[#f4f4f4] rounded p-2 text-[#343c4c] font-bold outline-none focus:ring-0">
                <span class="text-[#343c4c]/40 font-black text-[10px]">A</span>
                <input type="date" id="kardex_fecha_fin" class="text-xs border-none bg-[#f4f4f4] rounded p-2 text-[#343c4c] font-bold outline-none focus:ring-0">
                <button onclick="filtrarKardex()" class="bg-[#343c4c] text-white p-2 rounded hover:bg-[#0464a4] transition-colors"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></button>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-[#343c4c]/10 text-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-[#f4f4f4] border-b-2 border-[#dcb47c] font-black text-[#343c4c] uppercase text-[10px] tracking-widest">
                        <tr>
                            <th class="p-4">Variante / Artículo</th>
                            <th class="p-4 text-center">Cant. Entrada</th>
                            <th class="p-4 text-center">Stock Anterior</th>
                            <th class="p-4 text-center">Stock Resultante</th>
                            <th class="p-4">Operador</th>
                            <th class="p-4">Detalle Histórico</th>
                        </tr>
                    </thead>
                    <tbody id="kardex_tbody" class="divide-y divide-[#f4f4f4] text-[#343c4c] font-medium">
                        @forelse($movimientos as $mov)
                            <tr class="hover:bg-[#f4f4f4]/50 transition-colors kardex-row">
                                <td class="p-4 font-bold text-[#343c4c] uppercase">{{ $mov->variante->producto->nombre }} <span class="text-[10px] text-[#0464a4]">({{ $mov->variante->talla }} / {{ $mov->variante->color }})</span></td>
                                <td class="p-4 text-center font-black text-[#0464a4]">+{{ $mov->cantidad }} un.</td>
                                <td class="p-4 text-center bg-[#f4f4f4]/50 font-bold text-[#343c4c]/60">{{ $mov->stock_anterior }} un.</td>
                                <td class="p-4 text-center font-black text-[#343c4c] bg-[#dcb47c]/10">{{ $mov->stock_resultante }} un.</td>
                                <td class="p-4 font-bold text-xs uppercase">{{ $mov->user->persona->nombre }} {{ $mov->user->persona->apellidos }}</td>
                                <td class="p-4 text-xs text-[#343c4c]/60">
                                    {{ $mov->motivo }}<br>
                                    <span class="text-[10px] text-[#343c4c]/40">{{ $mov->created_at->format('Y-m-d H:i') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr id="kardex_empty"><td colspan="6" class="p-10 text-center text-[#343c4c]/40 font-bold bg-white">No se registran movimientos de entrada recientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="bg-[#f4f4f4]/50 p-4 text-center border-t border-[#343c4c]/5">
                <button id="btnCargarMasKardex" onclick="cargarMasKardex()" class="bg-white border border-[#343c4c]/10 text-[#343c4c] font-bold py-2 px-6 rounded-lg text-xs uppercase tracking-widest hover:bg-[#343c4c] hover:text-white transition-colors shadow-sm">
                    Cargar 25 más
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let loteItems = [];
    let kardexOffset = 25; // Ya cargamos 25 iniciales

    // AUTO-CORRECCIONES DE LÍMITES
    const cantInput = document.getElementById('cantidad_selector');
    cantInput.addEventListener('input', function() {
        if (this.value > 10000) this.value = 10000;
        if (this.value < 1 && this.value !== '') this.value = 1;
    });

    const costoInput = document.getElementById('costo_selector');
    costoInput.addEventListener('input', function() {
        if (this.value > 100000) this.value = 100000;
        if (this.value < 0 && this.value !== '') this.value = 0;
    });

    // CUSTOM DROPDOWN SEARCHABLE
    const buscador = document.getElementById('buscador_variante');
    const lista = document.getElementById('lista_variantes');
    const hiddenId = document.getElementById('variante_selector_hidden');
    let selectedOptionData = null;

    buscador.addEventListener('focus', () => {
        lista.classList.remove('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!document.getElementById('dropdown_container').contains(e.target)) {
            lista.classList.add('hidden');
        }
    });

    buscador.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        const opciones = document.querySelectorAll('.opcion-variante');
        let encontradas = 0;
        
        opciones.forEach(op => {
            if (op.getAttribute('data-search').includes(term)) {
                op.classList.remove('hidden');
                encontradas++;
            } else {
                op.classList.add('hidden');
            }
        });
    });

    const opcionesDiv = document.querySelectorAll('.opcion-variante');
    opcionesDiv.forEach(op => {
        op.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const costo = this.getAttribute('data-costo');
            
            selectedOptionData = {
                id: id,
                nombre: nombre,
                costo: parseFloat(costo),
                venta: parseFloat(this.getAttribute('data-venta')),
                provId: this.getAttribute('data-provid'),
                provNombre: this.getAttribute('data-provnombre')
            };

            buscador.value = nombre;
            hiddenId.value = id;
            costoInput.value = costo;
            lista.classList.add('hidden');
        });
    });

    function agregarAlLote() {
        const cantidad = parseInt(cantInput.value);
        const costo = parseFloat(costoInput.value);

        if(!selectedOptionData || hiddenId.value === "" || isNaN(cantidad) || cantidad <= 0 || cantidad > 10000 || isNaN(costo) || costo <= 0 || costo > 100000) {
            alert('Por favor, selecciona un producto válido y asegúrate de que los parámetros numéricos sean reales y dentro de los límites.');
            return;
        }

        const id = selectedOptionData.id;
        const nombre = selectedOptionData.nombre;
        const precioVenta = selectedOptionData.venta;
        const provId = selectedOptionData.provId;
        const provNombre = selectedOptionData.provNombre;

        const existeIndex = loteItems.findIndex(item => item.id === id);

        if(existeIndex !== -1) {
            loteItems[existeIndex].cantidad += cantidad;
            loteItems[existeIndex].costo = costo;
        } else {
            loteItems.push({
                id: id,
                nombre: nombre,
                cantidad: cantidad,
                costo: costo,
                precioVenta: precioVenta,
                provId: provId,
                provNombre: provNombre
            });
        }

        cantInput.value = 1;
        costoInput.value = '';
        buscador.value = '';
        hiddenId.value = '';
        selectedOptionData = null;
        
        renderizarLoteTable();
    }

    function removerDelLote(index) {
        loteItems.splice(index, 1);
        renderizarLoteTable();
    }

    function renderizarLoteTable() {
        const tbody = document.getElementById('lote_tbody');
        const totalDisplay = document.getElementById('total_lote_display');
        const btnSubmit = document.getElementById('btnGuardarLote');
        const provListaDiv = document.getElementById('proveedores_detectados_lista');

        tbody.innerHTML = '';
        let totalAcumulado = 0;
        let proveedoresUnicos = new Set();

        if(loteItems.length === 0) {
            tbody.innerHTML = `<tr id="empty_row"><td colspan="6" class="p-12 text-center text-[#343c4c]/40 font-bold bg-[#f4f4f4]/50">No has añadido productos a esta nota de ingreso todavía.</td></tr>`;
            totalDisplay.innerText = "Bs 0.00";
            provListaDiv.innerHTML = "Ningún producto añadido.";
            btnSubmit.disabled = true;
            btnSubmit.className = "w-full mt-4 bg-[#f4f4f4] text-[#343c4c]/40 font-black py-4 rounded-xl shadow-sm transition-all pointer-events-none text-xs uppercase tracking-widest";
            return;
        }

        loteItems.forEach((item, index) => {
            const subtotal = item.parentTotal = item.cantidad * item.costo;
            totalAcumulado += subtotal;
            proveedoresUnicos.add(item.provNombre);

            // CÁLCULO MARGEN DE UTILIDAD EN TIEMPO REAL APLICADO A LA PALETA FCB
            const margenBs = item.precioVenta - item.costo;
            const margenPorcentaje = (margenBs / item.precioVenta) * 100;
            
            let badgeClass = "bg-[#0464a4]/10 text-[#0464a4] border-[#0464a4]/20"; // Saludable
            if (margenBs <= 0) {
                badgeClass = "bg-[#dc043c]/10 text-[#dc043c] border-[#dc043c]/20 font-black animate-pulse"; // Pérdida
            } else if (margenPorcentaje < 15) {
                badgeClass = "bg-[#dcb47c]/20 text-[#343c4c] border-[#dcb47c]/50"; // Advertencia
            }

            const tr = document.createElement('tr');
            tr.className = "hover:bg-[#f4f4f4]/50 transition-colors";
            tr.innerHTML = `
                <td class="p-4 font-bold text-[#343c4c] uppercase text-xs">
                    ${item.nombre}
                    <input type="hidden" name="variante_id[]" value="${item.id}">
                    <input type="hidden" name="cantidad[]" value="${item.cantidad}">
                    <input type="hidden" name="precio_compra[]" value="${item.costo}">
                </td>
                <td class="p-4"><span class="text-[10px] text-[#343c4c]/60 font-black uppercase tracking-wider">${item.provNombre}</span></td>
                <td class="p-4 text-center font-black text-lg text-[#0464a4]">${item.cantidad} un.</td>
                <td class="p-4 text-right font-black text-[#dc043c]">Bs ${item.costo.toFixed(2)}</td>
                <td class="p-4 text-center">
                    <span class="inline-block px-3 py-1.5 border text-[10px] rounded-lg font-black uppercase tracking-wider shadow-sm ${badgeClass}">
                        Bs ${margenBs.toFixed(2)} (${margenPorcentaje.toFixed(1)}%)
                    </span>
                </td>
                <td class="p-4 text-center">
                    <button type="button" onclick="removerDelLote(${index})" title="Quitar" class="text-[#dc043c] hover:bg-[#dc043c]/10 p-2 rounded-lg font-bold text-lg transition-colors">&times;</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        provListaDiv.innerHTML = "";
        proveedoresUnicos.forEach(p => {
            provListaDiv.innerHTML += `<div class="flex items-center text-[#343c4c] font-black text-[10px] uppercase tracking-widest"><span class="mr-2 text-[#0464a4]">🏢</span> ${p}</div>`;
        });

        totalDisplay.innerText = `Bs ${totalAcumulado.toFixed(2)}`;
        btnSubmit.disabled = false;
        btnSubmit.className = "w-full mt-4 bg-[#dc043c] hover:bg-[#343c4c] text-white font-black py-4 rounded-xl shadow-lg transition-colors text-xs uppercase tracking-widest transform hover:-translate-y-0.5 cursor-pointer";
    }

    async function cargarMasKardex() {
        const fechaInicio = document.getElementById('kardex_fecha_inicio').value;
        const fechaFin = document.getElementById('kardex_fecha_fin').value;
        
        const btn = document.getElementById('btnCargarMasKardex');
        btn.innerText = 'Cargando...';
        btn.disabled = true;

        try {
            const res = await fetch(`{{ route('personal.inventario.kardex') }}?offset=${kardexOffset}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
            const data = await res.json();
            
            if (data.movimientos.length === 0) {
                btn.innerText = 'No hay más resultados';
                return;
            }

            const tbody = document.getElementById('kardex_tbody');
            if(document.getElementById('kardex_empty')) {
                document.getElementById('kardex_empty').remove();
            }

            data.movimientos.forEach(mov => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-[#f4f4f4]/50 transition-colors kardex-row";
                tr.innerHTML = `
                    <td class="p-4 font-bold text-[#343c4c] uppercase">${mov.producto_nombre} <span class="text-[10px] text-[#0464a4]">(${mov.variante_info})</span></td>
                    <td class="p-4 text-center font-black text-[#0464a4]">+${mov.cantidad} un.</td>
                    <td class="p-4 text-center bg-[#f4f4f4]/50 font-bold text-[#343c4c]/60">${mov.stock_anterior} un.</td>
                    <td class="p-4 text-center font-black text-[#343c4c] bg-[#dcb47c]/10">${mov.stock_resultante} un.</td>
                    <td class="p-4 font-bold text-xs uppercase">${mov.operador}</td>
                    <td class="p-4 text-xs text-[#343c4c]/60">
                        ${mov.motivo}<br>
                        <span class="text-[10px] text-[#343c4c]/40">${mov.fecha}</span>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            kardexOffset += 25;
            btn.innerText = 'Cargar 25 más';
            btn.disabled = false;
        } catch (error) {
            console.error(error);
            btn.innerText = 'Error al cargar';
        }
    }

    function filtrarKardex() {
        kardexOffset = 0;
        document.getElementById('kardex_tbody').innerHTML = '';
        const btn = document.getElementById('btnCargarMasKardex');
        btn.disabled = false;
        btn.innerText = 'Cargar 25 más';
        cargarMasKardex();
    }
</script>
@endsection