<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id('id_horario'); // Clave primaria

            $table->string('title'); // Título del evento
            $table->dateTime('start_at'); // Fecha y hora de inicio
            $table->dateTime('end_at'); // Fecha y hora de fin
            $table->string('color')->nullable(); // Color del evento
            $table->text('description')->nullable(); // Descripción opcional
            $table->boolean('is_available')->default(true); // Disponibilidad
            $table->string('reservation_status')->default(false); // Estado de la reserva

            // Clave foránea para la tabla laboratorio
            $table->foreignId('id_laboratorio')
                ->nullable()
                ->constrained('laboratorios', 'id_laboratorio')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Relación con la tabla laboratorios');

            // Clave foránea para la tabla users
            $table->foreignId('id_usuario')
                ->nullable()
                ->constrained('users', 'id_usuario') // Relación con users.id
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Relación con la tabla usuarios');

            $table->timestamps(); // Campos created_at y updated_at

            // Índices adicionales (opcional)
            $table->index('reservation_status'); // Indexar por estado de reserva
        });
    }

    public function down()
    {
        Schema::dropIfExists('horarios');
    }
};
