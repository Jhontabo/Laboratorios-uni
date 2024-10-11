<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratorista', function (Blueprint $table) {
            $table->id('id_laboratorista'); // Clave primaria de laboratorista
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade')->onUpdate('cascade');
            $table->string('estado')->nullable();
            $table->string('fecha_ingreso')->nullable();
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('laboratorista');
    }
    
};
