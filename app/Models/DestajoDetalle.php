<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestajoDetalle extends Model
{
    use HasFactory;

    protected $table = 'destajos_detalles';

    protected $fillable = [
        'obra_id',
        'frente',
        'cotizacion',
        'monto_aprobado',
        'pendiente',
        'estado',
        'monto_aprobado_total',
        'destajo_id',
        'pagos',
    ];

    protected $casts = [
        'pagos' => 'array',
    ];

    public function pagos()
    {
        return $this->hasMany(DestajoPago::class);
    }

    public function imagenes()
    {
        return $this->hasMany(Imagen::class);
    }
}
