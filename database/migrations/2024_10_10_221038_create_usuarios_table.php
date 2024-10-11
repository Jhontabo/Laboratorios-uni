<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); // Clave primaria
            $table->string('nombre');
            $table->string('apellido');
            $table->string('correo_electronico')->unique();
            $table->string('telefono');
            $table->string('Direccion');
            $table->rememberToken(); 
            $table->timestamps();     // created_at y updated_at
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
    
};
