@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Directorio de Proveedores</h2>
        <p class="text-gray-500 text-sm">Gestiona las empresas que suministran tus productos deportivos.</p>
    </div>
    <button onclick="openModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow flex items-center transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nuevo Proveedor
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($proveedores as $prov)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
            <div class="p-5">
                <div class="flex justify-between items-start">
                    <h3 class="text-lg font-bold text-gray-800">{{ $prov->nombre_empresa }}</h3>
                    @if($prov->activo)
                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Activo</span>
                    @else
                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-semibold rounded-full">Inactivo</span>
                    @endif
                </div>
                
                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <p><strong class="text-gray-700">Contacto:</strong> {{ $prov->contacto_nombre ?? 'No registrado' }}</p>
                    <p><strong class="text-gray-700">Teléfono:</strong> {{ $prov->telefono ?? 'No registrado' }}</p>
                    <p><strong class="text-gray-700">Email:</strong> {{ $prov->email ?? 'No registrado' }}</p>
                    <p><strong class="text-gray-700">Ubicación:</strong> {{ $prov->ciudad ?? 'N/A' }}, {{ $prov->pais ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div class="bg-gray-50 px-5 py-3 border-t border-gray-200 flex justify-end space-x-2 rounded-b-lg">
                <button onclick="openModal({{ $prov->toJson() }})" class="text-blue-600 hover:text-blue-800 text-sm font-semibold px-3 py-1 bg-blue-100 rounded transition">
                    Editar
                </button>
                <button onclick="openDeleteModal({{ $prov->id }}, '{{ $prov->nombre_empresa }}')" class="text-red-600 hover:text-red-800 text-sm font-semibold px-3 py-1 bg-red-100 rounded transition">
                    Eliminar
                </button>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white p-8 text-center rounded-lg border border-gray-200 shadow-sm">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            <p class="text-gray-500 text-lg">No hay proveedores registrados aún.</p>
        </div>
    @endforelse
</div>

<div id="proveedorModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl mx-4 overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 id="modalTitle" class="text-lg font-bold text-gray-800">Nuevo Proveedor</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">&times;</button>
        </div>

        <form id="proveedorForm" method="POST" action="{{ route('proveedores.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Nombre de la Empresa *</label>
                    <input type="text" name="nombre_empresa" id="nombre_empresa" required maxlength="150" class="val-input mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-red-600 mt-1 font-semibold hidden" id="err_nombre_empresa"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Persona de Contacto</label>
                    <input type="text" name="contacto_nombre" id="contacto_nombre" maxlength="150" class="val-input mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-red-600 mt-1 font-semibold hidden" id="err_contacto_nombre"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <div class="flex mt-1">
                        <select id="codigo_pais" class="val-input rounded-l-md border-gray-300 shadow-sm border-y border-l bg-gray-50 text-gray-700 py-2 px-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <option value="+591">+591 (BOL)</option>
                            <option value="+54">+54 (ARG)</option>
                            <option value="+51">+51 (PER)</option>
                            <option value="+56">+56 (CHI)</option>
                            <option value="+55">+55 (BRA)</option>
                            <option value="+57">+57 (COL)</option>
                            <option value="+52">+52 (MEX)</option>
                            <option value="+1">+1 (US/CA)</option>
                            <option value="+34">+34 (ESP)</option>
                        </select>
                        <input type="text" id="telefono_num" maxlength="15" class="val-input flex-1 rounded-r-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <input type="hidden" name="telefono" id="telefono">
                    <p class="text-xs text-red-600 mt-1 font-semibold hidden" id="err_telefono"></p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input type="email" name="email" id="email" maxlength="100" class="val-input mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-red-600 mt-1 font-semibold hidden" id="err_email"></p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <textarea name="direccion" id="direccion" rows="2" maxlength="255" class="val-input mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    <p class="text-xs text-red-600 mt-1 font-semibold hidden" id="err_direccion"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">País *</label>
                    <select name="pais" id="pais" required class="val-input mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione un país...</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Perú">Perú</option>
                        <option value="Chile">Chile</option>
                        <option value="Brasil">Brasil</option>
                        <option value="Colombia">Colombia</option>
                        <option value="México">México</option>
                        <option value="Estados Unidos">Estados Unidos</option>
                        <option value="Canadá">Canadá</option>
                        <option value="España">España</option>
                    </select>
                    <p class="text-xs text-red-600 mt-1 font-semibold hidden" id="err_pais"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ciudad *</label>
                    <select name="ciudad" id="ciudad" required class="val-input mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Primero seleccione un país...</option>
                    </select>
                    <p class="text-xs text-red-600 mt-1 font-semibold hidden" id="err_ciudad"></p>
                </div>
            </div>

            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-medium transition">Cancelar</button>
                <button type="submit" id="btnSubmit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold transition disabled:opacity-50 disabled:cursor-not-allowed">Guardar Proveedor</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center transition-opacity duration-300">
    <div class="bg-white rounded-lg shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="p-6 text-center">
                <svg class="mx-auto mb-4 w-14 h-14 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="mb-5 text-lg font-normal text-gray-500">¿Estás seguro de que deseas eliminar al proveedor <strong id="deleteProvName" class="text-gray-800"></strong>?</h3>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 font-medium transition">Cancelar</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 font-bold transition">Sí, eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const citiesByCountry = {
        'Bolivia': ['La Paz', 'Santa Cruz', 'Cochabamba', 'Oruro', 'Potosí', 'Tarija', 'Chuquisaca', 'Beni', 'Pando'],
        'Argentina': ['Buenos Aires', 'Córdoba', 'Rosario', 'Mendoza', 'Tucumán'],
        'Perú': ['Lima', 'Arequipa', 'Trujillo', 'Chiclayo', 'Cusco'],
        'Chile': ['Santiago', 'Valparaíso', 'Concepción', 'Antofagasta', 'La Serena'],
        'Brasil': ['São Paulo', 'Río de Janeiro', 'Brasilia', 'Salvador', 'Fortaleza'],
        'Colombia': ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena'],
        'México': ['Ciudad de México', 'Guadalajara', 'Monterrey', 'Puebla', 'Tijuana'],
        'Estados Unidos': ['Nueva York', 'Los Ángeles', 'Chicago', 'Houston', 'Miami'],
        'Canadá': ['Toronto', 'Montreal', 'Vancouver', 'Calgary', 'Ottawa'],
        'España': ['Madrid', 'Barcelona', 'Valencia', 'Sevilla', 'Zaragoza']
    };

    const countryCodesMap = {
        '+591': 'Bolivia',
        '+54': 'Argentina',
        '+51': 'Perú',
        '+56': 'Chile',
        '+55': 'Brasil',
        '+57': 'Colombia',
        '+52': 'México',
        '+1': 'Estados Unidos', 
        '+34': 'España'
    };

    document.getElementById('codigo_pais').addEventListener('change', function(e) {
        const country = countryCodesMap[e.target.value];
        if(country) {
            document.getElementById('pais').value = country;
            updateCities();
            checkFormValidity();
        }
    });

    document.getElementById('pais').addEventListener('change', function() {
        updateCities();
    });

    function updateCities(selectedCity = null) {
        const pais = document.getElementById('pais').value;
        const ciudadSelect = document.getElementById('ciudad');
        
        ciudadSelect.innerHTML = '<option value="">Seleccione una ciudad...</option>';
        if(pais && citiesByCountry[pais]) {
            citiesByCountry[pais].forEach(c => {
                const opt = document.createElement('option');
                opt.value = c;
                opt.innerText = c;
                ciudadSelect.appendChild(opt);
            });
            if(selectedCity && citiesByCountry[pais].includes(selectedCity)) {
                ciudadSelect.value = selectedCity;
            }
        }
    }

    // Validaciones
    function valNombreEmpresa(v) {
        if(!v.trim()) return "El nombre de la empresa es obligatorio.";
        if(v.length < 2) return "Debe tener al menos 2 caracteres.";
        return null;
    }
    
    function valContacto(v) {
        if(!v.trim()) return null; // Opcional
        if(!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(v)) return "Solo se admiten letras y espacios.";
        if(v.length < 2) return "Debe tener al menos 2 letras.";
        return null;
    }

    function valTelefono() {
        const num = document.getElementById('telefono_num').value.trim();
        if(!num) return null; // Opcional
        if(!/^[0-9]+$/.test(num)) return "Solo se admiten números.";
        if(num.length < 6) return "El número es demasiado corto.";
        return null;
    }

    function valEmail(v) {
        if(!v.trim()) return null; // Opcional
        if(!/^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(v)) return "Formato de correo no válido.";
        return null;
    }

    function valCiudad(v) {
        if(!v.trim()) return "La ciudad es obligatoria.";
        return null;
    }

    function valPais(v) {
        if(!v.trim()) return "El país es obligatorio.";
        return null;
    }

    function checkFormValidity() {
        const errs = [];
        
        const setErr = (id, msg) => {
            const el = document.getElementById(id);
            const inputId = id.replace('err_', '');
            const input = document.getElementById(inputId);
            
            if(msg) {
                el.innerText = msg;
                el.classList.remove('hidden');
                if(input) { input.classList.add('border-red-500', 'bg-red-50'); input.classList.remove('border-green-500', 'bg-green-50', 'border-gray-300'); }
                errs.push(msg);
            } else {
                el.classList.add('hidden');
                if(input && input.value.trim().length > 0) { 
                    input.classList.remove('border-red-500', 'bg-red-50', 'border-gray-300'); 
                    input.classList.add('border-green-500', 'bg-green-50'); 
                } else if(input) {
                    input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
                    input.classList.add('border-gray-300');
                }
            }
        };

        const tErr = valTelefono();
        setErr('err_telefono', tErr);
        if(tErr) {
            document.getElementById('telefono_num').classList.add('border-red-500', 'bg-red-50');
        } else if(document.getElementById('telefono_num').value.trim()) {
            document.getElementById('telefono_num').classList.add('border-green-500', 'bg-green-50');
            document.getElementById('telefono_num').classList.remove('border-red-500', 'bg-red-50', 'border-gray-300');
        } else {
            document.getElementById('telefono_num').classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
        }

        setErr('err_nombre_empresa', valNombreEmpresa(document.getElementById('nombre_empresa').value));
        setErr('err_contacto_nombre', valContacto(document.getElementById('contacto_nombre').value));
        setErr('err_email', valEmail(document.getElementById('email').value));
        setErr('err_ciudad', valCiudad(document.getElementById('ciudad').value));
        setErr('err_pais', valPais(document.getElementById('pais').value));

        const btnSubmit = document.getElementById('btnSubmit');
        btnSubmit.disabled = errs.length > 0 || !document.getElementById('nombre_empresa').value.trim();
    }

    document.querySelectorAll('.val-input').forEach(el => {
        el.addEventListener('input', checkFormValidity);
        el.addEventListener('change', checkFormValidity);
    });

    document.getElementById('proveedorForm').addEventListener('submit', function(e) {
        const telNum = document.getElementById('telefono_num').value.trim();
        if(telNum) {
            document.getElementById('telefono').value = document.getElementById('codigo_pais').value + " " + telNum;
        } else {
            document.getElementById('telefono').value = "";
        }
    });

    function parseTelefono(fullTelefono) {
        if(!fullTelefono) return { codigo: '+591', numero: '' };
        const parts = fullTelefono.split(' ');
        if(parts.length > 1 && parts[0].startsWith('+')) {
            const codigo = parts[0];
            const numero = parts.slice(1).join(' ');
            return { codigo, numero };
        }
        return { codigo: '+591', numero: fullTelefono }; // Fallback
    }

    function openModal(proveedor = null) {
        const modal = document.getElementById('proveedorModal');
        const form = document.getElementById('proveedorForm');
        const method = document.getElementById('formMethod');
        const title = document.getElementById('modalTitle');
        const btnSubmit = document.getElementById('btnSubmit');

        // Limpiar estilos de error
        document.querySelectorAll('.val-input').forEach(input => {
            input.classList.remove('border-red-500', 'bg-red-50', 'border-green-500', 'bg-green-50');
            input.classList.add('border-gray-300');
        });
        document.querySelectorAll('[id^="err_"]').forEach(el => el.classList.add('hidden'));

        if (proveedor) {
            // Configuración en Modo Edición
            title.innerText = 'Editar Proveedor';
            btnSubmit.innerText = 'Actualizar Cambios';
            form.action = `/proveedores/${proveedor.id}`;
            method.value = 'PUT'; 

            document.getElementById('nombre_empresa').value = proveedor.nombre_empresa || '';
            document.getElementById('contacto_nombre').value = proveedor.contacto_nombre || '';
            
            const tel = parseTelefono(proveedor.telefono);
            const selectCodigo = document.getElementById('codigo_pais');
            if(Array.from(selectCodigo.options).some(opt => opt.value === tel.codigo)) {
                selectCodigo.value = tel.codigo;
            } else {
                selectCodigo.value = '+591'; // default si no coincide
            }
            document.getElementById('telefono_num').value = tel.numero || '';
            
            document.getElementById('email').value = proveedor.email || '';
            document.getElementById('direccion').value = proveedor.direccion || '';
            
            document.getElementById('pais').value = proveedor.pais || '';
            updateCities(proveedor.ciudad || '');
        } else {
            // Configuración en Modo Creación
            title.innerText = 'Nuevo Proveedor';
            btnSubmit.innerText = 'Guardar Proveedor';
            form.action = `{{ route('proveedores.store') }}`;
            method.value = 'POST';
            form.reset(); 
            document.getElementById('codigo_pais').value = '+591';
            document.getElementById('pais').value = 'Bolivia';
            updateCities();
        }

        checkFormValidity();
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('proveedorModal').classList.add('hidden');
    }

    function openDeleteModal(id, nombre) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        
        document.getElementById('deleteProvName').innerText = nombre;
        form.action = `/proveedores/${id}`;
        
        modal.classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
@endsection