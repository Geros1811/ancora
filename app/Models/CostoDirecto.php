<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostoDirecto extends Model
{
    use HasFactory;

    protected $table = 'costos_directos';

    protected $fillable = [
        'obra_id',
        'nombre',
        'costo'
    ];
}
