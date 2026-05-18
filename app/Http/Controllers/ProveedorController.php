<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;

class ProveedorController extends Controller
{
    // Mostrar lista de proveedores
    public function index()
    {
        $proveedores = Proveedor::orderBy('id', 'desc')->get();
        return view('proveedores.index', compact('proveedores'));
    }

    // Registrar nuevo proveedor
    public function store(Request $request)
    {
        $request->validate([
            'nombre_empresa' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100|unique:proveedores,email',
            'contacto_nombre' => 'nullable|string|max:150',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:80',
        ]);

        Proveedor::create($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado exitosamente.');
    }

    // Actualizar datos del proveedor
    public function update(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $request->validate([
            'nombre_empresa' => 'required|string|max:150',
            'telefono' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:100|unique:proveedores,email,' . $proveedor->id,
            'contacto_nombre' => 'nullable|string|max:150',
            'direccion' => 'nullable|string',
            'ciudad' => 'nullable|string|max:100',
            'pais' => 'nullable|string|max:80',
        ]);

        $proveedor->update($request->all());

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado exitosamente.');
    }

    // Eliminar proveedor
    public function destroy($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado correctamente.');
    }
}