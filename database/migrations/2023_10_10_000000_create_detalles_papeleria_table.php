<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetallesPapeleriaTable extends Migration
{
    public function up()
    {
        Schema::create('detalles_papeleria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_id');
            $table->date('fecha');
            $table->string('concepto');
            $table->string('unidad');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 15, 2)->storedAs('cantidad * precio_unitario');
            $table->decimal('costo_total', 15, 2)->nullable();
            $table->timestamps();

            $table->foreign('obra_id')->references('id')->on('obras')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalles_papeleria');
    }
}
