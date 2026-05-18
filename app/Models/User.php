<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'persona_id',
        'rol_id',
        'email',
        'username',
        'password',
        'activo',
        'ultimo_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
            'ultimo_login' => 'datetime',
        ];
    }

    // RELACIONES

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'user_id');
    }

    public function pagosVerificados()
    {
        return $this->hasMany(Pago::class, 'verificado_por');
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'user_id');
    }

    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'user_id');
    }

    public function enviosAsignados()
    {
        return $this->hasMany(Envio::class, 'admin_asignado');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'user_id');
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'user_id');
    }
}