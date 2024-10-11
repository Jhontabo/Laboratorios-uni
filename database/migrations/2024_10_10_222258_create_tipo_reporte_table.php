<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
{
    Schema::create('tipo_reporte', function (Blueprint $table) {
        $table->id('id_tipo_reporte');
        $table->enum('tipo_reporte', ['uso_laboratorio', 'reservas_lab', 'mantenimiento_equipos'])->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('tipo_reporte');
}

    
};
