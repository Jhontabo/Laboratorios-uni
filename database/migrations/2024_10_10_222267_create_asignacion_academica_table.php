<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
{
    Schema::create('asignacion_academica', function (Blueprint $table) {
        $table->id('idAsignacion_Academica');
        $table->foreignId('id_docente')->constrained('docente','id_docente')->onDelete('cascade')->onUpdate('cascade');
        $table->foreignId('d_programa')->constrained('programa','id_programa')->onDelete('cascade')->onUpdate('cascade');
        $table->string('fecha_asignacion')->nullable();
        $table->string('estado')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('asignacion_academica');
}

   
};
