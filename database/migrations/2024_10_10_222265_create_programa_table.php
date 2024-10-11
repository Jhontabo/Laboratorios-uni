<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   

    public function up()
    {
        Schema::create('programa', function (Blueprint $table) {
            $table->id('id_programa');
            $table->string('nombre')->nullable();
            $table->foreignId('id_facultad')->constrained('facultad','id_facultad')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_plan_de_estudios')->constrained('plan_de_estudios','id_plan_de_estudios')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('programa');
    }
    
};
