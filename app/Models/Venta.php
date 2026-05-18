<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'user_id',
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

    // RELACIONES

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function pago()
    {
        return $this->belongsTo(Pago::class, 'pago_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id');
    }

    public function orden()
    {
        return $this->hasOne(Orden::class, 'venta_id');
    }
}