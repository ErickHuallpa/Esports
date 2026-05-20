@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Punto de Venta (POS)</h2>
        <p class="text-gray-500 text-sm">Registra ventas presenciales en mostrador de forma rápida e intuitiva.</p>
    </div>
</div>

<form action="{{ route('cajero.pos.store') }}" method="POST" id="posForm">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-xl border p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 uppercase mb-4 border-b pb-2">1. Seleccionar Artículos</h3>
                
                <div class="flex flex-col md:flex-row gap-3 mb-4">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" id="search_input" onkeyup="filtrarProductos()" placeholder="Buscar por nombre, marca o color..." class="pl-10 w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-green-500">
                    </div>
                    <div class="w-full md:w-56 flex-shrink-0">
                        <select id="categoria_filter" onchange="filtrarProductos()" class="w-full rounded-lg border-gray-300 p-2 text-sm bg-gray-50 focus:ring-green-500">
                            <option value="">Todas las Categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-[350px] overflow-y-auto pr-2 pb-2" id="catalogo_pos">
                    @forelse($variantes as $v)
                        @php 
                            $fotos = json_decode($v->producto->imagen_url, true) ?? [];
                            $portada = count($fotos) > 0 ? $fotos[0] : null;
                        @endphp
                        
                        <div class="producto-card flex bg-white border border-gray-200 rounded-xl overflow-hidden hover:border-green-500 hover:shadow-md transition cursor-pointer group" 
                             data-nombre="{{ strtolower($v->producto->nombre) }}" 
                             data-marca="{{ strtolower($v->producto->marca ?? '') }}" 
                             data-color="{{ strtolower($v->color ?? '') }}" 
                             data-categoria="{{ $v->producto->categoria_id }}"
                             onclick="agregarVariantePos('{{ $v->id }}', '{{ addslashes($v->producto->nombre) }}', 'Talla: {{ $v->talla ?? 'N/A' }} | Color: {{ $v->color ?? 'N/A' }}', {{ $v->producto->precio_venta }}, {{ $v->stock }})">
                             
                            <div class="w-24 h-24 bg-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                @if($portada)
                                    <img src="{{ asset('storage/' . $portada) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                @else
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            
                            <div class="p-3 flex-grow flex flex-col justify-between">
                                <div>
                                    <div class="text-[9px] text-green-600 font-bold uppercase tracking-widest line-clamp-1">{{ $v->producto->categoria->nombre ?? 'General' }}</div>
                                    <h4 class="text-xs font-bold text-gray-800 line-clamp-2 leading-tight mt-0.5" title="{{ $v->producto->nombre }}">{{ $v->producto->nombre }}</h4>
                                    <p class="text-[10px] text-gray-500 mt-1">Talla: <span class="font-bold">{{ $v->talla ?? '-' }}</span> | Color: <span class="font-bold">{{ $v->color ?? '-' }}</span></p>
                                </div>
                                <div class="flex justify-between items-end mt-1">
                                    <span class="text-sm font-black text-gray-900">Bs {{ number_format($v->producto->precio_venta, 2) }}</span>
                                    <span class="text-[10px] font-bold px-2 py-0.5 bg-gray-100 rounded text-gray-700">Stock: {{ $v->stock }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-8 text-center text-gray-400 text-sm border-2 border-dashed rounded-xl">No hay productos en almacén.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b text-gray-700 uppercase text-xs font-bold">
                        <tr>
                            <th class="p-3">Artículo</th>
                            <th class="p-3 text-center">Cant.</th>
                            <th class="p-3 text-right">Precio Unit.</th>
                            <th class="p-3 text-right">Subtotal</th>
                            <th class="p-3 text-center">X</th>
                        </tr>
                    </thead>
                    <tbody id="pos_tbody" class="divide-y text-gray-600 bg-white">
                        <tr id="empty_row">
                            <td colspan="5" class="p-8 text-center text-gray-400">Haz clic en un artículo del catálogo superior para agregarlo al cobro.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-xl border p-5 shadow-sm">
                <div class="flex justify-between items-center border-b pb-2 mb-4">
                    <h3 class="text-sm font-bold text-gray-700 uppercase">2. Datos del Cliente</h3>
                    <span id="badge_cliente" class="hidden px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded">Cliente Frecuente</span>
                </div>

                <div class="space-y-3 relative">
                    <div id="loading_ci" class="hidden absolute top-2 right-2">
                        <div class="w-4 h-4 border-2 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600">Cédula de Identidad (C.I.) *</label>
                        <input type="text" id="ci_input" name="ci" required placeholder="Ej: 1234567" onblur="buscarClienteAJAX()" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600">Correo Electrónico *</label>
                        <input type="email" id="email_input" name="email" required placeholder="correo@ejemplo.com" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-green-500">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-600">Nombres *</label>
                            <input type="text" id="nombre_input" name="nombre" required placeholder="Nombres" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600">Apellidos *</label>
                            <input type="text" id="apellidos_input" name="apellidos" required placeholder="Apellidos" class="mt-1 block w-full rounded border-gray-300 p-2 text-sm focus:ring-green-500">
                        </div>
                    </div>
                    <p id="info_cuenta_nueva" class="text-[10px] text-blue-600 italic leading-tight mt-1">El sistema vinculará este correo para crear su cuenta. Su C.I. será la contraseña temporal.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border p-5 shadow-sm">
                <h3 class="text-sm font-bold text-gray-700 uppercase mb-4 border-b pb-2">3. Facturación</h3>
                
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 mb-2">Método de Pago Presencial *</label>
                    <select name="tipo_pago_id" required class="w-full rounded border-gray-300 p-2 text-sm font-bold bg-gray-50 focus:ring-green-500">
                        @foreach($tipoPagos as $tp)
                            <option value="{{ $tp->id }}">{{ $tp->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="border-t pt-4 bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-700">Total a Cobrar:</span>
                    <span class="text-3xl font-black text-green-600" id="total_display">Bs 0.00</span>
                </div>

                <button type="submit" id="btnProcesar" disabled class="w-full mt-4 bg-gray-400 text-white font-bold py-3.5 rounded-lg shadow-sm transition pointer-events-none">
                    Completar Transacción
                </button>
            </div>
        </div>
    </div>
</form>

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
                
                nombreInput.classList.add('bg-gray-100');
                apellidosInput.classList.add('bg-gray-100');
                emailInput.classList.add('bg-gray-100');

                badge.classList.remove('hidden');
                infoText.classList.add('hidden');
            } else {
                nombreInput.value = '';
                apellidosInput.value = '';
                emailInput.value = '';
                
                nombreInput.readOnly = false;
                apellidosInput.readOnly = false;
                emailInput.readOnly = false;

                nombreInput.classList.remove('bg-gray-100');
                apellidosInput.classList.remove('bg-gray-100');
                emailInput.classList.remove('bg-gray-100');

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
    // LOGICA DEL CARRITO DEL POS CON INCREMENTADORES
    // ==========================================
    let posItems = [];

    function agregarVariantePos(id, nombre, detalle, precio, stockMax) {
        const cantidad = 1; // Por defecto añade 1 unidad por cada clic
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
            tbody.innerHTML = `<tr id="empty_row"><td colspan="5" class="p-8 text-center text-gray-400">Haz clic en un artículo del catálogo superior para agregarlo al cobro.</td></tr>`;
            btnProcesar.disabled = true;
            btnProcesar.classList.remove('bg-green-600', 'hover:bg-green-700');
            btnProcesar.classList.add('bg-gray-400', 'pointer-events-none');
            totalDisplay.innerText = "Bs 0.00";
            return;
        }

        posItems.forEach((item, index) => {
            const subtotal = item.precio * item.cantidad;
            totalGeneral += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="p-3">
                    <p class="font-bold text-gray-800 line-clamp-1" title="${item.nombre}">${item.nombre}</p>
                    <p class="text-[10px] text-gray-500">${item.detalle}</p>
                    <input type="hidden" name="variante_id[]" value="${item.id}">
                    <input type="hidden" name="cantidad[]" value="${item.cantidad}">
                </td>
                <td class="p-3 text-center">
                    <div class="flex items-center justify-center space-x-1">
                        <button type="button" onclick="cambiarCantidad(${index}, -1)" class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-full font-bold text-gray-600 hover:bg-red-500 hover:text-white transition">-</button>
                        <span class="font-bold w-6 text-center text-sm">${item.cantidad}</span>
                        <button type="button" onclick="cambiarCantidad(${index}, 1)" class="w-6 h-6 flex items-center justify-center bg-gray-200 rounded-full font-bold text-gray-600 hover:bg-green-500 hover:text-white transition">+</button>
                    </div>
                </td>
                <td class="p-3 text-right text-gray-600">Bs ${item.precio.toFixed(2)}</td>
                <td class="p-3 text-right font-bold text-green-700">Bs ${subtotal.toFixed(2)}</td>
                <td class="p-3 text-center">
                    <button type="button" onclick="eliminarDelPos(${index})" class="text-red-400 hover:text-red-700 font-bold text-lg">&times;</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        totalDisplay.innerText = `Bs ${totalGeneral.toFixed(2)}`;
        
        btnProcesar.disabled = false;
        btnProcesar.classList.remove('bg-gray-400', 'pointer-events-none');
        btnProcesar.classList.add('bg-green-600', 'hover:bg-green-700');
    }
</script>
@endsection