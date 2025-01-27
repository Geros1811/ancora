<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleComidas extends Model
{
    use HasFactory;

    protected $table = 'detalle_comidas';

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
