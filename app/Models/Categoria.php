<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    /**
     * La tabla asociada al modelo.
     * Laravel lo deduce automáticamente como 'categorias', pero es buena práctica especificarlo.
     *
     * @var string
     */
    protected $table = 'categorias';

    /**
     * Los atributos que son asignables masivamente (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     * Esto es muy útil con PostgreSQL para evitar que interprete el booleano como un entero (0 o 1).
     *
     * @var array
     */
    protected $casts = [
        'activo' => 'boolean',
    ];
}