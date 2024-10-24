<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva');
            $table->foreignId('id_usuario')->constrained('users', 'id_usuario')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_laboratorio')->constrained('laboratorio', 'id_laboratorio')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_horario')->constrained('horario', 'id_horario')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_laboratorista')->constrained('laboratorista', 'id_laboratorista')->onDelete('cascade')->onUpdate('cascade');
            $table->string('Estado')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
