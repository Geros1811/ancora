<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaChica extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'maestro_obra_id',
        'fecha',
        'cantidad',
    ];

    protected $casts = [
    ];

    public function maestroObra()
    {
        return $this->belongsTo(User::class, 'maestro_obra_id');
    }

    public function detallesCajaChica()
    {
        return $this->hasMany(DetalleCajaChica::class);
    }
}
