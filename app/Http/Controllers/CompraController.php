<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoVariante;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Orden;
use App\Models\Envio;
use App\Models\Inventario;
use App\Models\TipoPago;
use App\Models\Cupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CompraController extends Controller
{
    public function checkoutForm()
    {
        $cartItems = session()->get('carrito', []);
        if (count($cartItems) === 0) {
            return redirect('/')->with('error', 'El carrito se encuentra vacío.');
        }
        $tipoPagos = TipoPago::all();
        return view('cliente.checkout', compact('cartItems', 'tipoPagos'));
    }
    public function procesarCompra(Request $request)
    {
        $request->validate([
            'tipo_pago_id' => 'required|exists:tipo_pagos,id',
            'metodo_entrega' => 'required|in:tienda,delivery,envio',
            'direccion_delivery' => 'required_if:metodo_entrega,delivery|nullable|string',
            'zona_destino' => 'required_if:metodo_entrega,delivery|nullable|string|max:100',
            'ciudad_encomienda' => 'required_if:metodo_entrega,envio|nullable|string|max:100',
            'pago_envio' => 'required_if:metodo_entrega,envio|in:destino,pagado',
            'cupon_id' => 'nullable|exists:cupones,id',
            'descuento_aplicado' => 'nullable|numeric|min:0',
            'comprobante' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072'
        ], [
            'tipo_pago_id.required' => 'Debes seleccionar una forma de pago.',
            'direccion_delivery.required_if' => 'La dirección es obligatoria para el delivery.',
            'ciudad_encomienda.required_if' => 'La ciudad es obligatoria para enviar la encomienda.',
        ]);
        $cartItems = session()->get('carrito', []);
        if (count($cartItems) === 0) {
            return redirect('/')->with('error', 'Tu sesión de compra ha expirado.');
        }
        $tipoPago = TipoPago::findOrFail($request->tipo_pago_id);
        if ($tipoPago->nombre === 'QR' && !$request->hasFile('comprobante')) {
            return back()->withErrors(['comprobante' => 'Debes adjuntar la imagen del comprobante de transferencia bancaria QR.'])->withInput();
        }
        DB::beginTransaction();
        try {
            $subtotalArticulos = 0;
            foreach ($cartItems as $item) {
                $subtotalArticulos += $item['precio'] * $item['cantidad'];
            }
            $descuentoAplicado = 0;
            $cupon = null;
            if ($request->filled('cupon_id')) {
                $cupon = Cupon::lockForUpdate()->findOrFail($request->cupon_id);
                if ($cupon->usado) {
                    throw new \Exception('Este cupón ya fue utilizado.');
                }
                $descuentoAplicado = (float) $cupon->valor;
                if ($descuentoAplicado > $subtotalArticulos) {
                    $descuentoAplicado = $subtotalArticulos;
                }
                $cupon->update([
                    'usado' => true,
                    'usado_en' => now(),
                    'usado_por' => auth()->id(),
                ]);
            } else {
                $descuentoAplicado = floatval($request->descuento_aplicado ?? 0);
                if ($descuentoAplicado < 0) {
                    $descuentoAplicado = 0;
                }
                if ($descuentoAplicado > $subtotalArticulos) {
                    $descuentoAplicado = $subtotalArticulos;
                }
            }
            $subtotalConDescuento = $subtotalArticulos - $descuentoAplicado;
            $costoEnvio = 0.00;
            $ciudadFinal = 'Potosí';
            $direccionFinal = 'Recojo en tienda física E-SPORTS';
            $zonaFinal = null;
            $notaEnvio = '';
            if ($request->metodo_entrega === 'delivery') {
                $costoEnvio = 5.00;
                $ciudadFinal = 'Potosí (Área Urbana)';
                $direccionFinal = $request->direccion_delivery;
                $zonaFinal = $request->zona_destino;
            } elseif ($request->metodo_entrega === 'envio') {
                $ciudadFinal = $request->ciudad_encomienda;
                $direccionFinal = 'Recojo en Terminal / Agencia destino';
                if ($request->pago_envio === 'pagado') {
                    $costoEnvio = 25.00;
                    $notaEnvio = ' (El cliente ya pagó 25 Bs por el envío en su transferencia)';
                } else {
                    $costoEnvio = 0.00;
                    $notaEnvio = ' (Cobro del envío en Destino por parte de la empresa de transporte)';
                }
                $direccionFinal .= $notaEnvio;
            }
            $totalVenta = $subtotalConDescuento + $costoEnvio;
            $pagoData = [
                'tipo_pago_id' => $tipoPago->id,
                'user_id' => auth()->id(),
                'monto' => $totalVenta,
                'estado' => ($tipoPago->nombre === 'QR') ? 'pendiente' : 'verificado',
                'fecha_pago' => now(),
            ];
            if ($request->hasFile('comprobante')) {
                $pagoData['comprobante_url'] = $request->file('comprobante')->store('comprobantes/qr', 'public');
            }
            $pago = Pago::create($pagoData);
            $venta = Venta::create([
                'user_id' => auth()->id(),
                'pago_id' => $pago->id,
                'precio_total' => $totalVenta,
                'descuento_aplicado' => $descuentoAplicado,
                'estado_venta' => ($tipoPago->nombre === 'QR') ? 'pendiente' : 'confirmada',
                'fecha_venta' => now(),
            ]);
            foreach ($cartItems as $varianteId => $item) {
                $variante = ProductoVariante::with('producto')->findOrFail($varianteId);
                if ($variante->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: " . $variante->producto->nombre);
                }
                $stockAnterior = $variante->stock;
                $variante->decrement('stock', $item['cantidad']);
                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_variante_id' => $variante->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario_compra' => $variante->producto->precio_compra,
                    'precio_unitario_venta' => $item['precio'],
                    'descuento_unitario' => 0.00,
                    'subtotal' => $item['precio'] * $item['cantidad'],
                ]);
                Inventario::create([
                    'producto_variante_id' => $variante->id,
                    'user_id' => auth()->id(),
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_resultante' => $variante->stock,
                    'motivo' => 'Reserva automática por orden online Nro: ' . $venta->id,
                ]);
            }
            $orden = Orden::create([
                'venta_id' => $venta->id,
                'user_id' => auth()->id(),
                'estado_orden' => ($tipoPago->nombre === 'QR') ? 'Validando Pago' : 'Preparando',
                'ciudad_destino' => $ciudadFinal,
                'direccion_envio' => $direccionFinal,
            ]);
            if ($request->metodo_entrega !== 'tienda') {
                Envio::create([
                    'orden_id' => $orden->id,
                    'direccion_destino' => $direccionFinal,
                    'ciudad_destino' => $ciudadFinal,
                    'zona_destino' => $zonaFinal,
                    'estado_envio' => 'preparando',
                    'costo_envio' => $costoEnvio,
                ]);
            }
            DB::commit();
            session()->forget('carrito');
            Cache::forget('carrito_user_' . auth()->id());
            return redirect('/')->with('success', '¡Su orden ha sido registrada! Espere a que el personal corrobore su pago.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Fallo crítico en el checkout: ' . $e->getMessage());
        }
    }
    public function listaPagosPendientes()
    {
        $pagos = Pago::with(['user.persona', 'tipoPago', 'venta'])->where('estado', 'pendiente')->orderBy('id', 'asc')->get();
        return view('admin.pagos.index', compact('pagos'));
    }
    public function verificarPago(Request $request, $id)
    {
        $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
            'motivo_rechazo' => 'required_if:accion,rechazar|string|nullable',
            'observaciones' => 'nullable|string'
        ]);
        $pago = Pago::with('venta.detalles.variante')->findOrFail($id);
        if ($pago->user_id === auth()->id()) {
            return back()->with('error', 'Alerta de Seguridad: No tienes autorización para evaluar ni aprobar tus propias transacciones o comprobantes.');
        }
        DB::beginTransaction();
        try {
            if ($request->accion === 'aprobar') {
                $pago->update([
                    'estado' => 'verificado',
                    'verificado_por' => auth()->id(),
                    'fecha_verificacion' => now(),
                    'observaciones' => $request->observaciones,
                ]);
                $pago->venta->update(['estado_venta' => 'confirmada']);
                $pago->venta->orden->update(['estado_orden' => 'Preparando']);
                $msg = "El pago fue aprobado y la venta ha sido confirmada.";
            } else {
                foreach ($pago->venta->detalles as $detalle) {
                    $variante = $detalle->variante;
                    $stockAnterior = $variante->stock;
                    $variante->increment('stock', $detalle->cantidad);
                    Inventario::create([
                        'producto_variante_id' => $variante->id,
                        'user_id' => auth()->id(),
                        'tipo_movimiento' => 'ajuste',
                        'cantidad' => $detalle->cantidad,
                        'stock_anterior' => $stockAnterior,
                        'stock_resultante' => $variante->stock,
                        'motivo' => 'Reversión automática. Comprobante QR rechazado en la orden: ' . $pago->venta->id,
                    ]);
                }
                $pago->update([
                    'estado' => 'rechazado',
                    'motivo_rechazo' => $request->motivo_rechazo,
                    'verificado_por' => auth()->id(),
                    'fecha_verificacion' => now(),
                ]);
                $pago->venta->update(['estado_venta' => 'cancelada']);
                $pago->venta->orden->update(['estado_orden' => 'Rechazada por Pago Inválido']);
                $msg = "El comprobante fue rechazado. El stock ha sido restituido al almacén.";
            }
            DB::commit();
            return redirect()->route('admin.pagos.index')->with('success', $msg);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error operativo en caja: ' . $e->getMessage());
        }
    }
}