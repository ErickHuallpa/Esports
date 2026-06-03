<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use Illuminate\Http\Request;

class CuponController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado', 'disponibles');
        $query = Cupon::latest();
        
        if ($estado === 'disponibles') {
            $query->where('usado', false);
        } elseif ($estado === 'usados') {
            $query->where('usado', true);
        }
        
        $cupones = $query->get();
        return view('admin.cupones.index', compact('cupones', 'estado'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:10|unique:cupones,codigo',
            'valor' => 'required|numeric|min:5|max:250|multiple_of:5',
            'monto_minimo' => 'required|numeric|min:0',
        ]);
        Cupon::create([
            'codigo' => strtoupper(trim($data['codigo'])),
            'tipo' => 'fijo',
            'valor' => $data['valor'],
            'monto_minimo' => $data['monto_minimo'],
            'usado' => false,
            'activo' => true,
        ]);
        return back()->with('success', 'Cupón creado correctamente.');
    }
    public function update(Request $request, $id)
    {
        $cupon = Cupon::findOrFail($id);
        $data = $request->validate([
            'codigo' => 'required|string|max:10|unique:cupones,codigo,' . $id,
            'valor' => 'required|numeric|min:5|max:250|multiple_of:5',
            'monto_minimo' => 'required|numeric|min:0',
            'activo' => 'boolean',
        ]);
        $cupon->update([
            'codigo' => strtoupper(trim($data['codigo'])),
            'valor' => $data['valor'],
            'monto_minimo' => $data['monto_minimo'],
            'activo' => $request->has('activo'),
        ]);
        return back()->with('success', 'Cupón actualizado correctamente.');
    }
    public function reactivar($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->update([
            'usado' => false,
            'usado_en' => null,
            'usado_por' => null,
        ]);
        return back()->with('success', 'Cupón restaurado. Ahora está disponible nuevamente.');
    }
    public function destroy($id)
    {
        Cupon::findOrFail($id)->delete();
        return back()->with('success', 'Cupón eliminado.');
    }
    public function validarCupon(Request $request)
    {
        $codigo = strtoupper(trim($request->codigo));
        $cupon = Cupon::where('codigo', $codigo)->first();
        if (!$cupon) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Cupón no encontrado.'
            ]);
        }
        if (!$cupon->activo) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Este cupón se encuentra inactivo.'
            ]);
        }
        if ($cupon->usado) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Este cupón ya fue utilizado.'
            ]);
        }
        return response()->json([
            'valido' => true,
            'mensaje' => '¡Cupón aplicado!',
            'cupon' => [
                'id' => $cupon->id,
                'tipo' => $cupon->tipo,
                'valor' => (float) $cupon->valor,
                'monto_minimo' => (float) $cupon->monto_minimo,
            ]
        ]);
    }
}