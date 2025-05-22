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

            $table->foreignId('schedule_id')
                ->constrained('schedules')
                ->cascadeOnDelete();

            $table->string('project_type');             // Proyecto integrador, trabajo de grado...
            $table->string('academic_program');         // Ingeniería de Sistemas, etc.
            $table->unsignedTinyInteger('semester');    // 1 al 10
            $table->string('applicants');               // Nombres de los solicitantes
            $table->string('research_name');            // Nombre de la investigación
            $table->string('advisor');                  // Nombre del asesor


            $table->timestamps();
            $table->index('research_name');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_unstructured');
    }
};
