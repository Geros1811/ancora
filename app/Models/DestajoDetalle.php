<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestajoDetalle extends Model
{
    use HasFactory;

    protected $table = 'destajo_detalles';

    protected $fillable = [
        'obra_id',
        'frente',
        'cotizacion',
        'monto_aprobado',
        'paso_numero',
        'paso_fecha',
        'paso_monto',
        'pendiente',
        'estado',
        'monto_aprobado_total'
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public static function calcularMontoAprobadoTotal($obraId)
    {
        return self::where('obra_id', $obraId)->sum('monto_aprobado');
    }
}
