<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\ProductoVariante;
use App\Models\Inventario;
use App\Models\Categoria;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    public function index()
    {
        // Carga ansiosa para evitar el problema de consultas N+1
        $productos = Producto::with(['categoria', 'proveedor', 'variantes'])->orderBy('id', 'desc')->get();
        $categorias = Categoria::where('activo', true)->get();
        $proveedores = Proveedor::where('activo', true)->get();

        return view('productos.index', compact('productos', 'categorias', 'proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'nombre' => 'required|string|max:150',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', 
            'modelo_3d' => 'nullable|file|max:10240',
            'variante_talla.*' => 'nullable|string|max:50',
            'variante_color.*' => 'nullable|string|max:50',
            'variante_stock.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except(['imagenes', 'modelo_3d', 'variante_talla', 'variante_color', 'variante_stock']);

            // Procesar múltiples imágenes guardándolas en un array JSON
            if ($request->hasFile('imagenes')) {
                $rutasImagenes = [];
                foreach ($request->file('imagenes') as $foto) {
                    $rutasImagenes[] = $foto->store('productos/imagenes', 'public');
                }
                $data['imagen_url'] = json_encode($rutasImagenes);
            }

            // Procesar modelo 3D
            if ($request->hasFile('modelo_3d')) {
                $data['modelo_3d_url'] = $request->file('modelo_3d')->store('productos/modelos', 'public');
            }

            // 1. Guardar Producto General
            $producto = Producto::create($data);

            // 2. Guardar Variantes y registrar Auditoría en Inventarios
            if ($request->has('variante_stock')) {
                foreach ($request->variante_stock as $index => $stockInicial) {
                    $variante = ProductoVariante::create([
                        'producto_id' => $producto->id,
                        'talla' => $request->variante_talla[$index] ?? null,
                        'color' => $request->variante_color[$index] ?? null,
                        'stock' => $stockInicial,
                    ]);

                    // REGISTRO OBLIGATORIO EN INVENTARIOS (Kardex)
                    Inventario::create([
                        'producto_variante_id' => $variante->id,
                        'user_id' => auth()->id(),
                        'tipo_movimiento' => 'entrada',
                        'cantidad' => $stockInicial,
                        'stock_anterior' => 0,
                        'stock_resultante' => $stockInicial,
                        'motivo' => 'Carga inicial en el registro del producto.',
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('productos.index')->with('success', 'Producto e inventario inicial registrados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'nombre' => 'required|string|max:150',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'modelo_3d' => 'nullable|file|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except(['imagenes', 'modelo_3d']);

            if ($request->hasFile('imagenes')) {
                if ($producto->imagen_url) {
                    $viejasFotos = json_decode($producto->imagen_url, true) ?? [];
                    foreach ($viejasFotos as $vieja) {
                        Storage::disk('public')->delete($vieja);
                    }
                }
                $rutasImagenes = [];
                foreach ($request->file('imagenes') as $foto) {
                    $rutasImagenes[] = $foto->store('productos/imagenes', 'public');
                }
                $data['imagen_url'] = json_encode($rutasImagenes);
            }

            if ($request->hasFile('modelo_3d')) {
                if ($producto->modelo_3d_url) Storage::disk('public')->delete($producto->modelo_3d_url);
                $data['modelo_3d_url'] = $request->file('modelo_3d')->store('productos/modelos', 'public');
            }

            $producto->update($data);

            DB::commit();
            return redirect()->route('productos.index')->with('success', 'Datos del catálogo actualizados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->imagen_url) {
            $fotos = json_decode($producto->imagen_url, true) ?? [];
            foreach ($fotos as $foto) {
                Storage::disk('public')->delete($foto);
            }
        }
        if ($producto->modelo_3d_url) Storage::disk('public')->delete($producto->modelo_3d_url);

        $producto->delete();

        return redirect()->route('productos.index')->with('success', 'Producto eliminado permanentemente.');
    }

    // =======================================================
    // MÉTODO AGREGADO: CREACIÓN RÁPIDA DE CATEGORÍAS
    // =======================================================
    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string',
        ]);

        Categoria::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo' => true
        ]);

        return redirect()->route('productos.index')->with('success', 'Categoría agregada exitosamente al catálogo.');
    }
}