<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductoVariante;
use Illuminate\Support\Facades\Cache;

class CarritoController extends Controller
{
    public function agregar(Request $request)
    {
        $request->validate([
            'producto_variante_id' => 'required|exists:producto_variantes,id',
        ]);

        // Cargamos producto y sus ofertas válidas
        $variante = ProductoVariante::with(['producto.ofertas' => function($q) {
            $q->where('fecha_inicio', '<=', now())
              ->where('fecha_fin', '>=', now())
              ->where('activa', true);
        }])->findOrFail($request->producto_variante_id);

        if ($variante->stock <= 0) {
            return back()->with('error', 'La combinación seleccionada ya no cuenta con stock disponible.');
        }

        $carrito = session()->get('carrito', []);
        $idSession = $variante->id;

        // CÁLCULO DINÁMICO DE OFERTA
        $ofertaActiva = $variante->producto->ofertas->first();
        $precioFinal = $variante->producto->precio_venta;

        if ($ofertaActiva) {
            $descuento = $precioFinal * ($ofertaActiva->porcentaje_descuento / 100);
            $precioFinal = $precioFinal - $descuento;
        }

        if (isset($carrito[$idSession])) {
            if ($carrito[$idSession]['cantidad'] + 1 > $variante->stock) {
                return back()->with('error', 'No puedes agregar más unidades de las disponibles en almacén.');
            }
            $carrito[$idSession]['cantidad']++;
            // Actualizamos precio en caso de que la oferta haya cambiado
            $carrito[$idSession]['precio'] = $precioFinal;
        } else {
            $fotos = json_decode($variante->producto->imagen_url, true) ?? [];
            $portada = count($fotos) > 0 ? $fotos[0] : null;

            $carrito[$idSession] = [
                'producto_id' => $variante->producto_id,
                'nombre' => $variante->producto->nombre,
                'precio' => $precioFinal, // <--- GUARDAMOS EL PRECIO REBAJADO
                'imagen_url' => $portada,
                'talla' => $variante->talla,
                'color' => $variante->color,
                'cantidad' => 1,
            ];
        }

        session()->put('carrito', $carrito);
        Cache::put('carrito_user_' . auth()->id(), $carrito, now()->addDays(30));

        return back()->with('success', 'Producto añadido al carrito.')->with('open_cart', true);
    }

    public function eliminar(Request $request)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$request->id])) {
            unset($carrito[$request->id]);
            session()->put('carrito', $carrito);
            
            if (count($carrito) > 0) {
                Cache::put('carrito_user_' . auth()->id(), $carrito, now()->addDays(30));
            } else {
                Cache::forget('carrito_user_' . auth()->id());
            }
        }

        return back()->with('success', 'Artículo removido del carrito.')->with('open_cart', true);
    }
}