<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destajo extends Model
{
    use HasFactory;

    protected $table = 'destajos';

    protected $fillable = [
        'nomina_id',
        'obra_id',
        'frente',
        'fecha',
        'no_pago',
        'cantidad',
        'observaciones'
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class);
    }
}
