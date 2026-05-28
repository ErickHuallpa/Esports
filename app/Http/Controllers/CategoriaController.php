<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')->orderBy('id', 'desc')->get();
        return view('admin.categorias.index', compact('categorias'));
    }
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
    public function toggleStatus($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->activo = !$categoria->activo;
        $categoria->save();
        $mensaje = $categoria->activo ? 'Categoría activada en el catálogo.' : 'Categoría oculta del catálogo público.';
        return redirect()->route('admin.categorias.index')->with('success', $mensaje);
    }
}