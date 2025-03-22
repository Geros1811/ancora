<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestajoSinNominaDetalle extends Model
{
    use HasFactory;

    protected $table = 'destajo_sin_nomina_detalles';

    protected $fillable = [
        'partida_id',
        'clave',
        'concepto',
        'unidad',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'pagos',
    ];
}
