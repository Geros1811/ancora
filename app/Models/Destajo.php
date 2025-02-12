<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Destajo extends Model
{
    use HasFactory;

    protected $table = 'destajos';

    protected $fillable = [
        'obra_id',
        'nomina_id',
        'frente',
        'cantidad',
        'monto_aprobado',
        'paso_actual',
        'no_pago',
        'locked'
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class);
    }
    public function destajo()
{
    return $this->belongsTo(Destajo::class, 'destajo_id');
}
}
