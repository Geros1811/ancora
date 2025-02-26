<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagosAdministrativos extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'nombre',
        'costo',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
