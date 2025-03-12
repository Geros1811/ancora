<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obra extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'presupuesto',
        'metros_cuadrados',
        'cliente',
        'fecha_inicio',
        'fecha_termino',
        'residente',
        'ubicacion',
        'descripcion',
        'user_id',
        'arquitecto_id'
    ];

    public function detalles()
    {
        return $this->hasMany(DetallesObra::class, 'obra_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function architects()
    {
        return $this->belongsToMany(User::class, 'obra_user', 'obra_id', 'user_id')
            ->withPivot('added_by')
            ->withTimestamps();
    }

    public function maestroObras()
    {
        return $this->belongsToMany(User::class, 'obra_user', 'obra_id', 'user_id')
            ->withPivot('added_by')
            ->withTimestamps();
    }
}
