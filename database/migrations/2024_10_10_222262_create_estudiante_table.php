<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('estudiante', function (Blueprint $table) {
        $table->id('id_estudiante');
        $table->foreignId('id_usuario')->constrained('usuarios','id_usuario')->onDelete('cascade')->onUpdate('cascade');
        $table->string('Estado');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('estudiante');
}

};
