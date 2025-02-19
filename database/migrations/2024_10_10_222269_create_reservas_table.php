<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
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

            // ✅ Corregir la clave foránea de `id_usuario`
            $table->unsignedBigInteger('id_usuario')->nullable(); // Permitir NULL para usuarios no autenticados
            $table->foreign('id_usuario')
                ->references('id_usuario') // 🔥 Referenciar `id_usuario` en `users`
                ->on('users')
                ->nullOnDelete(); // Si el usuario es eliminado, se pone NULL

            // ✅ Corregir la clave foránea de `id_laboratorio`
            $table->unsignedBigInteger('id_laboratorio')->nullable();
            $table->foreign('id_laboratorio')
                ->references('id_laboratorio')
                ->on('laboratorios')
                ->cascadeOnDelete();

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