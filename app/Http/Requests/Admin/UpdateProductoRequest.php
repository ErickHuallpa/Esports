<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\NombrePersona;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Obtener el ID del producto de la ruta
        $productoId = $this->route('id') ?? $this->route('producto');

        return [
            'categoria_id' => 'required|exists:categorias,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'nombre' => ['required', 'string', 'min:3', 'max:150', 'unique:productos,nombre,' . $productoId, 'regex:/^[\p{L}\p{N}\s\-\._\(\)\/]+$/u', 'regex:/[\p{L}\p{N}]/u'],
            'precio_compra' => 'required|numeric|gt:0|max:100000',
            'precio_venta' => 'required|numeric|gt:0|gte:precio_compra|max:100000',
            'imagenes.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:10240',
            'modelo_3d' => 'nullable|file|max:20480',
            'video' => 'nullable|file|mimes:mp4,avi,mov,webm,ogg,mkv,wmv,flv,3gp,qt',
            'variante_talla.*' => 'nullable|string|max:50',
            'variante_color.*' => 'nullable|string|max:50',
            'variante_stock.*' => 'required|integer|min:0',
            'marca' => ['nullable', 'string', 'max:100', 'regex:/^[\p{L}\p{N}\s\-\._\(\)\/]+$/u', 'regex:/[\p{L}\p{N}]/u'],
            'descripcion' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.min' => 'El nombre del producto debe tener al menos 3 caracteres.',
            'nombre.unique' => 'Ya existe un producto registrado con este nombre.',
            'precio_compra.required' => 'El precio de compra es obligatorio.',
            'precio_compra.gt' => 'El precio de compra debe ser un número positivo mayor a 0.',
            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.gt' => 'El precio de venta debe ser un número positivo mayor a 0.',
            'precio_venta.gte' => 'El precio de venta (PVP) debe ser mayor o igual al costo de compra.',
            'video.uploaded' => 'Error al subir el video. Verifica el archivo.',
            'video.mimes' => 'El formato del video no es compatible (usa mp4, avi, mov, webm, ogg, etc.).',
            'variante_stock.*.required' => 'El stock de la variante es obligatorio.',
            'variante_stock.*.integer' => 'El stock debe ser un número entero.',
            'variante_stock.*.min' => 'El stock no puede ser negativo.',
            'marca.regex' => 'La marca de fabricación debe contener letras o números y no puede consistir únicamente en símbolos o líneas.',
            'marca.max' => 'La marca de fabricación no puede superar los 100 caracteres.',
            'descripcion.max' => 'La descripción funcional no puede superar los 1000 caracteres.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('nombre')) {
            $this->merge([
                'nombre' => strip_tags(trim($this->nombre)),
            ]);
        }
    }
}
