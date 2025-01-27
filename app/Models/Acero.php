<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acero extends Model
{
    use HasFactory;

    protected $table = 'aceros';

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
