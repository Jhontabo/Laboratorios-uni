<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Campos básicos del horario
            $table->string('title');
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('color')->default('#3b82f6');
            $table->text('description')->nullable();
            $table->boolean('is_available')->default(true);

            // Relaciones
            $table->foreignId('laboratory_id')
                ->constrained('laboratories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Campos adicionales necesarios (elimina el nullable si son obligatorios)
            $table->foreignId('academic_program_id')
                ->nullable()
                ->constrained('academic_programs')
                ->nullOnDelete();

            $table->integer('semester')->nullable();
            $table->integer('student_count')->nullable();
            $table->integer('group_count')->nullable();

            $table->timestamps();

            // Índices para mejorar el rendimiento
            $table->index('is_available');
            $table->index('start_at');
            $table->index('end_at');
            $table->index(['laboratory_id', 'start_at']); // Para búsquedas por lab y fecha
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};
