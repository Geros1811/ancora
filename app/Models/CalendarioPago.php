<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioPago extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'concepto',
        'fecha_pago',
        'pago',
        'acumulado',
        'ticket',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
