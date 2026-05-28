<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oferta;
use App\Models\Producto;

class OfertaController extends Controller
{
    public function index()
    {
        $ofertas = Oferta::with('producto')->orderBy('id', 'desc')->get();
        $productosDisponibles = Producto::whereDoesntHave('ofertas', function ($q) {
            $q->where('fecha_fin', '>=', now());
        })->where('visible', true)->get();
        return view('admin.ofertas.index', compact('ofertas', 'productosDisponibles'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'porcentaje_descuento' => 'required|numeric|min:1|max:99',
            'fecha_inicio' => 'required|date|before:fecha_fin',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);
        Oferta::create([
            'producto_id' => $request->producto_id,
            'porcentaje_descuento' => $request->porcentaje_descuento,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'activa' => true,
        ]);
        return redirect()->route('admin.ofertas.index')->with('success', 'Campaña de descuento creada exitosamente.');
    }
    public function destroy($id)
    {
        $oferta = Oferta::findOrFail($id);
        $oferta->delete();
        return redirect()->route('admin.ofertas.index')->with('success', 'La oferta ha sido dada de baja del catálogo.');
    }
    public function validarCupon(Request $request)
    {
        $codigo = strtoupper(trim($request->codigo));
                $cuponesValidos = [
            'ESPORTS10' => ['tipo' => 'porcentaje', 'valor' => 10],
            'BIENVENIDO50' => ['tipo' => 'fijo', 'valor' => 50],
        ];
        if (array_key_exists($codigo, $cuponesValidos)) {
            return response()->json([
                'valido' => true,
                'mensaje' => '¡Cupón Aplicado Exitosamente!',
                'cupon' => $cuponesValidos[$codigo]
            ]);
        }
        return response()->json([
            'valido' => false,
            'mensaje' => 'El código de descuento ingresado no es válido o ha expirado.'
        ]);
    }
}