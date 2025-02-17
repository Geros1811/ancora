<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Imagen extends Model
{
    use HasFactory;

    protected $table = 'imagenes';

    protected $fillable = [
        'path',
        'destajo_id',
    ];

    public function destajo()
    {
        return $this->belongsTo(Destajo::class);
    }
}
