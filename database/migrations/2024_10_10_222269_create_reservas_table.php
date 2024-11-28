<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva'); // Clave primaria

            // Clave foránea para horario
            $table->foreignId('id_horario')
                ->constrained('horario', 'id_horario') // Relación con horario.id_horario
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Clave foránea para usuarios
            $table->foreignId('id_usuario')
                ->constrained('users', 'id_usuario') // Relación con users.id_usuario
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('estado')->default('pendiente'); // Estado de la reserva
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
