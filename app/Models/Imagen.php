<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    protected $table = 'imagenes';

    protected $fillable = [
        'path',
        'destajo_detalle_id',
    ];

    public function destajoDetalle()
    {
        return $this->belongsTo(DestajoDetalle::class);
    }
}
