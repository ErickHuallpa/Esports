<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventarios';

    protected $fillable = [
        'producto_variante_id',
        'user_id',
        'tipo_movimiento',
        'cantidad',
        'stock_anterior',
        'stock_resultante',
        'motivo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_movimiento' => 'datetime',
        ];
    }

    // RELACIONES

    public function variante()
    {
        return $this->belongsTo(ProductoVariante::class, 'producto_variante_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}