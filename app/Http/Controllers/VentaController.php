<?php

namespace App\Http\Controllers;

use App\Models\Venta;

class VentaController extends Controller
{
    public function index()
    {
        // Solo mostramos ventas confirmadas
        $ventas = Venta::with(['user.persona', 'pago.tipoPago', 'detalles.variante.producto', 'orden'])
                        ->where('estado_venta', 'confirmada')
                        ->orderBy('id', 'desc')
                        ->get();

        return view('cajero.ventas_confirmadas', compact('ventas'));
    }
}