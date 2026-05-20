<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
        // Traemos las reseñas (ratings) para calcular el promedio de estrellas en el Home
        $productos = Producto::with(['categoria', 'variantes', 'resenas'])
                            ->where('visible', true)
                            ->orderBy('id', 'desc')
                            ->get();

        return view('home', compact('productos'));
    }

    public function show($id)
    {
        // Traemos el producto con sus reseñas y la info del usuario que escribió cada reseña
        $producto = Producto::with(['categoria', 'variantes', 'resenas.user.persona'])->findOrFail($id);

        return view('producto.show', compact('producto'));
    }
}