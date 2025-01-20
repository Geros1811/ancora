<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calendarios_pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('obra_id'); // Tipo correcto para la clave foránea
            $table->string('concepto');
            $table->date('fecha_pago')->nullable();
            $table->decimal('pago', 15, 2)->default(0.00);
            $table->decimal('acumulado', 15, 2)->default(0.00);
            $table->string('ticket')->nullable();
            $table->timestamps();
        
            $table->foreign('obra_id')->references('id')->on('obras')->onDelete('cascade');
        });        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendarios_pagos');
    }
};
