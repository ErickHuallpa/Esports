<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Envio;
use App\Models\Orden;

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

    // =======================================================
    // LOGÍSTICA: CONTROL DE ÓRDENES (Envíos y Recojos)
    // =======================================================
    public function controlEnvios()
    {
        // Traemos TODAS las Órdenes confirmadas, tengan o no un "Envío" asociado.
        $ordenes = Orden::with(['venta.user.persona', 'envio'])
                        ->whereHas('venta', function ($q) {
                            $q->where('estado_venta', 'confirmada');
                        })
                        ->orderBy('id', 'desc')
                        ->get();
                        
        return view('personal.envios.index', compact('ordenes'));
    }

    public function actualizarEstadoEnvio(Request $request, $id)
    {
        $request->validate([
            'estado_logistico' => 'required|string',
            'codigo_seguimiento' => 'nullable|string|max:100',
            'responsable_entrega' => 'nullable|string|max:150',
        ]);

        // Buscamos la Orden por su ID (Ya no el Envio directo)
        $orden = Orden::with('envio')->findOrFail($id);

        $estadoOrden = '';
        switch ($request->estado_logistico) {
            case 'preparando': 
                $estadoOrden = 'Preparando Paquete'; 
                break;
            case 'listo_tienda': 
                $estadoOrden = 'Listo para Recojo en Tienda'; 
                break;
            case 'en_camino': 
                $estadoOrden = 'En Tránsito a Destino'; 
                break;
            case 'llego_destino': 
                $estadoOrden = 'Llegó al Destino / Agencia'; 
                break;
            case 'entregado': 
                $estadoOrden = 'Completada / Entregada'; 
                break;
            case 'fallido': 
                $estadoOrden = 'Problema Logístico'; 
                break;
            default:
                $estadoOrden = $request->estado_logistico;
        }

        // 1. Actualizamos el estado general de la Orden
        $orden->update(['estado_orden' => $estadoOrden]);

        // 2. Si la Orden REQUIERE envío, actualizamos los datos de la empresa de transporte
        if ($orden->envio) {
            $orden->envio->update([
                'estado_envio' => $request->estado_logistico,
                'codigo_seguimiento' => $request->codigo_seguimiento,
                'responsable_entrega' => $request->responsable_entrega,
                'admin_asignado' => auth()->id(),
            ]);

            // Si se marcó como entregado, sellar la fecha real
            if ($request->estado_logistico === 'entregado') {
                $orden->envio->update(['fecha_entrega_real' => now()]);
            }
        }

        return redirect()->back()->with('success', 'El estado logístico de la orden ha sido actualizado.');
    }
}