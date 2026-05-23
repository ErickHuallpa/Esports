<?php

namespace App\Http\Controllers;

use App\Models\Cupon;
use Illuminate\Http\Request;

class CuponController extends Controller
{
    public function index()
    {
        $cupones = Cupon::latest()->get();
        return view('admin.cupones.index', compact('cupones'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo' => 'required|string|max:50|unique:cupones,codigo',
            'valor' => 'required|numeric|min:0.01',
        ]);

        Cupon::create([
            'codigo' => strtoupper(trim($data['codigo'])),
            'tipo' => 'fijo',
            'valor' => $data['valor'],
            'usado' => false,
        ]);

        return back()->with('success', 'Cupón creado correctamente.');
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
            ]
        ]);
    }
}