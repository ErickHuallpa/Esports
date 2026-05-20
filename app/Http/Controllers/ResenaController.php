<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resena;

class ResenaController extends Controller
{
    // =======================================================
    // NUEVO MÉTODO: Mostrar el historial del cliente
    // =======================================================
    public function misResenas()
    {
        $resenas = Resena::with(['producto.categoria'])
                        ->where('user_id', auth()->id())
                        ->orderBy('id', 'desc')
                        ->get();

        return view('cliente.mis_resenas', compact('resenas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        try {
            Resena::create([
                'user_id' => auth()->id(),
                'producto_id' => $request->producto_id,
                'calificacion' => $request->calificacion,
                'comentario' => $request->comentario,
            ]);

            return back()->with('success', '¡Gracias por compartir tu opinión!');
            
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23505') {
                return back()->with('error', 'Ya has publicado una reseña para este artículo anteriormente.');
            }
            return back()->with('error', 'Ocurrió un error al guardar tu opinión.');
        }
    }

    public function update(Request $request, $id)
    {
        $resena = Resena::findOrFail($id);

        // Seguridad: Verificar que la reseña pertenezca al usuario logueado
        if ($resena->user_id !== auth()->id()) {
            return back()->with('error', 'Acción no permitida.');
        }

        $request->validate([
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $resena->update([
            'calificacion' => $request->calificacion,
            'comentario' => $request->comentario,
        ]);

        return back()->with('success', 'Tu reseña ha sido actualizada correctamente.');
    }

    public function destroy($id)
    {
        $resena = Resena::findOrFail($id);

        if ($resena->user_id !== auth()->id()) {
            return back()->with('error', 'Acción no permitida.');
        }

        $resena->delete();

        return back()->with('success', 'Tu reseña ha sido eliminada del producto.');
    }
}