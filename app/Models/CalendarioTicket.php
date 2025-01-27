<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarioTicket extends Model
{
    use HasFactory;

    protected $fillable = ['calendario_pago_id', 'archivo'];

    // Relación con la tabla de calendarios pagos
    public function calendarioPago()
    {
        return $this->belongsTo(CalendarioPago::class);
    }
}
