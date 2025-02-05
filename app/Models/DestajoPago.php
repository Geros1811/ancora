<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DestajoPago extends Model
{
    protected $fillable = [
        'destajo_detalle_id',
        'fecha',
        'monto',
    ];

    public function destajoDetalle()
    {
        return $this->belongsTo(DestajoDetalle::class);
    }
}
