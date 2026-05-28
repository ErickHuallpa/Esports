@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4 max-w-7xl mx-auto">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Punto de Venta (POS)</h1>
        <p class="text-gray-500 text-sm mt-1 font-medium">Registra ventas presenciales en mostrador de forma rápida e intuitiva.</p>
    </div>
</div>

<form action="{{ route('cajero.pos.store') }}" method="POST" id="posForm" class="max-w-7xl mx-auto">
    @csrf
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        
        <!-- COLUMNA IZQUIERDA: Catálogo y Carrito -->
        <div class="xl:col-span-2 space-y-6">
            
            <!-- PANEL 1: CATÁLOGO -->
            <div class="bg-white rounded-2xl border border-[#343c4c]/10 shadow-xl overflow-hidden">
                <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#dcb47c]">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#dcb47c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        1. Seleccionar Artículos
                    </h3>
                </div>
                
                <div class="p-6">
                    <!-- Filtros -->
                    <div class="flex flex-col md:flex-row gap-4 mb-6">
                        <div class="relative flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-[#343c4c]/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" id="search_input" onkeyup="filtrarProductos()" placeholder="Buscar por nombre, marca o color..." 
                                class="pl-12 w-full rounded-xl border-none bg-[#f4f4f4] p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-medium text-[#343c4c]">
                        </div>
                        <div class="w-full md:w-64 flex-shrink-0">
                            <select id="categoria_filter" onchange="filtrarProductos()" 
                                class="w-full rounded-xl border-none p-3 text-sm bg-[#f4f4f4] focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer">
                                <option value="">Todas las Categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Grilla de Productos -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 pb-2 custom-scrollbar" id="catalogo_pos">
                        @forelse($variantes as $v)
                            @php 
                                $fotos = json_decode($v->producto->imagen_url, true) ?? [];
                                $portada = count($fotos) > 0 ? $fotos[0] : null;
                            @endphp
                            
                            <div class="producto-card flex bg-white border-2 border-[#f4f4f4] rounded-xl overflow-hidden hover:border-[#0464a4] hover:shadow-lg transition-all cursor-pointer group relative" 
                                 data-nombre="{{ strtolower($v->producto->nombre) }}" 
                                 data-marca="{{ strtolower($v->producto->marca ?? '') }}" 
                                 data-color="{{ strtolower($v->color ?? '') }}" 
                                 data-categoria="{{ $v->producto->categoria_id }}"
                                 onclick="agregarVariantePos('{{ $v->id }}', '{{ addslashes($v->producto->nombre) }}', 'Talla: {{ $v->talla ?? 'N/A' }} | Color: {{ $v->color ?? 'N/A' }}', {{ $v->producto->precio_venta }}, {{ $v->stock }})">
                                 
                                <div class="w-28 bg-[#f4f4f4] flex-shrink-0 flex items-center justify-center overflow-hidden p-2">
                                    @if($portada)
                                        <img src="{{ asset('storage/' . $portada) }}" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500 drop-shadow-sm">
                                    @else
                                        <svg class="w-8 h-8 text-[#343c4c]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                                
                                <div class="p-4 flex-grow flex flex-col justify-between">
                                    <div>
                                        <div class="text-[9px] text-[#dcb47c] font-black uppercase tracking-widest line-clamp-1 mb-1">{{ $v->producto->categoria->nombre ?? 'General' }}</div>
                                        <h4 class="text-sm font-bold text-[#343c4c] line-clamp-2 leading-tight group-hover:text-[#0464a4] transition-colors" title="{{ $v->producto->nombre }}">{{ $v->producto->nombre }}</h4>
                                        <p class="text-[10px] text-[#343c4c]/60 mt-1.5 font-medium">Talla: <span class="font-black text-[#343c4c]">{{ $v->talla ?? '-' }}</span> | Color: <span class="font-black text-[#343c4c]">{{ $v->color ?? '-' }}</span></p>
                                    </div>
                                    <div class="flex justify-between items-end mt-3">
                                        <span class="text-base font-black text-[#dc043c]">Bs {{ number_format($v->producto->precio_venta, 2) }}</span>
                                        <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 bg-[#343c4c] text-white rounded shadow-sm">Stock: {{ $v->stock }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-10 text-center text-[#343c4c]/40 font-bold border-2 border-dashed border-[#dcb47c] rounded-xl bg-white">No hay productos en almacén.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- PANEL 2: TABLA DE CARRITO -->
            <div class="bg-white rounded-2xl border border-[#343c4c]/10 shadow-xl overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-[10px] font-black tracking-widest">
                        <tr>
                            <th class="p-4">Artículo</th>
                            <th class="p-4 text-center">Cant.</th>
                            <th class="p-4 text-right">Precio Unit.</th>
                            <th class="p-4 text-right">Subtotal</th>
                            <th class="p-4 text-center">X</th>
                        </tr>
                    </thead>
                    <tbody id="pos_tbody" class="divide-y divide-[#f4f4f4] bg-white text-[#343c4c]">
                        <tr id="empty_row">
                            <td colspan="5" class="p-10 text-center text-[#343c4c]/40 font-bold text-sm bg-[#f4f4f4]/50">
                                🛒 Haz clic en un artículo del catálogo superior para agregarlo al cobro.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- COLUMNA DERECHA: Cliente y Facturación -->
        <div class="space-y-6">
            
            <!-- PANEL 3: DATOS DEL CLIENTE -->
            <div class="bg-white rounded-2xl border border-[#343c4c]/10 shadow-xl overflow-hidden">
                <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#0464a4] flex justify-between items-center">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#0464a4]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        2. Datos del Cliente
                    </h3>
                    <span id="badge_cliente" class="hidden px-2 py-1 bg-[#dcb47c] text-[#343c4c] text-[9px] font-black uppercase tracking-widest rounded shadow-sm">Cliente Frecuente</span>
                </div>

                <div class="p-6 space-y-4 relative">
                    <div id="loading_ci" class="hidden absolute top-4 right-6">
                        <div class="w-5 h-5 border-2 border-[#0464a4] border-t-transparent rounded-full animate-spin"></div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">C.I. *</label>
                        <input type="text" id="ci_input" name="ci" required placeholder="Ej: 1234567" onblur="buscarClienteAJAX()" 
                            class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Correo Electrónico *</label>
                        <input type="email" id="email_input" name="email" required placeholder="correo@ejemplo.com" 
                            class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Nombres *</label>
                            <input type="text" id="nombre_input" name="nombre" required placeholder="Nombres" 
                                class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Apellidos *</label>
                            <input type="text" id="apellidos_input" name="apellidos" required placeholder="Apellidos" 
                                class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c]">
                        </div>
                    </div>
                    <p id="info_cuenta_nueva" class="text-[10px] text-[#0464a4] bg-[#0464a4]/10 p-2 rounded-lg font-bold leading-tight mt-2 border border-[#0464a4]/20">
                        ℹ️ El sistema vinculará este correo para crear su cuenta. Su C.I. será la contraseña temporal.
                    </p>
                </div>
            </div>

            <!-- PANEL 4: FACTURACIÓN Y COBRO -->
            <div class="bg-white rounded-2xl border border-[#343c4c]/10 shadow-xl overflow-hidden">
                <div class="bg-[#343c4c] px-6 py-4 border-b-4 border-[#dc043c] flex justify-between items-center">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center">
                        <svg class="w-5 h-5 mr-2 text-[#dc043c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        3. Facturación
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Método de Pago *</label>
                        <select name="tipo_pago_id" required class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] cursor-pointer">
                            @foreach($tipoPagos as $tp)
                                <option value="{{ $tp->id }}">{{ $tp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="border-t-2 border-[#f4f4f4] pt-6 bg-white flex flex-col items-center justify-center space-y-2">
                        <span class="text-[10px] font-black text-[#343c4c]/50 uppercase tracking-widest">Total a Cobrar</span>
                        <span class="text-4xl font-black text-[#dc043c] drop-shadow-sm" id="total_display">Bs 0.00</span>
                    </div>

                    <button type="submit" id="btnProcesar" disabled class="w-full mt-6 bg-[#f4f4f4] text-[#343c4c]/40 font-black uppercase tracking-widest py-4 rounded-xl shadow-sm transition-all pointer-events-none text-sm">
                        Completar Transacción
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    /* Scrollbar minimalista para el catálogo */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f4f4f4; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #dcb47c; border-radius: 10px; }
</style>

<script>
    // ==========================================
    // BUSCADOR EN TIEMPO REAL DE PRODUCTOS
    // ==========================================
    function filtrarProductos() {
        const text = document.getElementById('search_input').value.toLowerCase();
        const cat = document.getElementById('categoria_filter').value;
        const cards = document.querySelectorAll('.producto-card');

        cards.forEach(card => {
            const nombre = card.getAttribute('data-nombre');
            const marca = card.getAttribute('data-marca');
            const color = card.getAttribute('data-color');
            const categoria = card.getAttribute('data-categoria');

            const matchText = nombre.includes(text) || marca.includes(text) || color.includes(text);
            const matchCat = cat === "" || categoria === cat;

            if (matchText && matchCat) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // ==========================================
    // BUSCADOR INTELIGENTE DE CLIENTES AJAX
    // ==========================================
    async function buscarClienteAJAX() {
        const ciInput = document.getElementById('ci_input').value;
        if(ciInput.trim() === '') return;

        document.getElementById('loading_ci').classList.remove('hidden');

        try {
            const response = await fetch(`{{ route('cajero.pos.buscarCliente') }}?ci=${ciInput}`);
            const data = await response.json();

            const nombreInput = document.getElementById('nombre_input');
            const apellidosInput = document.getElementById('apellidos_input');
            const emailInput = document.getElementById('email_input');
            const badge = document.getElementById('badge_cliente');
            const infoText = document.getElementById('info_cuenta_nueva');

            if (data.encontrado) {
                nombreInput.value = data.nombre;
                apellidosInput.value = data.apellidos;
                emailInput.value = data.email;
                
                nombreInput.readOnly = true;
                apellidosInput.readOnly = true;
                emailInput.readOnly = true;
                
                nombreInput.classList.add('opacity-70', 'cursor-not-allowed');
                apellidosInput.classList.add('opacity-70', 'cursor-not-allowed');
                emailInput.classList.add('opacity-70', 'cursor-not-allowed');

                badge.classList.remove('hidden');
                infoText.classList.add('hidden');
            } else {
                nombreInput.value = '';
                apellidosInput.value = '';
                emailInput.value = '';
                
                nombreInput.readOnly = false;
                apellidosInput.readOnly = false;
                emailInput.readOnly = false;

                nombreInput.classList.remove('opacity-70', 'cursor-not-allowed');
                apellidosInput.classList.remove('opacity-70', 'cursor-not-allowed');
                emailInput.classList.remove('opacity-70', 'cursor-not-allowed');

                badge.classList.add('hidden');
                infoText.classList.remove('hidden');
            }
        } catch (error) {
            console.error("Error al buscar cliente:", error);
        } finally {
            document.getElementById('loading_ci').classList.add('hidden');
        }
    }

    // ==========================================
    // LOGICA DEL CARRITO DEL POS
    // ==========================================
    let posItems = [];

    function agregarVariantePos(id, nombre, detalle, precio, stockMax) {
        const cantidad = 1;
        const existeIndex = posItems.findIndex(item => item.id === id);
        
        if (existeIndex !== -1) {
            if(posItems[existeIndex].cantidad + cantidad > stockMax) {
                alert('No puedes exceder el stock disponible en almacén (' + stockMax + ').');
                return;
            }
            posItems[existeIndex].cantidad += cantidad;
        } else {
            if(cantidad > stockMax) {
                alert('Stock agotado en almacén.');
                return;
            }
            posItems.push({
                id: id,
                nombre: nombre,
                detalle: detalle,
                precio: parseFloat(precio),
                cantidad: cantidad,
                stockMax: parseInt(stockMax)
            });
        }
        renderizarTabla();
    }

    function cambiarCantidad(index, delta) {
        const item = posItems[index];
        const nuevaCant = item.cantidad + delta;
        
        if (nuevaCant <= 0) {
            eliminarDelPos(index);
            return;
        }
        
        if (nuevaCant > item.stockMax) {
            alert('Stock máximo en almacén alcanzado (' + item.stockMax + ').');
            return;
        }
        
        item.cantidad = nuevaCant;
        renderizarTabla();
    }

    function eliminarDelPos(index) {
        posItems.splice(index, 1);
        renderizarTabla();
    }

    function renderizarTabla() {
        const tbody = document.getElementById('pos_tbody');
        const btnProcesar = document.getElementById('btnProcesar');
        const totalDisplay = document.getElementById('total_display');
        
        tbody.innerHTML = '';
        let totalGeneral = 0;

        if (posItems.length === 0) {
            tbody.innerHTML = `<tr id="empty_row"><td colspan="5" class="p-10 text-center text-[#343c4c]/40 font-bold text-sm bg-[#f4f4f4]/50">🛒 Haz clic en un artículo del catálogo superior para agregarlo al cobro.</td></tr>`;
            
            // Botón Desactivado
            btnProcesar.disabled = true;
            btnProcesar.className = "w-full mt-6 bg-[#f4f4f4] text-[#343c4c]/40 font-black uppercase tracking-widest py-4 rounded-xl shadow-sm transition-all pointer-events-none text-sm";
            totalDisplay.innerText = "Bs 0.00";
            return;
        }

        posItems.forEach((item, index) => {
            const subtotal = item.precio * item.cantidad;
            totalGeneral += subtotal;

            const tr = document.createElement('tr');
            tr.className = "hover:bg-[#f4f4f4]/50 transition-colors";
            tr.innerHTML = `
                <td class="p-4">
                    <p class="font-bold text-[#343c4c] line-clamp-1" title="${item.nombre}">${item.nombre}</p>
                    <p class="text-[10px] font-semibold text-[#343c4c]/60 mt-0.5">${item.detalle}</p>
                    <input type="hidden" name="variante_id[]" value="${item.id}">
                    <input type="hidden" name="cantidad[]" value="${item.cantidad}">
                </td>
                <td class="p-4 text-center">
                    <div class="flex items-center justify-center space-x-1">
                        <button type="button" onclick="cambiarCantidad(${index}, -1)" class="w-7 h-7 flex items-center justify-center bg-[#f4f4f4] rounded-full font-black text-[#343c4c] hover:bg-[#dc043c] hover:text-white transition-colors shadow-sm">-</button>
                        <span class="font-black w-8 text-center text-sm text-[#343c4c]">${item.cantidad}</span>
                        <button type="button" onclick="cambiarCantidad(${index}, 1)" class="w-7 h-7 flex items-center justify-center bg-[#f4f4f4] rounded-full font-black text-[#343c4c] hover:bg-[#0464a4] hover:text-white transition-colors shadow-sm">+</button>
                    </div>
                </td>
                <td class="p-4 text-right font-bold text-[#343c4c]/70 text-sm">Bs ${item.precio.toFixed(2)}</td>
                <td class="p-4 text-right font-black text-[#dc043c] text-sm">Bs ${subtotal.toFixed(2)}</td>
                <td class="p-4 text-center">
                    <button type="button" onclick="eliminarDelPos(${index})" title="Quitar" class="text-[#dc043c] hover:bg-[#dc043c]/10 p-2 rounded-lg font-bold text-lg transition-colors">&times;</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        totalDisplay.innerText = `Bs ${totalGeneral.toFixed(2)}`;
        
        // Botón Activado
        btnProcesar.disabled = false;
        btnProcesar.className = "w-full mt-6 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-widest py-4 rounded-xl shadow-lg transition-all text-sm transform hover:-translate-y-0.5";
    }
</script>
@endsection