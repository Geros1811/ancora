<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCalendariosTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('calendarios_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('calendario_pago_id'); // Clave foránea que se refiere a 'calendarios_pagos'
            $table->string('imagen_path')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            // Relación con la tabla 'calendarios_pagos'
            $table->foreign('calendario_pago_id')->references('id')->on('calendarios_pagos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('calendarios_tickets');
    }
}