<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Oferta extends Model
{
    use HasFactory;

    protected $table = 'ofertas';

    protected $fillable = [
        'producto_id',
        'descripcion',
        'porcentaje_descuento',
        'precio_oferta',
        'fecha_inicio',
        'fecha_fin',
        'activa',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje_descuento' => 'decimal:2',
            'precio_oferta' => 'decimal:2',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activa' => 'boolean',
        ];
    }

    // RELACIONES

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}