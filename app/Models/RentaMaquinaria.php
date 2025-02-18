<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentaMaquinaria extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'fecha',
        'concepto',
        'unidad',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $table = 'renta_maquinarias';
}
