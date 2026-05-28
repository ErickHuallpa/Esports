@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between sm:items-center gap-4 max-w-7xl mx-auto">
    <div>
        <h1 class="text-3xl font-black text-[#343c4c] tracking-tight uppercase">Gestión de Cupones</h1>
        <p class="text-gray-500 text-sm mt-1">Administra los códigos promocionales de uso único para el checkout.</p>
    </div>

    <button onclick="document.getElementById('crearModal').classList.remove('hidden')"
        class="inline-flex justify-center items-center bg-[#0464a4] hover:bg-[#343c4c] text-white text-sm font-black uppercase tracking-wider py-3 px-6 rounded-xl shadow-md transition-all transform hover:-translate-y-0.5">
        ➕ Nuevo Cupón
    </button>
</div>

<div class="bg-white rounded-2xl shadow-xl max-w-7xl mx-auto overflow-hidden border border-[#343c4c]/10 z-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#343c4c] text-white border-b-4 border-[#dcb47c] uppercase text-xs font-black tracking-widest">
                <tr>
                    <th class="p-4">Código Promocional</th>
                    <th class="p-4 text-center">Valor de Descuento</th>
                    <th class="p-4 text-center">Estado de Uso</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                @forelse($cupones as $cupon)
                    <tr class="hover:bg-[#f4f4f4]/50 transition-colors">
                        <td class="p-4 font-black text-lg text-[#343c4c] tracking-wide uppercase">{{ $cupon->codigo }}</td>
                        <td class="p-4 text-center font-black text-xl text-[#0464a4]">Bs {{ number_format($cupon->valor, 2) }}</td>
                        <td class="p-4 text-center">
                            @if($cupon->usado)
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
                            <form action="{{ route('admin.cupones.destroy', $cupon->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('¿Estás seguro de que deseas eliminar este cupón de forma permanente? No se podrá reutilizar.')"
                                    class="text-[#dc043c] bg-[#dc043c]/10 hover:bg-[#dc043c] hover:text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-sm">
                                    Eliminar
                                </button>
                            </form>
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
            <h3 class="font-black uppercase tracking-wider text-sm flex items-center">
                🎟️ Registrar Nuevo Cupón
            </h3>
            <button onclick="document.getElementById('crearModal').classList.add('hidden')" class="text-white hover:text-[#dc043c] text-2xl font-bold transition-colors">&times;</button>
        </div>

        <form action="{{ route('admin.cupones.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Código del Cupón *</label>
                    <input type="text" name="codigo" placeholder="Ej: MILAN2026"
                        class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-bold text-[#343c4c] uppercase tracking-wider" required>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-[#343c4c] uppercase tracking-widest mb-1.5">Monto de Descuento (Bs) *</label>
                    <input type="number" name="valor" step="0.01" min="0.01" placeholder="Ej: 15.00"
                        class="w-full bg-[#f4f4f4] border-none rounded-xl p-3 text-sm focus:ring-2 focus:ring-[#0464a4] font-black text-[#343c4c]" required>
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
                <button type="submit" class="px-5 py-2.5 bg-[#0464a4] hover:bg-[#343c4c] text-white font-black uppercase tracking-wider rounded-xl shadow-md transition-colors text-xs">
                    Guardar Cupón
                </button>
            </div>
        </form>
    </div>
</div>
@endsection