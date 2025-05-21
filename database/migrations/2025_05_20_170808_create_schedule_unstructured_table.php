<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedule_unstructured', function (Blueprint $table) {
            $table->id();

            // Clave foránea a schedules
            $table->foreignId('schedule_id')
                ->constrained('schedules')
                ->cascadeOnDelete(); // Elimina los registros asociados al eliminar schedule

            // Campos específicos para unstructured
            $table->string('research_name');
            $table->string('advisor_name');
            $table->string('applicants_name');


            $table->timestamps(); // Campos created_at y updated_at

            // Índices para mejorar rendimiento en consultas
            $table->index('research_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_unstructured');
    }
};
