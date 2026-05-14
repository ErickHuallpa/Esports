<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas';

    protected $fillable = [
        'cliente_id',
        'producto_id',
        'calificacion',
        'comentario',
        'fecha_resena',
    ];

    protected function casts(): array
    {
        return [
            'calificacion' => 'integer',
            'fecha_resena' => 'datetime',
        ];
    }
}