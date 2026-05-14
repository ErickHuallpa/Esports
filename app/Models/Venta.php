<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id',
        'pago_id',
        'precio_total',
        'descuento_aplicado',
        'estado_venta',
        'fecha_venta',
    ];

    protected function casts(): array
    {
        return [
            'precio_total' => 'decimal:2',
            'descuento_aplicado' => 'decimal:2',
            'fecha_venta' => 'datetime',
        ];
    }
}