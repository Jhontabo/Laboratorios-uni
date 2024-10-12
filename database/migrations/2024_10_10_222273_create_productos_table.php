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
            $table->string('numero_serie')->nullable(); // Nuevo campo
            $table->date('fecha_adicion')->nullable(); // Fecha de adición del producto
            $table->decimal('costo_unitario', 8, 2)->nullable(); // Costo unitario del equipo o suministro
            $table->string('ubicacion')->nullable(); // Ubicación del equipo o suministro
            $table->enum('estado', ['nuevo', 'usado', 'dañado'])->default('nuevo'); // Estado del equipo o suministro
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
