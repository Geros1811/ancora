<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SueldoResidente extends Model
{
    use HasFactory;

    protected $table = 'sueldo_residentes';

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
