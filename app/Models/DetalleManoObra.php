<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleManoObra extends Model
{
    use HasFactory;

    protected $table = 'detalle_mano_obra';

    protected $fillable = [
        'obra_id',
        'nombre',
        'puesto',
        'lunes',
        'martes',
        'miercoles',
        'jueves',
        'viernes',
        'sabado',
        'total_horas',
        'precio_hora',
        'extras_menos',
        'subtotal'
    ];
}
