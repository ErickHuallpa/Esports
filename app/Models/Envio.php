<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    protected $table = 'envios';

    protected $fillable = [
        'orden_id',
        'direccion_destino',
        'ciudad_destino',
        'zona_destino',
        'ruta',
        'estado_envio',
        'admin_asignado',
        'codigo_seguimiento',
        'fecha_despacho',
        'fecha_entrega_estimada',
        'fecha_entrega_real',
        'responsable_entrega',
        'costo_envio',
    ];

    protected function casts(): array
    {
        return [
            'fecha_despacho' => 'date',
            'fecha_entrega_estimada' => 'date',
            'fecha_entrega_real' => 'date',
            'costo_envio' => 'decimal:2',
        ];
    }
}