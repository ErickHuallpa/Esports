<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Envio;
use App\Models\Orden;
use Barryvdh\DomPDF\Facade\Pdf; // IMPORTAMOS LA LIBRERÍA DE PDF

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
    // NUEVO: DESCARGAR COMPROBANTE PDF
    // =======================================================
    public function descargarComprobante($id)
    {
        $venta = Venta::with(['pago.tipoPago', 'detalles.variante.producto', 'user.persona'])->findOrFail($id);

        // Seguridad: Solo el dueño de la compra puede descargar el comprobante
        if ($venta->user_id !== auth()->id()) {
            abort(403, 'No tienes permiso para descargar este comprobante.');
        }

        // Regla: Solo pagos verificados/aprobados pueden generar comprobante
        if ($venta->pago->estado !== 'verificado') {
            abort(403, 'El comprobante solo está disponible para pagos aprobados.');
        }

        // Cargar vista PDF
        $pdf = Pdf::loadView('cliente.comprobante_pdf', compact('venta'));
        
        // Descargar archivo
        return $pdf->download('Comprobante_Venta_'.$venta->id.'.pdf');
    }

    public function marcarComoRecibido($id)
    {
        $orden = Orden::where('id', $id)->whereHas('venta', function ($q) {
            $q->where('user_id', auth()->id());
        })->firstOrFail();

        $estado = strtolower($orden->estado_orden);
        if (str_contains($estado, 'listo') || str_contains($estado, 'llegó') || str_contains($estado, 'llego')) {
            $orden->estado_orden = 'Completada / Entregada';
            $orden->save();

            if ($orden->envio) {
                $orden->envio->estado_envio = 'entregado';
                $orden->envio->fecha_entrega_real = now();
                $orden->envio->save();
            }

            return redirect()->back()->with('success', '¡Gracias por confirmar la recepción de tu pedido!');
        }

        return redirect()->back()->with('error', 'El pedido aún no está listo para ser marcado como recibido.');
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

        $orden = Orden::with('envio')->findOrFail($id);

        $estadoActual = $orden->estado_orden;
        $nivelActual = 0;
        if(str_contains($estadoActual, 'Preparando') || str_contains($estadoActual, 'Listo')) $nivelActual = 1;
        elseif(str_contains($estadoActual, 'Tránsito')) $nivelActual = 2;
        elseif(str_contains($estadoActual, 'Llegó')) $nivelActual = 3;
        elseif(str_contains($estadoActual, 'Entregada') || str_contains($estadoActual, 'Completada')) $nivelActual = 4;

        $nivelNuevo = 0;
        switch ($request->estado_logistico) {
            case 'preparando': 
            case 'listo_tienda':
                $nivelNuevo = 1; break;
            case 'en_camino': 
                $nivelNuevo = 2; break;
            case 'llego_destino': 
                $nivelNuevo = 3; break;
            case 'entregado': 
                $nivelNuevo = 4; break;
            case 'fallido': 
                $nivelNuevo = 99; break;
        }

        if ($nivelNuevo < $nivelActual && $nivelNuevo !== 99 && !str_contains($estadoActual, 'Problema')) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => 'No se puede retroceder a un estado anterior.'], 400);
            }
            return redirect()->back()->with('error', 'No se puede retroceder a un estado anterior.');
        }

        $estadoOrden = '';
        $estadoEnvioDB = null; 

        switch ($request->estado_logistico) {
            case 'preparando': 
                $estadoOrden = 'Preparando Paquete'; 
                $estadoEnvioDB = 'preparando';
                break;
            case 'listo_tienda': 
                $estadoOrden = 'Listo para Recojo en Tienda'; 
                $estadoEnvioDB = 'preparando';
                break;
            case 'en_camino': 
                if ($orden->envio && $orden->envio->ciudad_destino === 'Potosí') {
                    $estadoOrden = 'En Tránsito a tu domicilio'; 
                } else {
                    $estadoOrden = 'En Tránsito a Destino'; 
                }
                $estadoEnvioDB = 'en camino';
                break;
            case 'llego_destino': 
                if ($orden->envio && $orden->envio->ciudad_destino === 'Potosí') {
                    $estadoOrden = 'Llegó a tu ubicación'; 
                } else {
                    $estadoOrden = 'Llegó al Destino / Agencia'; 
                }
                $estadoEnvioDB = 'en camino';
                break;
            case 'entregado': 
                $estadoOrden = 'Completada / Entregada'; 
                $estadoEnvioDB = 'entregado';
                break;
            case 'fallido': 
                $estadoOrden = 'Problema Logístico'; 
                $estadoEnvioDB = 'fallido';
                break;
            default:
                $estadoOrden = $request->estado_logistico;
        }

        $orden->update(['estado_orden' => $estadoOrden]);

        if ($orden->envio && $estadoEnvioDB) {
            $orden->envio->update([
                'estado_envio' => $estadoEnvioDB,
                'codigo_seguimiento' => $request->codigo_seguimiento,
                'responsable_entrega' => $request->responsable_entrega,
                'admin_asignado' => auth()->id(),
            ]);

            if ($request->estado_logistico === 'entregado') {
                $orden->envio->update(['fecha_entrega_real' => now()]);
            }
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Estado actualizado', 
                'nuevo_estado' => $estadoOrden,
                'nuevo_estado_envio' => $estadoEnvioDB
            ]);
        }

        return redirect()->back()->with('success', 'El estado logístico de la orden ha sido actualizado.');
    }
}