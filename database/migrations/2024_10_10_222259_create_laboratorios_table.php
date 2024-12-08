<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('laboratorios', function (Blueprint $table) {
            $table->id('id_laboratorio'); // Clave primaria
            $table->string('nombre')->nullable(); // Nombre del laboratorio
            $table->string('ubicacion')->nullable(); // Ubicación del laboratorio
            $table->integer('capacidad')->nullable(); // Capacidad máxima del laboratorio

            // Clave foránea para el usuario asignado al laboratorio
            $table->foreignId('id_usuario')
                ->nullable() // Permitir que no haya usuario asignado inicialmente
                ->constrained('users', 'id_usuario') // Relación con la tabla users
                ->onDelete('cascade') // Eliminar laboratorio si el usuario es eliminado
                ->onUpdate('cascade'); // Actualizar clave si el usuario es actualizado

            $table->timestamps(); // Campos created_at y updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratorios'); // Eliminar la tabla laboratorios
    }
};
