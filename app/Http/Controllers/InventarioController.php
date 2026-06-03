<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoVariante;
use App\Models\Inventario;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function index()
    {
        $variantes = ProductoVariante::with(['producto.categoria', 'producto.proveedor'])->get();
        $movimientos = Inventario::with(['variante.producto', 'user.persona'])
                                ->where('tipo_movimiento', 'entrada')
                                ->orderBy('id', 'desc')
                                ->take(25)
                                ->get();
        return view('personal.inventario.index', compact('variantes', 'movimientos'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'variante_id' => 'required|array|min:1',
            'cantidad' => 'required|array|min:1',
            'precio_compra' => 'required|array|min:1',
            'motivo_general' => 'nullable|string|max:255',
        ]);
        DB::beginTransaction();
        try {
            $motivo = $request->motivo_general ?? 'Reabastecimiento regular de mercadería';
            foreach ($request->variante_id as $index => $varId) {
                $variante = ProductoVariante::with('producto.proveedor')->findOrFail($varId);
                $cantIngreso = intval($request->cantidad[$index]);
                $nuevoPrecioCompra = floatval($request->precio_compra[$index]);
                if ($cantIngreso <= 0 || $nuevoPrecioCompra <= 0) {
                    throw new \Exception("Los valores de cantidad y costos ingresados deben ser positivos.");
                }
                if (!$variante->producto->proveedor_id) {
                    throw new \Exception("El producto '{$variante->producto->nombre}' carece de un proveedor base en el catálogo.");
                }
                $stockAnterior = $variante->stock;
                                $variante->increment('stock', $cantIngreso);
                $variante->producto->update([
                    'precio_compra' => $nuevoPrecioCompra
                ]);
                Inventario::create([
                    'producto_variante_id' => $variante->id,
                    'user_id' => auth()->id(),
                    'tipo_movimiento' => 'entrada',
                    'cantidad' => $cantIngreso,
                    'stock_anterior' => $stockAnterior,
                    'stock_resultante' => $variante->stock,
                    'motivo' => "Prov: " . $variante->producto->proveedor->nombre_empresa . " | " . $motivo . " | Costo Adquisición: Bs " . number_format($nuevoPrecioCompra, 2),
                ]);
            }
            DB::commit();
            return redirect()->route('personal.inventario.index')->with('success', 'Lote procesado. El stock e inversión fueron actualizados sin alterar los precios de venta.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Fallo operativo en almacén: ' . $e->getMessage());
        }
    }

    public function kardexApi(Request $request)
    {
        $offset = $request->query('offset', 0);
        $limit = 25;
        $fechaInicio = $request->query('fecha_inicio');
        $fechaFin = $request->query('fecha_fin');

        $query = Inventario::with(['variante.producto', 'user.persona'])
                           ->where('tipo_movimiento', 'entrada');

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);
        } elseif ($fechaInicio) {
            $query->where('created_at', '>=', $fechaInicio . ' 00:00:00');
        } elseif ($fechaFin) {
            $query->where('created_at', '<=', $fechaFin . ' 23:59:59');
        }

        $movimientos = $query->orderBy('id', 'desc')
                             ->skip($offset)
                             ->take($limit)
                             ->get();

        $data = $movimientos->map(function ($mov) {
            return [
                'producto_nombre' => $mov->variante->producto->nombre,
                'variante_info' => ($mov->variante->talla ?? 'N/A') . ' / ' . ($mov->variante->color ?? 'N/A'),
                'cantidad' => $mov->cantidad,
                'stock_anterior' => $mov->stock_anterior,
                'stock_resultante' => $mov->stock_resultante,
                'operador' => $mov->user->persona->nombre . ' ' . $mov->user->persona->apellidos,
                'motivo' => $mov->motivo,
                'fecha' => $mov->created_at->format('Y-m-d H:i')
            ];
        });

        return response()->json(['movimientos' => $data]);
    }
}