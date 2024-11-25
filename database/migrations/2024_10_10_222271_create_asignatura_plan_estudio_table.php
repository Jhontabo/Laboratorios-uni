<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('asignatura_plan_estudio', function (Blueprint $table) {
        $table->id('id_asignatura_plan_estudio');
        $table->foreignId('id_plan_de_estudios')->constrained('plan_de_estudios','id_plan_de_estudios')->onDelete('cascade')->onUpdate('cascade');
        $table->foreignId('id_asignatura')->constrained('asignatura','id_asignatura')->onDelete('cascade')->onUpdate('cascade');
        $table->string('Estado')->nullable();
        $table->string('Fecha_actualizacion')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('asignatura_plan_estudio');
}

};
