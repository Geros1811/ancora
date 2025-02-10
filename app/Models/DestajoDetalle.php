<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestajoDetalle extends Model
{
    protected $table = 'destajos_detalles';

    protected $fillable = [
        'obra_id',
        'frente',
        'cotizacion',
        'monto_aprobado',
        'pendiente',
        'estado',
        'monto_aprobado_total',
    ];

    protected $casts = [
        'pagos' => 'array',
    ];

    public function pagos()
    {
        return $this->hasMany(DestajoPago::class);
    }
}
