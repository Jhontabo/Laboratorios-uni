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
                ->constrained('horarios', 'id_horario') // Relación con horarios.id_horario
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Clave foránea para usuarios
            $table->foreignId('id_usuario')
                ->constrained('users', 'id_usuario') // Relación con users.id_usuario
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('estado')->default('pendiente'); // Estado de la reserva
            $table->timestamps(); // Campos created_at y updated_at

            // Índices adicionales (opcional)
            $table->index('id_horario');
            $table->index('id_usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
