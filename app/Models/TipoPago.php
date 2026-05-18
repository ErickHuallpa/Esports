<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPago extends Model
{
    use HasFactory;

    protected $table = 'tipo_pagos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * Obtener todos los registros de pagos que utilizaron este tipo de pago.
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'tipo_pago_id');
    }
}