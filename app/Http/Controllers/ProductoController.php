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
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240', 
            'modelo_3d' => 'nullable|file|max:20480',
            // AÑADIDO 'webm' A LA LISTA DE EXTENSIONES PERMITIDAS
            'video' => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:51200', 
            'variante_talla.*' => 'nullable|string|max:50',
            'variante_color.*' => 'nullable|string|max:50',
            'variante_stock.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Excluimos campos no directos de la BD
            $data = $request->except(['imagenes', 'modelo_3d', 'video', 'variante_talla', 'variante_color', 'variante_stock']);
            
            if ($request->hasFile('imagenes')) {
                $rutasImagenes = [];
                foreach ($request->file('imagenes') as $foto) {
                    $rutasImagenes[] = $foto->store('productos/imagenes', 'public');
                }
                $data['imagen_url'] = json_encode($rutasImagenes);
            }
            
            if ($request->hasFile('modelo_3d')) {
                $data['modelo_3d_url'] = $request->file('modelo_3d')->store('productos/modelos', 'public');
            }

            if ($request->hasFile('video')) {
                $data['video_url'] = $request->file('video')->store('productos/videos', 'public');
            }
            
            $producto = Producto::create($data);
            
            if ($request->has('variante_stock')) {
                foreach ($request->variante_stock as $index => $stockInicial) {
                    $variante = ProductoVariante::create([
                        'producto_id' => $producto->id,
                        'talla' => $request->variante_talla[$index] ?? null,
                        'color' => $request->variante_color[$index] ?? null,
                        'stock' => $stockInicial,
                    ]);
                    
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
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240', 
            'modelo_3d' => 'nullable|file|max:20480', 
            // AÑADIDO 'webm' A LA LISTA DE EXTENSIONES PERMITIDAS
            'video' => 'nullable|mimes:mp4,mov,ogg,qt,webm|max:51200', 
            'variante_talla.*' => 'nullable|string|max:50',
            'variante_color.*' => 'nullable|string|max:50',
            'variante_stock.*' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $data = $request->except(['imagenes', 'modelo_3d', 'video', 'imagenes_a_eliminar', 'eliminar_video', 'variante_id', 'variante_talla', 'variante_color', 'variante_stock']);
            
            $fotosActuales = json_decode($producto->imagen_url, true) ?? [];

            // 1. Eliminar imágenes
            if ($request->has('imagenes_a_eliminar')) {
                foreach ($request->imagenes_a_eliminar as $imgDel) {
                    Storage::disk('public')->delete($imgDel);
                    if (($key = array_search($imgDel, $fotosActuales)) !== false) {
                        unset($fotosActuales[$key]);
                    }
                }
                $fotosActuales = array_values($fotosActuales);
            }

            // 2. Agregar nuevas imágenes
            if ($request->hasFile('imagenes')) {
                foreach ($request->file('imagenes') as $foto) {
                    $fotosActuales[] = $foto->store('productos/imagenes', 'public');
                }
            }
            $data['imagen_url'] = json_encode($fotosActuales);

            // 3. Modelo 3D
            if ($request->hasFile('modelo_3d')) {
                if ($producto->modelo_3d_url) Storage::disk('public')->delete($producto->modelo_3d_url);
                $data['modelo_3d_url'] = $request->file('modelo_3d')->store('productos/modelos', 'public');
            }

            // 4. Video (Eliminación manual o reemplazo)
            if ($request->eliminar_video == '1') {
                if ($producto->video_url) Storage::disk('public')->delete($producto->video_url);
                $data['video_url'] = null;
            }

            if ($request->hasFile('video')) {
                if ($producto->video_url) Storage::disk('public')->delete($producto->video_url);
                $data['video_url'] = $request->file('video')->store('productos/videos', 'public');
            }

            $producto->update($data);

            // 5. Variantes
            if ($request->has('variante_stock')) {
                foreach ($request->variante_stock as $index => $stock) {
                    $vId = $request->variante_id[$index] ?? null;
                    if ($vId) {
                        $variante = ProductoVariante::find($vId);
                        if($variante) {
                            $variante->update([
                                'talla' => $request->variante_talla[$index] ?? null,
                                'color' => $request->variante_color[$index] ?? null,
                                'stock' => $stock
                            ]);
                        }
                    } else {
                        ProductoVariante::create([
                            'producto_id' => $producto->id,
                            'talla' => $request->variante_talla[$index] ?? null,
                            'color' => $request->variante_color[$index] ?? null,
                            'stock' => $stock
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('productos.index')->with('success', 'Catálogo actualizado exitosamente.');
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
        if ($producto->video_url) Storage::disk('public')->delete($producto->video_url);
        
        $producto->delete();
        return redirect()->route('productos.index')->with('success', 'Producto eliminado permanentemente.');
    }

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
        return redirect()->route('productos.index')->with('success', 'Categoría agregada exitosamente.');
    }
}