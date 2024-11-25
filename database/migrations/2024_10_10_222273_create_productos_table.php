<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_productos');
            $table->string('nombre')->nullable();
            $table->text('descripcion')->nullable();
            $table->integer('cantidad_disponible')->nullable();
            $table->foreignId('id_laboratorio')
                  ->constrained('laboratorio','id_laboratorio')
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_categorias')
                  ->constrained('categorias','id_categorias')
                  ->onDelete('cascade')->onUpdate('cascade');
            $table->string('numero_serie')->nullable(); 
            $table->string('ubicacion')->nullable(); 
            $table->enum('estado', ['nuevo', 'usado', 'dañado'])->default('nuevo'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
