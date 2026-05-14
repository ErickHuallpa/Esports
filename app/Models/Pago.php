<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';

    protected $fillable = [
        'tipo_pago_id',
        'cliente_id',
        'monto',
        'estado',
        'comprobante_url',
        'motivo_rechazo',
        'fecha_pago',
        'verificado_por',
        'fecha_verificacion',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'monto' => 'decimal:2',
            'fecha_pago' => 'datetime',
            'fecha_verificacion' => 'datetime',
        ];
    }
}