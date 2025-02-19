<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCajaChica extends Model
{
    use HasFactory;

    protected $fillable = [
        'caja_chica_id',
        'descripcion',
        'vista',
        'gasto',
        'foto',
    ];
}
