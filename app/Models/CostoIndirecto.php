<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostoIndirecto extends Model
{
    use HasFactory;

    protected $table = 'costos_indirectos';

    protected $fillable = [
        'obra_id',
        'nombre',
        'costo',
    ];
}
