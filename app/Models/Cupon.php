<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';
    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'monto_minimo',
        'usado',
        'activo',
        'usado_en',
        'usado_por',
    ];

    protected $casts = [
        'usado' => 'boolean',
        'activo' => 'boolean',
        'valor' => 'decimal:2',
        'usado_en' => 'datetime',
    ];
}