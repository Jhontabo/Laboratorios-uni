<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('asignacion_de_horario', function (Blueprint $table) {
            $table->id('id_laboratorio_horario');
            $table->foreignId('id_laboratorio')->constrained('laboratorio','id_laboratorio')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_horario')->constrained('horario','id_horario')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_laboratorista')->constrained('laboratorista','id_laboratorista')->onDelete('cascade')->onUpdate('cascade');
            $table->string('Fecha_asignacion');
            $table->string('Estado');
            $table->string('duracion')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('asignacion_de_horario');
    }
    

};
