<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallePapeleria extends Model
{
    use HasFactory;

    protected $table = 'detalles_papeleria';

    protected $fillable = [
        'fecha',
        'concepto',
        'unidad',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];
}
