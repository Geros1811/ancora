<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetallesObra extends Model
{
    use HasFactory;

    protected $table = 'detalles_obra'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'obra_id',
        'seccion',
        'concepto',
        'fecha',
        'costo',
        'acumulado'
    ];

    // Relación con Obra (muchos a uno)
    public function obra()
    {
        return $this->belongsTo(Obra::class, 'obra_id');
    }
}