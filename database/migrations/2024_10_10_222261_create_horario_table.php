<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('horario', function (Blueprint $table) {
            $table->id('id_horario'); // Clave primaria

            $table->string('title'); // Título del evento
            $table->dateTime('start_at'); // Fecha y hora de inicio
            $table->dateTime('end_at'); // Fecha y hora de fin 
            $table->string('color')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);


            // Clave foránea para la tabla laboratorio
            $table->foreignId('id_laboratorio')
                ->nullable()
                ->constrained('laboratorios', 'id_laboratorio')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Clave foránea para la tabla users
            $table->foreignId('id_usuario')
                ->nullable()
                ->constrained('users', 'id_usuario') // Relación con users.id_usuario
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('horario');
    }
};
