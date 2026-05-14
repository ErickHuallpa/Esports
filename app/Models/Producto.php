<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'proveedor_id',
        'nombre',
        'descripcion',
        'marca',
        'precio_compra',
        'precio_venta',
        'stock',
        'tallas_disponibles',
        'colores_disponibles',
        'imagen_url',
        'modelo_3d_url',
        'visible',
        'agotado',
    ];

    protected function casts(): array
    {
        return [
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'visible' => 'boolean',
            'agotado' => 'boolean',
        ];
    }
}