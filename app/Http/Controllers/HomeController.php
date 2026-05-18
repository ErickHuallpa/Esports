<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;

class HomeController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria', 'variantes'])
                            ->where('visible', true)
                            ->orderBy('id', 'desc')
                            ->get();

        return view('home', compact('productos'));
    }

    public function show($id)
    {
        // Traemos el producto junto con todas sus variantes de stock registradas
        $producto = Producto::with(['categoria', 'variantes'])->findOrFail($id);

        return view('producto.show', compact('producto'));
    }
}