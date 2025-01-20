<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    use HasFactory;

    // Define los atributos que se pueden asignar masivamente
    protected $fillable = [
        'nombre',
        'presupuesto',
        'cliente',
        'fecha_inicio',
        'fecha_termino',
        'residente',
        'ubicacion',
        'descripcion'
    ];

    // Relación con DetallesObra (uno a muchos)
    public function detalles()
    {
        return $this->hasMany(DetallesObra::class, 'obra_id');
    }
}
