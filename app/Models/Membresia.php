<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membresia extends Model
{
    use HasFactory;

    protected $table = 'membresias'; // Nombre de la tabla
    protected $fillable = ['nombre', 'precio', 'max_obras', 'max_clientes', 'max_residentes', 'duracion_dias'];
}
