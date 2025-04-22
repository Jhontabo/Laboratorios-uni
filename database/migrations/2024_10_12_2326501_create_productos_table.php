<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                // Estructura básica
                $table->id('id_producto');
                $table->string('nombre');
                $table->text('descripcion')->nullable();
                $table->integer('cantidad_disponible')->default(0);

                // Relaciones
                $table->foreignId('id_laboratorio')
                    ->constrained('laboratorios', 'id_laboratorio')
                    ->cascadeOnDelete();

                $table->foreignId('id_categorias')
                    ->constrained('categorias', 'id_categorias')
                    ->cascadeOnDelete();

                // Datos técnicos esenciales
                $table->string('numero_serie')->nullable()->unique();
                $table->decimal('costo_unitario', 8, 2)->nullable();
                $table->string('ubicacion')->nullable();
                $table->date('fecha_adquisicion')->nullable();

                // Gestión de estado
                $table->enum('tipo_producto', ['equipo', 'suministro'])->default('equipo');
                $table->enum('estado_producto', [
                    'nuevo',
                    'usado',
                    'dañado',
                    'dado_de_baja',
                    'perdido'
                ])->default('nuevo');

                $table->boolean('disponible_para_prestamo')->default(true);

                // Multimedia
                $table->string('imagen')->nullable();

                // Timestamps
                $table->timestamps();

                // Índices para mejor performance
                $table->index('nombre');
                $table->index('numero_serie');
                $table->index('disponible_para_prestamo');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};