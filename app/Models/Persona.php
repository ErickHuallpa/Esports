<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [ 
        'nombre',
        'apellidos',
        'ci',
        'telefono',
        'direccion',
        'fecha_nacimiento',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Obtener la cuenta de usuario única asociada a esta persona.
     */
    public function user()
    {
        return $this->hasOne(User::class, 'persona_id');
    }
}