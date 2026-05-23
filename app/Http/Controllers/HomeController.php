<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
        // Traemos las reseñas, variantes y las ofertas activas
        $productos = Producto::with(['categoria', 'variantes', 'resenas', 'ofertas' => function($q) {
                                // Filtramos para cargar solo las ofertas que están en fecha válida
                                $q->where('fecha_inicio', '<=', now())
                                  ->where('fecha_fin', '>=', now())
                                  ->where('activa', true);
                            }])
                            ->where('visible', true)
                            ->orderBy('id', 'desc')
                            ->get();

        return view('home', compact('productos'));
    }

    public function show($id)
    {
        $producto = Producto::with(['categoria', 'variantes', 'resenas.user.persona', 'ofertas' => function($q) {
                                $q->where('fecha_inicio', '<=', now())
                                  ->where('fecha_fin', '>=', now())
                                  ->where('activa', true);
                            }])->findOrFail($id);

        return view('producto.show', compact('producto'));
    }
}