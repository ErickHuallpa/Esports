@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Cupones</h1>
        <p class="text-gray-500 text-sm">Administra cupones de uso único.</p>
    </div>

    <button onclick="document.getElementById('crearModal').classList.remove('hidden')"
        class="inline-flex justify-center items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm transition">
        ➕ Nuevo Cupón
    </button>
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b text-gray-700 uppercase text-xs font-bold">
                <tr>
                    <th class="p-4">Código</th>
                    <th class="p-4 text-center">Valor</th>
                    <th class="p-4 text-center">Estado</th>
                    <th class="p-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y text-gray-600">
                @forelse($cupones as $cupon)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-bold text-gray-900">{{ $cupon->codigo }}</td>
                        <td class="p-4 text-center font-black text-green-600">Bs {{ number_format($cupon->valor, 2) }}</td>
                        <td class="p-4 text-center">
                            @if($cupon->usado)
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded-full uppercase">Usado</span>
                            @else
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-[10px] font-bold rounded-full uppercase">Disponible</span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <form action="{{ route('admin.cupones.destroy', $cupon->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    onclick="return confirm('¿Eliminar este cupón?')"
                                    class="text-red-500 hover:text-red-700 bg-red-50 px-3 py-1.5 rounded-lg text-xs font-bold transition">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">No existen cupones registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="crearModal" class="fixed inset-0 z-50 hidden backdrop-blur-sm bg-gray-900/30 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 overflow-hidden border">
        <div class="px-6 py-4 border-b flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800 text-base">Crear Cupón</h3>
            <button onclick="document.getElementById('crearModal').classList.add('hidden')" class="text-gray-400 text-2xl font-bold">&times;</button>
        </div>

        <form action="{{ route('admin.cupones.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Código *</label>
                    <input type="text" name="codigo" placeholder="Ej: DESCUENTO10"
                        class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500 uppercase" required>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Valor en Bs *</label>
                    <input type="number" name="valor" step="0.01" min="0.01" placeholder="Ej: 10"
                        class="w-full rounded-lg border-gray-300 p-2 text-sm focus:ring-blue-500" required>
                </div>

                <p class="text-xs text-gray-500">
                    Este cupón será de uso único y descontará ese monto fijo.
                </p>
            </div>

            <div class="px-6 py-4 border-t bg-gray-50 flex justify-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm">
                    Guardar Cupón
                </button>
            </div>
        </form>
    </div>
</div>
@endsection