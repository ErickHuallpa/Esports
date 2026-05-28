<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Envio;

class PedidoController extends Controller
{
    public function misPedidos()
    {
        $ventas = Venta::with(['pago.tipoPago', 'detalles.variante.producto', 'orden.envio'])
                        ->where('user_id', auth()->id())
                        ->orderBy('id', 'desc')
                        ->get();
        return view('cliente.mis_pedidos', compact('ventas'));
    }
    public function controlEnvios()
    {
        $envios = Envio::with(['orden.venta.user.persona'])
                        ->whereHas('orden.venta', function ($q) {
                            $q->where('estado_venta', 'confirmada');
                        })
                        ->orderBy('id', 'desc')
                        ->get();
        return view('personal.envios.index', compact('envios'));
    }
    public function actualizarEstadoEnvio(Request $request, $id)
    {
        $request->validate([
            'estado_envio' => 'required|in:preparando,en camino,llego al destino,entregado,fallido',
            'codigo_seguimiento' => 'nullable|string|max:100',
            'responsable_entrega' => 'nullable|string|max:150',
            'fecha_entrega_estimada' => 'nullable|date',
        ]);
        $envio = Envio::findOrFail($id);
        $envio->update([
            'estado_envio' => $request->estado_envio,
            'codigo_seguimiento' => $request->codigo_seguimiento,
            'responsable_entrega' => $request->responsable_entrega,
            'fecha_entrega_estimada' => $request->fecha_entrega_estimada,
            'admin_asignado' => auth()->id(),
        ]);
        $estadoOrden = '';
        switch ($request->estado_envio) {
            case 'preparando': 
                $estadoOrden = 'Preparando Paquete'; 
                break;
            case 'en camino': 
                $estadoOrden = 'En Tránsito a Destino'; 
                break;
            case 'llego al destino': 
                $estadoOrden = 'Llegó al Destino / Agencia'; 
                break;
            case 'entregado': 
                $estadoOrden = 'Completada / Entregada'; 
                $envio->update(['fecha_entrega_real' => now()]);
                break;
            case 'fallido': 
                $estadoOrden = 'Problema Logístico'; 
                break;
        }
        $envio->orden->update(['estado_orden' => $estadoOrden]);
        return redirect()->back()->with('success', 'Estado del envío actualizado correctamente.');
    }
}