<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario_compra',
        'precio_unitario_venta',
        'descuento_unitario',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'precio_unitario_compra' => 'decimal:2',
            'precio_unitario_venta' => 'decimal:2',
            'descuento_unitario' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }
}