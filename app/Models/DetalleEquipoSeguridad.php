<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEquipoSeguridad extends Model
{
    use HasFactory;

    protected $table = 'detalle_equipo_seguridad';

    protected $fillable = [
        'obra_id',
        'fecha',
        'concepto',
        'unidad',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];
}
