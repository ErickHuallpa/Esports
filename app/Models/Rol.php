<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [ 
        'nombre',
        'descripcion',
    ];

    /**
     * Un rol pertenece a muchos usuarios.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}