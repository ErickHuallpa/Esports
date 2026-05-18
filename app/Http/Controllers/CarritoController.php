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

        $variante = ProductoVariante::with('producto')->findOrFail($request->producto_variante_id);

        if ($variante->stock <= 0) {
            return back()->with('error', 'La combinación seleccionada ya no cuenta con stock disponible.');
        }

        $carrito = session()->get('carrito', []);
        $idSession = $variante->id;

        if (isset($carrito[$idSession])) {
            if ($carrito[$idSession]['cantidad'] + 1 > $variante->stock) {
                return back()->with('error', 'No puedes agregar más unidades de las disponibles en almacén.');
            }
            $carrito[$idSession]['cantidad']++;
        } else {
            $fotos = json_decode($variante->producto->imagen_url, true) ?? [];
            $portada = count($fotos) > 0 ? $fotos[0] : null;

            $carrito[$idSession] = [
                'producto_id' => $variante->producto_id,
                'nombre' => $variante->producto->nombre,
                'precio' => $variante->producto->precio_venta,
                'imagen_url' => $portada,
                'talla' => $variante->talla,
                'color' => $variante->color,
                'cantidad' => 1,
            ];
        }

        // Guardamos en la sesión actual
        session()->put('carrito', $carrito);

        // RESPALDO EN CACHÉ: Vinculamos el carrito al ID del usuario por 30 días
        Cache::put('carrito_user_' . auth()->id(), $carrito, now()->addDays(30));

        return back()->with('success', 'Producto añadido al carrito.')->with('open_cart', true);
    }

    public function eliminar(Request $request)
    {
        $carrito = session()->get('carrito', []);

        if (isset($carrito[$request->id])) {
            unset($carrito[$request->id]);
            session()->put('carrito', $carrito);
            
            // Actualizamos o limpiamos el respaldo en caché
            if (count($carrito) > 0) {
                Cache::put('carrito_user_' . auth()->id(), $carrito, now()->addDays(30));
            } else {
                Cache::forget('carrito_user_' . auth()->id());
            }
        }

        return back()->with('success', 'Artículo removido del carrito.')->with('open_cart', true);
    }
}