<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('asignatura', function (Blueprint $table) {
        $table->id('id_asignatura');
        $table->string('nombre')->nullable();
        $table->string('numero_creditos')->nullable();
        $table->string('Estado')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('asignatura');
}


    
};
