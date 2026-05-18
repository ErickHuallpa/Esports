<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas';

    protected $fillable = [
        'user_id',
        'producto_id',
        'calificacion',
        'comentario',
    ];

    protected function casts(): array
    {
        return [
            'fecha_resena' => 'datetime',
        ];
    }

    // RELACIONES

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}