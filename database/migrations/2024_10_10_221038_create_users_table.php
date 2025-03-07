<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id_usuario'); // Clave primaria personalizada
            $table->string('name'); // Nombre del usuario
            $table->string('apellido'); // Apellido del usuario
            $table->string('email')->unique(); // Correo único
            $table->string('telefono')->nullable(); // Teléfono opcional
            $table->string('direccion'); // Dirección
            $table->enum('estado', ['activo', 'inactivo'])->default('activo'); // Estado del usuario
            $table->rememberToken(); // Token para recordar sesión
            $table->timestamps(); // Timestamps created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
