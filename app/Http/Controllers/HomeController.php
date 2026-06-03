<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::where('activo', true)->orderBy('nombre', 'asc')->get();
        
        $destacados = Producto::with(['categoria', 'variantes', 'ofertas' => function($q) {
                                $q->where('fecha_inicio', '<=', now())
                                  ->where('fecha_fin', '>=', now())
                                  ->where('activa', true);
                            }])
                            ->where('visible', true)
                            ->withCount(['ofertas as tiene_oferta' => function($q) {
                                $q->where('fecha_inicio', '<=', now())
                                  ->where('fecha_fin', '>=', now())
                                  ->where('activa', true);
                            }])
                            ->orderByDesc('tiene_oferta')
                            ->orderBy('id', 'desc')
                            ->take(4)
                            ->get();

        $query = Producto::with(['categoria', 'variantes', 'resenas', 'ofertas' => function($q) {
                            $q->where('fecha_inicio', '<=', now())
                              ->where('fecha_fin', '>=', now())
                              ->where('activa', true);
                        }])->where('visible', true);

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre', 'ilike', $searchTerm)
                  ->orWhere('marca', 'ilike', $searchTerm);
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->filled('filtro') && $request->filtro === 'ofertas') {
            $query->whereHas('ofertas', function($q) {
                $q->where('fecha_inicio', '<=', now())
                  ->where('fecha_fin', '>=', now())
                  ->where('activa', true);
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'precio_asc':
                    $query->orderBy('precio_venta', 'asc');
                    break;
                case 'precio_desc':
                    $query->orderBy('precio_venta', 'desc');
                    break;
                case 'nuevos':
                    $query->orderBy('id', 'desc');
                    break;
                case 'antiguos':
                    $query->orderBy('id', 'asc');
                    break;
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // CAMBIO AQUÍ: Paginar de 25 en 25 en lugar de get()
        $productos = $query->paginate(25);

        return view('home', compact('productos', 'destacados', 'categorias'));
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