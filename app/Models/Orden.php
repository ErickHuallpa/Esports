<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'venta_id',
        'user_id',
        'estado_orden',
        'ciudad_destino',
        'direccion_envio',
    ];

    // RELACIONES

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function envio()
    {
        return $this->hasOne(Envio::class, 'orden_id');
    }
}