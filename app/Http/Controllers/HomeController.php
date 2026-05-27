<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. Obtenemos las categorías activas para llenar el Select del filtro
        $categorias = Categoria::where('activo', true)->orderBy('nombre', 'asc')->get();

        // 2. LÓGICA DEL SLIDER (Destacados)
        // Priorizamos los que tienen oferta activa, y luego los más nuevos.
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
                            ->orderByDesc('tiene_oferta') // Los que tienen oferta salen primero
                            ->orderBy('id', 'desc')       // Luego los más recientes (simulando más vendidos)
                            ->take(4)
                            ->get();

        // 3. LÓGICA DEL CATÁLOGO CON FILTROS DINÁMICOS
        $query = Producto::with(['categoria', 'variantes', 'resenas', 'ofertas' => function($q) {
                            $q->where('fecha_inicio', '<=', now())
                              ->where('fecha_fin', '>=', now())
                              ->where('activa', true);
                        }])->where('visible', true);

        // A. Filtro por Búsqueda de Texto (Buscador)
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre', 'ilike', $searchTerm) // ilike es ideal para PostgreSQL
                  ->orWhere('marca', 'ilike', $searchTerm);
            });
        }

        // B. Filtro por Categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // C. Ordenamiento (Sort)
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
            $query->orderBy('id', 'desc'); // Por defecto, los más recientes
        }

        $productos = $query->get();

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