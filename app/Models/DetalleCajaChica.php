<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCajaChica extends Model
{
    use HasFactory;

    protected $table = 'detalle_caja_chicas';

    protected $fillable = [
        'caja_chica_id',
        'concepto',
        'vista',
        'unidad',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'foto',
    ];
}
