<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre_empresa',
        'telefono',
        'email',
        'contacto_nombre',
        'direccion',
        'ciudad',
        'pais',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    /**
     * Obtener todos los productos suministrados por este proveedor.
     */
    public function productos()
    {
        return $this->hasMany(Producto::class, 'proveedor_id');
    }
}