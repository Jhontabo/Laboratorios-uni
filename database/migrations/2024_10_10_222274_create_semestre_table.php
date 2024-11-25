<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
    public function up()
{
    Schema::create('semestre', function (Blueprint $table) {
        $table->id('id_semestre');
        $table->string('nombre')->nullable();
        $table->string('fecha_inicio')->nullable();
        $table->string('fecha_fin')->nullable();
        $table->foreignId('id_asignatura')->constrained('asignatura','id_asignatura')->onDelete('cascade')->onUpdate('cascade');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('semestre');
}

};
