@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Nota de Ingreso a Almacén (Suministros)</h1>
    <p class="text-gray-500 text-sm">Registra la llegada de mercadería. El sistema protege el precio de venta actual y calcula el margen de utilidad en tiempo real.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase border-b pb-2">1. Selector de Suministro</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="md:col-span-3">
                    <label class="block text-xs font-bold text-gray-600 mb-1">Buscar Variante de Producto</label>
                    <select id="variante_selector" class="w-full rounded-lg border-gray-300 border p-2.5 text-sm bg-gray-50 focus:ring-purple-500">
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
                                <option value="{{ $v->id }}" disabled class="text-red-400 bg-red-50">
                                    {{ $v->producto->nombre }} (⚠️ Sin Proveedor Asignado)
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Cantidad que entra</label>
                    <input type="number" id="cantidad_selector" min="1" value="1" class="w-full rounded-lg border-gray-300 border p-2 text-sm text-center font-bold">
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Nuevo Costo Compra (Bs)</label>
                    <input type="number" id="costo_selector" min="0.1" step="0.01" placeholder="0.00" class="w-full rounded-lg border-gray-300 border p-2 text-sm text-right font-semibold">
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="agregarAlLote()" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg shadow text-sm transition">
                        Añadir al lote
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <form action="{{ route('personal.inventario.store') }}" method="POST" id="inventarioForm">
                @csrf
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b text-gray-700 uppercase text-xs font-bold">
                            <tr>
                                <th class="p-3">Artículo / Variante</th>
                                <th class="p-3">Proveedor</th>
                                <th class="p-3 text-center">Cantidad</th>
                                <th class="p-3 text-right">Costo Unit.</th>
                                <th class="p-3 text-center">Margen Comercial</th>
                                <th class="p-3 text-center">X</th>
                            </tr>
                        </thead>
                        <tbody id="lote_tbody" class="divide-y text-gray-600">
                            <tr id="empty_row">
                                <td colspan="6" class="p-8 text-center text-gray-400">No has añadido productos a esta nota de ingreso todavía.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-xl border p-5 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-gray-700 uppercase border-b pb-2">2. Resumen y Confirmación</h3>
            
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Proveedores en el Lote</label>
                <div id="proveedores_detectados_lista" class="text-xs space-y-1 bg-gray-50 p-3 rounded-lg border text-gray-500 italic">
                    Ningún producto añadido.
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Observaciones de Recepción</label>
                <textarea name="motivo_general" rows="2" placeholder="Ej: Ingreso según Factura de Importación." class="w-full rounded-lg border-gray-300 border p-2 text-sm focus:ring-purple-500"></textarea>
            </div>

            <div class="border-t pt-4 bg-gray-50 p-3 rounded-lg flex justify-between items-center text-sm">
                <span class="font-bold text-gray-700">Inversión Lote Total:</span>
                <span class="text-xl font-black text-purple-700" id="total_lote_display">Bs 0.00</span>
            </div>

            <button type="submit" id="btnGuardarLote" disabled class="w-full bg-gray-400 text-white font-bold py-3 rounded-lg shadow-sm transition pointer-events-none text-sm uppercase tracking-wider">
                Procesar e Incrementar Almacén
            </button>
            </form>
        </div>
    </div>
</div>

<div class="mt-8">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-800">Últimos Ingresos Registrados (Auditoría Kárdex)</h3>
    </div>
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden text-xs">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b font-bold text-gray-700 uppercase">
                <tr>
                    <th class="p-3">Variante / Artículo</th>
                    <th class="p-3 text-center">Cant. Entrada</th>
                    <th class="p-3 text-center">Stock Anterior</th>
                    <th class="p-3 text-center">Stock Resultante</th>
                    <th class="p-3">Operador</th>
                    <th class="p-3">Detalle Histórico</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @forelse($movimientos as $mov)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-3 font-bold text-gray-900">{{ $mov->variante->producto->nombre }} ({{ $mov->variante->talla }} / {{ $mov->variante->color }})</td>
                        <td class="p-3 text-center font-bold text-green-600">+{{ $mov->cantidad }} un.</td>
                        <td class="p-3 text-center bg-gray-50/50">{{ $mov->stock_anterior }} un.</td>
                        <td class="p-3 text-center font-semibold text-purple-700 bg-purple-50/30">{{ $mov->stock_resultante }} un.</td>
                        <td class="p-3 font-medium">{{ $mov->user->persona->nombre }} {{ $mov->user->persona->apellidos }}</td>
                        <td class="p-3 text-gray-500">{{ $mov->motivo }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center text-gray-400">No se registran movimientos de entrada recientes.</td></tr>
                @endforelse
            </tbody>
        </table>
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
            tbody.innerHTML = `<tr id="empty_row"><td colspan="6" class="p-8 text-center text-gray-400">No has añadido productos a esta nota de ingreso todavía.</td></tr>`;
            totalDisplay.innerText = "Bs 0.00";
            provListaDiv.innerHTML = "Ningún producto añadido.";
            btnSubmit.disabled = true;
            btnSubmit.className = "w-full bg-gray-400 text-white font-bold py-3 rounded-lg shadow-sm transition pointer-events-none text-sm uppercase tracking-wider";
            return;
        }

        loteItems.forEach((item, index) => {
            const subtotal = item.parentTotal = item.cantidad * item.costo;
            totalAcumulado += subtotal;
            proveedoresUnicos.add(item.provNombre);

            // CÁLCULO MARGEN DE UTILIDAD EN TIEMPO REAL
            const margenBs = item.precioVenta - item.costo;
            const margenPorcentaje = (margenBs / item.precioVenta) * 100;
            
            // Estilo dinámico según salud del margen comercial
            let badgeClass = "bg-green-100 text-green-800 border-green-200";
            if (margenBs <= 0) {
                badgeClass = "bg-red-100 text-red-800 border-red-300 font-black animate-pulse";
            } else if (margenPorcentaje < 15) {
                badgeClass = "bg-amber-100 text-amber-800 border-amber-200";
            }

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="p-3 font-medium text-gray-900">
                    ${item.nombre}
                    <input type="hidden" name="variante_id[]" value="${item.id}">
                    <input type="hidden" name="cantidad[]" value="${item.whitespace = item.cantidad}">
                    <input type="hidden" name="precio_compra[]" value="${item.costo}">
                </td>
                <td class="p-3"><span class="text-xs text-gray-600 font-semibold">${item.provNombre}</span></td>
                <td class="p-3 text-center font-bold">${item.cantidad} un.</td>
                <td class="p-3 text-right font-semibold">Bs ${item.costo.toFixed(2)}</td>
                <td class="p-3 text-center">
                    <span class="px-2 py-1 border text-[11px] rounded-lg font-bold ${badgeClass}">
                        Bs ${margenBs.toFixed(2)} (${margenPorcentaje.toFixed(1)}%)
                    </span>
                </td>
                <td class="p-3 text-center">
                    <button type="button" onclick="removerDelLote(${index})" class="text-red-500 hover:text-red-700 font-bold text-lg">&times;</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        provListaDiv.innerHTML = "";
        proveedoresUnicos.forEach(p => {
            provListaDiv.innerHTML += `<div class="flex items-center text-gray-700 font-bold text-xs">🏢 ${p}</div>`;
        });

        totalDisplay.innerText = `Bs ${totalAcumulado.toFixed(2)}`;
        btnSubmit.disabled = false;
        btnSubmit.className = "w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 rounded-lg shadow transition text-sm cursor-pointer uppercase tracking-wider";
    }
</script>
@endsection