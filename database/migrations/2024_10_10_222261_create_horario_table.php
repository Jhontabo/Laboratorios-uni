<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('horario', function (Blueprint $table) {
        $table->id('id_horario');
        $table->string('dia_semana')->nullable();
        $table->time('hora_inicio')->nullable();
        $table->time('hora_fin')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('horario');
}


};
