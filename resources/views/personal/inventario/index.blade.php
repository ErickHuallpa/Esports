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
                        <div class="md:col-span-3">
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Buscar Variante de Producto</label>
                            <select id="variante_selector" class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer">
                                <option value="">Seleccione el artículo que ingresa...</option>
                                @foreach($variantes as $v)
                                    @if($v->producto->proveedor)
                                        <option value="{{ $v->id }}" 
                                                data-nombre="{{ $v->producto->nombre }} ({{ $v->talla }} / {{ $v->color }})"
                                                data-costo="{{ $v->producto->precio_compra }}"
                                                data-venta="{{ $v->producto->precio_venta }}"
                                                data-provid="{{ $v->producto->proveedor_id }}"
                                                data-provnombre="{{ $v->producto->proveedor->nombre_empresa }}">
                                            [{{ $v->producto->marca ?? 'E-Sports' }}] {{ $v->producto->nombre }} - Talla: {{ $v->talla ?? 'N/A' }} | Color: {{ $v->color ?? 'N/A' }} (Venta actual: Bs {{ $v->producto->precio_venta }})
                                        </option>
                                    @else
                                        <option value="{{ $v->id }}" disabled class="text-[#dc043c] bg-[#dc043c]/10">
                                            {{ $v->producto->nombre }} (⚠️ Sin Proveedor Asignado)
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Cantidad que entra</label>
                            <input type="number" id="cantidad_selector" min="1" value="1" 
                                class="w-full bg-[#f4f4f4] border-none rounded-xl p-3.5 text-sm text-center focus:ring-2 focus:ring-[#0464a4] font-black text-[#343c4c]">
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nuevo Costo (Bs)</label>
                            <input type="number" id="costo_selector" min="0.1" step="0.01" placeholder="0.00" 
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
        <div class="mb-5 flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="text-xl font-black text-[#343c4c] uppercase tracking-tight">Últimos Ingresos Registrados</h3>
            <span class="ml-3 px-2 py-0.5 bg-[#dcb47c] text-[#343c4c] text-[10px] font-black rounded uppercase tracking-widest">Auditoría Kárdex</span>
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
                    <tbody class="divide-y divide-[#f4f4f4] text-[#343c4c] font-medium">
                        @forelse($movimientos as $mov)
                            <tr class="hover:bg-[#f4f4f4]/50 transition-colors">
                                <td class="p-4 font-bold text-[#343c4c] uppercase">{{ $mov->variante->producto->nombre }} <span class="text-[10px] text-[#0464a4]">({{ $mov->variante->talla }} / {{ $mov->variante->color }})</span></td>
                                <td class="p-4 text-center font-black text-[#0464a4]">+{{ $mov->cantidad }} un.</td>
                                <td class="p-4 text-center bg-[#f4f4f4]/50 font-bold text-[#343c4c]/60">{{ $mov->stock_anterior }} un.</td>
                                <td class="p-4 text-center font-black text-[#343c4c] bg-[#dcb47c]/10">{{ $mov->stock_resultante }} un.</td>
                                <td class="p-4 font-bold text-xs uppercase">{{ $mov->user->persona->nombre }} {{ $mov->user->persona->apellidos }}</td>
                                <td class="p-4 text-xs text-[#343c4c]/60">{{ $mov->motivo }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-10 text-center text-[#343c4c]/40 font-bold bg-white">No se registran movimientos de entrada recientes.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    let loteItems = [];

    document.getElementById('variante_selector').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if(!option.value) return;
        document.getElementById('costo_selector').value = option.getAttribute('data-costo');
    });

    function agregarAlLote() {
        const selector = document.getElementById('variante_selector');
        const cantInput = document.getElementById('cantidad_selector');
        const costoInput = document.getElementById('costo_selector');

        const cantidad = parseInt(cantInput.value);
        const costo = parseFloat(costoInput.value);

        if(selector.value === "" || isNaN(cantidad) || cantidad <= 0 || isNaN(costo) || costo <= 0) {
            alert('Por favor, ingresa parámetros numéricos reales superiores a cero.');
            return;
        }

        const option = selector.options[selector.selectedIndex];
        const id = option.value;
        const nombre = option.getAttribute('data-nombre');
        const precioVenta = parseFloat(option.getAttribute('data-venta'));
        const provId = option.getAttribute('data-provid');
        const provNombre = option.getAttribute('data-provnombre');

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
        selector.value = '';
        
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
</script>
@endsection