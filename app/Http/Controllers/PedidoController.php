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

        // Buscamos la Orden por su ID
        $orden = Orden::with('envio')->findOrFail($id);

        $estadoOrden = '';
        $estadoEnvioDB = null; // Variable para mapear a los valores válidos del Enum de BD

        switch ($request->estado_logistico) {
            case 'preparando': 
                $estadoOrden = 'Preparando Paquete'; 
                $estadoEnvioDB = 'preparando';
                break;
            case 'listo_tienda': 
                $estadoOrden = 'Listo para Recojo en Tienda'; 
                $estadoEnvioDB = 'preparando'; // Como no tiene envío, no importa mucho, pero por si acaso.
                break;
            case 'en_camino': 
                $estadoOrden = 'En Tránsito a Destino'; 
                $estadoEnvioDB = 'en camino'; // Mapeo exacto al Enum ['en camino']
                break;
            case 'llego_destino': 
                $estadoOrden = 'Llegó al Destino / Agencia'; 
                $estadoEnvioDB = 'en camino'; // Técnicamente sigue en camino hasta que lo entregan.
                break;
            case 'entregado': 
                $estadoOrden = 'Completada / Entregada'; 
                $estadoEnvioDB = 'entregado'; // Mapeo exacto al Enum ['entregado']
                break;
            case 'fallido': 
                $estadoOrden = 'Problema Logístico'; 
                $estadoEnvioDB = 'fallido'; // Mapeo exacto al Enum ['fallido']
                break;
            default:
                $estadoOrden = $request->estado_logistico;
        }

        // 1. Actualizamos el estado general de la Orden
        $orden->update(['estado_orden' => $estadoOrden]);

        // 2. Si la Orden REQUIERE envío, actualizamos los datos
        if ($orden->envio && $estadoEnvioDB) {
            $orden->envio->update([
                'estado_envio' => $estadoEnvioDB, // Pasamos el valor mapeado y correcto para la DB
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