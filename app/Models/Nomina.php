<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    use HasFactory;

    protected $table = 'nominas';

    protected $fillable = [
        'obra_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin'
    ];
}
