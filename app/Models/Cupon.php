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
        'usado',
        'usado_en',
        'usado_por',
    ];

    protected $casts = [
        'usado' => 'boolean',
        'valor' => 'decimal:2',
        'usado_en' => 'datetime',
    ];
}