@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-end gap-4">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight">Estructura de Categorías</h1>
        <p class="text-[#343c4c]/70 text-sm mt-1 font-medium">Gestiona las agrupaciones del catálogo. Las categorías desactivadas ocultarán sus productos vinculados en el catálogo público.</p>
    </div>
    <button onclick="abrirCrearModal()" class="inline-flex justify-center items-center bg-[#0464a4] hover:bg-[#343c4c] text-white text-sm font-black uppercase tracking-wider py-3 px-6 rounded-xl shadow-lg transition-transform transform hover:-translate-y-1">
        ➕ Agregar Nueva Categoría
    </button>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($categorias as $cat)
        <div class="bg-white rounded-2xl p-6 shadow-md relative flex flex-col justify-between transition-all duration-300 hover:shadow-xl border-t-4 {{ $cat->activo ? 'border-[#dcb47c]' : 'border-[#dc043c] bg-red-50/20' }}">
            
            <div>
                <div class="flex justify-between items-start mb-3">
                    <h3 class="font-bold text-xl text-[#343c4c] line-clamp-1" id="nombre_txt_{{ $cat->id }}">{{ $cat->nombre }}</h3>
                    
                    @if($cat->activo)
                        <span class="px-3 py-1 bg-[#0464a4] text-white text-[10px] font-black rounded-md uppercase tracking-widest shadow-sm">Activo</span>
                    @else
                        <span class="px-3 py-1 bg-[#dc043c] text-white text-[10px] font-black rounded-md uppercase tracking-widest shadow-sm">Oculto</span>
                    @endif
                </div>
                
                <p class="text-sm text-[#343c4c]/60 line-clamp-2 min-h-[40px]" id="desc_txt_{{ $cat->id }}">{{ $cat->descripcion ?? 'Sin descripción detallada registrada.' }}</p>
                
                <div class="mt-5 bg-[#f4f4f4] border border-[#343c4c]/5 rounded-xl p-3 flex items-center justify-between">
                    <span class="text-[#343c4c]/70 font-bold uppercase tracking-wider text-[10px]">Artículos vinculados:</span>
                    <span class="font-black text-[#dcb47c] text-lg leading-none">{{ $cat->productos_count }}</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-[#f4f4f4] flex gap-3 justify-end">
                <form action="{{ route('admin.categorias.estado', $cat->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="text-xs font-black uppercase tracking-widest px-4 py-2 rounded-lg transition-colors {{ $cat->activo ? 'text-[#dc043c] bg-[#dc043c]/10 hover:bg-[#dc043c] hover:text-white' : 'text-[#0464a4] bg-[#0464a4]/10 hover:bg-[#0464a4] hover:text-white' }}">
                        {{ $cat->activo ? 'Ocultar' : 'Mostrar' }}
                    </button>
                </form>

                <button onclick="abrirEditarModal({{ $cat->id }}, '{{ addslashes($cat->nombre) }}', '{{ addslashes($cat->descripcion) }}')" class="text-xs font-black uppercase tracking-widest px-4 py-2 bg-[#f4f4f4] hover:bg-[#dcb47c] hover:text-[#343c4c] text-[#343c4c] rounded-lg transition-colors shadow-sm">
                    Editar
                </button>
            </div>
            
        </div>
    @empty
        <div class="col-span-full py-16 text-center bg-white rounded-2xl border-2 border-dashed border-[#dcb47c] shadow-sm">
            <svg class="w-16 h-16 mx-auto text-[#dcb47c]/50 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
            <h2 class="text-xl font-bold text-[#343c4c]">No existen categorías registradas en el sistema</h2>
            <p class="text-[#343c4c]/60 mt-1">Crea la primera categoría para empezar a organizar tu catálogo.</p>
        </div>
    @endforelse
</div>

<div id="crearModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border-t-4 border-[#0464a4]">
        <div class="px-6 py-5 border-b border-[#f4f4f4] flex justify-between items-center bg-white">
            <h3 class="font-black text-[#343c4c] text-lg uppercase tracking-wider">Nueva Categoría</h3>
            <button onclick="cerrarCrearModal()" class="text-[#343c4c]/40 hover:text-[#dc043c] bg-gray-100 hover:bg-red-50 rounded-full p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('admin.categorias.store') }}" method="POST" id="crearForm">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Nombre de la Categoría *</label>
                    <input type="text" name="nombre" id="crear_nombre" required maxlength="100" placeholder="Ej: Indumentaria Oficial" class="val-input-crear w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors">
                    <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_crear_nombre"></p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Descripción Breve</label>
                    <textarea name="descripcion" id="crear_descripcion" rows="3" maxlength="255" placeholder="Describe los artículos que agrupa esta categoría..." class="val-input-crear w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors"></textarea>
                    <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_crear_descripcion"></p>
                </div>
            </div>
            <div class="px-6 py-5 border-t border-[#f4f4f4] bg-white flex justify-end space-x-3">
                <button type="button" onclick="cerrarCrearModal()" class="px-5 py-2.5 text-xs font-bold text-[#343c4c] hover:bg-[#f4f4f4] rounded-xl transition-colors">Cancelar</button>
                <button type="submit" id="btnCrearSubmit" class="px-5 py-2.5 bg-[#0464a4] hover:bg-[#343c4c] text-white text-xs font-black uppercase tracking-wider rounded-xl transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<div id="editarModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-[#343c4c]/60 flex items-center justify-center transition-opacity">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border-t-4 border-[#dcb47c]">
        <div class="px-6 py-5 border-b border-[#f4f4f4] flex justify-between items-center bg-white">
            <h3 class="font-black text-[#343c4c] text-lg uppercase tracking-wider">Corregir Datos</h3>
            <button onclick="cerrarEditarModal()" class="text-[#343c4c]/40 hover:text-[#dc043c] bg-gray-100 hover:bg-red-50 rounded-full p-1.5 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form id="editarForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Nombre de la Categoría *</label>
                    <input type="text" id="edit_nombre" name="nombre" required maxlength="100" class="val-input-edit w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors">
                    <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_edit_nombre"></p>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1">Descripción Breve</label>
                    <textarea id="edit_descripcion" name="descripcion" rows="3" maxlength="255" class="val-input-edit w-full bg-[#f4f4f4] border-2 border-transparent rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] text-[#343c4c] transition-colors"></textarea>
                    <p class="text-xs text-red-600 mt-1 font-bold hidden" id="err_edit_descripcion"></p>
                </div>
            </div>
            <div class="px-6 py-5 border-t border-[#f4f4f4] bg-white flex justify-end space-x-3">
                <button type="button" onclick="cerrarEditarModal()" class="px-5 py-2.5 text-xs font-bold text-[#343c4c] hover:bg-[#f4f4f4] rounded-xl transition-colors">Cancelar</button>
                <button type="submit" id="btnEditSubmit" class="px-5 py-2.5 bg-[#0464a4] hover:bg-[#343c4c] text-white text-xs font-black uppercase tracking-wider rounded-xl transition-colors shadow-md disabled:opacity-50 disabled:cursor-not-allowed">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Validaciones
    function valNombre(v) {
        if(!v) return "El nombre es obligatorio.";
        const str = v.trim();
        if(str.length === 0) return "El nombre es obligatorio.";
        if(str.length < 3) return "Debe tener al menos 3 caracteres.";
        if(str.length > 25) return "Máximo 25 caracteres permitidos.";
        if(!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9\s\-_]+$/.test(str)) return "Contiene caracteres no permitidos (solo letras, números y guiones).";
        return null;
    }

    function valDescripcion(v) {
        if(!v) return null;
        const str = v.trim();
        if(str.length === 0) return null; // opcional
        if(str.length < 5) return "Si ingresas una descripción, debe tener mínimo 5 caracteres.";
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

    function checkCrearForm() {
        const nInput = document.getElementById('crear_nombre');
        const dInput = document.getElementById('crear_descripcion');
        if(!nInput || !dInput) return;

        const nErr = valNombre(nInput.value);
        const dErr = valDescripcion(dInput.value);
        
        setFieldError(nInput, document.getElementById('err_crear_nombre'), nErr);
        setFieldError(dInput, document.getElementById('err_crear_descripcion'), dErr);
        
        const btn = document.getElementById('btnCrearSubmit');
        if(btn) btn.disabled = (nErr !== null || dErr !== null || !nInput.value.trim());
    }

    function checkEditForm() {
        const nInput = document.getElementById('edit_nombre');
        const dInput = document.getElementById('edit_descripcion');
        if(!nInput || !dInput) return;

        const nErr = valNombre(nInput.value);
        const dErr = valDescripcion(dInput.value);
        
        setFieldError(nInput, document.getElementById('err_edit_nombre'), nErr);
        setFieldError(dInput, document.getElementById('err_edit_descripcion'), dErr);
        
        const btn = document.getElementById('btnEditSubmit');
        if(btn) btn.disabled = (nErr !== null || dErr !== null || !nInput.value.trim());
    }

    document.querySelectorAll('.val-input-crear').forEach(el => {
        el.addEventListener('input', checkCrearForm);
        el.addEventListener('change', checkCrearForm);
    });

    document.querySelectorAll('.val-input-edit').forEach(el => {
        el.addEventListener('input', checkEditForm);
        el.addEventListener('change', checkEditForm);
    });

    window.abrirCrearModal = function() {
        const form = document.getElementById('crearForm');
        if(form) form.reset();
        
        // Limpiar estados de validación
        document.querySelectorAll('.val-input-crear').forEach(el => {
            el.classList.remove('border-red-400', 'bg-red-50', 'border-green-400', 'bg-green-50');
            el.classList.add('border-transparent');
        });
        document.querySelectorAll('#err_crear_nombre, #err_crear_descripcion').forEach(el => el.classList.add('hidden'));
        
        const btn = document.getElementById('btnCrearSubmit');
        if(btn) btn.disabled = true;

        document.getElementById('crearModal').classList.remove('hidden');
    };
    
    window.cerrarCrearModal = function() {
        document.getElementById('crearModal').classList.add('hidden');
    };

    window.abrirEditarModal = function(id, nombre, descripcion) {
        const form = document.getElementById('editarForm');
        if(form) form.action = `/categorias/${id}`;
        
        const inputNombre = document.getElementById('edit_nombre');
        const inputDesc = document.getElementById('edit_descripcion');
        
        if(inputNombre) inputNombre.value = nombre;
        if(inputDesc) inputDesc.value = descripcion;
        
        // Ejecutar validación para mostrar colores iniciales
        checkEditForm();
        
        document.getElementById('editarModal').classList.remove('hidden');
    };
    
    window.cerrarEditarModal = function() {
        document.getElementById('editarModal').classList.add('hidden');
    };
</script>
@endsection