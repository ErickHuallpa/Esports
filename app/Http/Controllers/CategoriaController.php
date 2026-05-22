<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    // Mostrar todas las categorías en el panel administrativo
    public function index()
    {
        $categorias = Categoria::withCount('productos')->orderBy('id', 'desc')->get();
        return view('admin.categorias.index', compact('categorias'));
    }

    // Almacenar una categoría desde el panel independiente
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:255',
        ], [
            'nombre.unique' => 'Esa categoría ya se encuentra registrada en el sistema.',
        ]);

        Categoria::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => true,
        ]);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    // Actualizar los datos de la categoría (Errores tipográficos)
    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $id,
            'descripcion' => 'nullable|string|max:255',
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('admin.categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    // Alternar estado operativo (Desactivar/Activar de forma lógica)
    public function toggleStatus($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->activo = !$categoria->activo;
        $categoria->save();

        $mensaje = $categoria->activo ? 'Categoría activada en el catálogo.' : 'Categoría oculta del catálogo público.';
        return redirect()->route('admin.categorias.index')->with('success', $mensaje);
    }
}