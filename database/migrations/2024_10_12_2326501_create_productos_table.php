<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_productos'); // Primary key
            $table->string('nombre')->nullable(); // Nombre del producto
            $table->text('descripcion')->nullable(); // Descripción del producto
            $table->integer('cantidad_disponible')->nullable(); // Cantidad disponible del producto
            $table->foreignId('id_laboratorio') // Relación con la tabla 'laboratorio'
                  ->constrained('laboratorio', 'id_laboratorio')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->foreignId('id_categorias') // Relación con la tabla 'categorias'
                  ->constrained('categorias', 'id_categorias')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('numero_serie')->nullable(); // Número de serie del producto
            $table->date('fecha_adicion')->nullable(); // Fecha en que el producto fue añadido
            $table->decimal('costo_unitario', 8, 2)->nullable(); // Costo unitario del producto
            $table->string('ubicacion')->nullable(); // Ubicación física del producto
            $table->enum('estado', ['nuevo', 'usado', 'dañado'])->default('nuevo'); // Estado del producto
            $table->timestamps(); // Timestamps automáticos (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
