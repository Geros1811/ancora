<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtrosPagosAdministrativos extends Model
{
    use HasFactory;

    protected $table = 'otros_pagos_administrativos';

    protected $fillable = [
        'obra_id',
        'fecha',
        'concepto',
        'importe',
        'observaciones',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
