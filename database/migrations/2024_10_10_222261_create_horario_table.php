<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('horario', function (Blueprint $table) {
            $table->id('id_horario');
            $table->foreignId('id_laboratorista')->constrained('laboratorista', 'id_laboratorista')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_laboratorio')->constrained('laboratorio', 'id_laboratorio')->onDelete('cascade')->onUpdate('cascade'); // Asegúrate que el nombre es singular
            $table->string('dia_semana')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('horario');
    }
};
