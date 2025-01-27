<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CostoIndirecto;
use App\Models\Obra;

class AddDefaultCostosIndirectosToExistingObras extends Migration
{
    public function up()
    {
        $obras = Obra::all();
        $costosIndirectos = ['Papelería', 'Gasolina', 'Renta', 'Utilidades'];

        foreach ($obras as $obra) {
            foreach ($costosIndirectos as $costo) {
                CostoIndirecto::updateOrCreate(
                    ['obra_id' => $obra->id, 'nombre' => $costo],
                    ['costo' => 0]
                );
            }
        }
    }

    public function down()
    {
        // No se requiere acción para revertir esta migración
    }
}
