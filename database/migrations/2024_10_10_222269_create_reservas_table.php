<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Crear la tabla 'reservas'
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reserva'); // Clave primaria personalizada

            // Datos del usuario no autenticado
            $table->string('nombre_usuario')->nullable()->comment('Nombre del usuario que realizó la reserva');
            $table->string('apellido_usuario')->nullable()->comment('Apellido del usuario que realizó la reserva');
            $table->string('correo_usuario')->nullable()->comment('Correo del usuario que realizó la reserva');

            // Razón del rechazo (si aplica)
            $table->text('razon_rechazo')->nullable()->comment('Razón del rechazo de la reserva');

            // Clave foránea para horarios
            $table->foreignId('id_horario')
                ->constrained('horarios', 'id_horario')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Clave foránea para usuarios
            $table->foreignId('id_usuario')
                ->nullable() // Permitir null para reservas de usuarios no autenticados
                ->constrained('users', 'id_usuario')
                ->nullOnDelete(); // Si el usuario se elimina, el campo se establece en null

            // Estado de la reserva
            $table->string('estado', 20)->default('pendiente')->comment('Estado de la reserva');

            // Campos de tiempo
            $table->timestamps(); // created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservas');
    }
};
