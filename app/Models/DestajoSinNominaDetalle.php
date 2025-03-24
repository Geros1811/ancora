<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DestajoSinNominaDetalle extends Model
{
    use HasFactory;

    public function partida(): BelongsTo
    {
        return $this->belongsTo(Partida::class);
    }

    protected $table = 'destajo_sin_nomina_detalles';

    protected $fillable = [
        'partida_id',
        'clave',
        'concepto',
        'unidad',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'pagos',
    ];
}
