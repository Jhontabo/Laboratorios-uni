<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('estudiante_programa', function (Blueprint $table) {
            $table->id('id_estudiante_programa');
            $table->foreignId('id_estudiante')->constrained('estudiante', 'id_estudiante')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_programa')->constrained('programa','id_programa')->onDelete('cascade')->onUpdate('cascade');
            $table->string('fecha_ingreso');
            $table->string('estado');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('estudiante_programa');
    }
    
};
