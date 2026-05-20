<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoVariante;
use App\Models\Categoria;
use App\Models\TipoPago;
use App\Models\Pago;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Orden;
use App\Models\Inventario;
use App\Models\Persona;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PosController extends Controller
{
    public function index()
    {
        // Traemos las variantes con stock y las categorías para el filtro
        $variantes = ProductoVariante::with('producto.categoria')->where('stock', '>', 0)->get();
        $categorias = Categoria::where('activo', true)->get();
        $tipoPagos = TipoPago::all();

        return view('cajero.pos', compact('variantes', 'categorias', 'tipoPagos'));
    }

    public function buscarCliente(Request $request)
    {
        $persona = Persona::with('user')->where('ci', $request->ci)->first();
        
        if ($persona) {
            return response()->json([
                'encontrado' => true,
                'nombre' => $persona->nombre,
                'apellidos' => $persona->apellidos,
                'email' => $persona->user->email ?? ''
            ]);
        }
        
        return response()->json(['encontrado' => false]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ci' => 'required|string|max:20',
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'tipo_pago_id' => 'required|exists:tipo_pagos,id',
            'variante_id' => 'required|array|min:1',
            'cantidad' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            // 1. GESTIÓN RÁPIDA DEL CLIENTE
            $persona = Persona::where('ci', $request->ci)->first();
            
            if (!$persona) {
                if(User::where('email', $request->email)->exists()) {
                    return back()->with('error', 'El correo electrónico ya pertenece a otro usuario registrado.');
                }

                $persona = Persona::create([
                    'nombre' => $request->nombre,
                    'apellidos' => $request->apellidos,
                    'ci' => $request->ci,
                ]);

                $rolCliente = Rol::where('nombre', 'cliente')->first();
                
                $user = User::create([
                    'persona_id' => $persona->id,
                    'rol_id' => $rolCliente->id,
                    'email' => $request->email,
                    'username' => 'cliente_' . $request->ci,
                    'password' => Hash::make($request->ci), 
                    'activo' => true,
                ]);
            } else {
                $user = $persona->user;
            }

            // 2. CÁLCULO DE TOTALES Y VALIDACIÓN DE STOCK
            $totalVenta = 0;
            $itemsProcesar = [];

            foreach ($request->variante_id as $index => $varId) {
                $variante = ProductoVariante::with('producto')->findOrFail($varId);
                $cantPedida = $request->cantidad[$index];

                if ($variante->stock < $cantPedida) {
                    throw new \Exception("Stock insuficiente para: " . $variante->producto->nombre);
                }

                $subtotal = $variante->producto->precio_venta * $cantPedida;
                $totalVenta += $subtotal;

                $itemsProcesar[] = [
                    'variante' => $variante,
                    'cantidad' => $cantPedida,
                    'subtotal' => $subtotal
                ];
            }

            // 3. REGISTRO DEL PAGO
            $pago = Pago::create([
                'tipo_pago_id' => $request->tipo_pago_id,
                'user_id' => $user->id,
                'monto' => $totalVenta,
                'estado' => 'verificado', 
                'verificado_por' => auth()->id(),
                'fecha_pago' => now(),
                'fecha_verificacion' => now(),
                'observaciones' => 'Venta presencial en Mostrador (POS).',
            ]);

            // 4. REGISTRO DE LA VENTA
            $venta = Venta::create([
                'user_id' => $user->id,
                'pago_id' => $pago->id,
                'precio_total' => $totalVenta,
                'descuento_aplicado' => 0.00,
                'estado_venta' => 'confirmada',
                'fecha_venta' => now(),
            ]);

            // 5. DESCUENTO DE INVENTARIO
            foreach ($itemsProcesar as $item) {
                $var = $item['variante'];
                $stockAnterior = $var->stock;
                
                $var->decrement('stock', $item['cantidad']);

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_variante_id' => $var->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario_compra' => $var->producto->precio_compra,
                    'precio_unitario_venta' => $var->producto->precio_venta,
                    'descuento_unitario' => 0.00,
                    'subtotal' => $item['subtotal'],
                ]);

                Inventario::create([
                    'producto_variante_id' => $var->id,
                    'user_id' => auth()->id(),
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $item['cantidad'],
                    'stock_anterior' => $stockAnterior,
                    'stock_resultante' => $var->stock,
                    'motivo' => 'Venta Física Directa en Mostrador Nro: ' . $venta->id,
                ]);
            }

            // 6. ORDEN CERRADA
            Orden::create([
                'venta_id' => $venta->id,
                'user_id' => $user->id,
                'estado_orden' => 'Completada / Entregada',
                'ciudad_destino' => 'Potosí',
                'direccion_envio' => 'Entregado presencialmente en tienda',
            ]);

            DB::commit();
            return redirect()->route('cajero.pos.index')->with('success', '¡Venta presencial completada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error en la caja: ' . $e->getMessage());
        }
    }
}