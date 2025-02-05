<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestajoDetalle extends Model
{
    protected $fillable = [
        'obra_id',
        'frente',
        'cotizacion',
        'monto_aprobado',
        'pendiente',
        'estado',
        'monto_aprobado_total',
    ];

    public function pagos()
    {
        return $this->hasMany(DestajoPago::class);
    }
}
