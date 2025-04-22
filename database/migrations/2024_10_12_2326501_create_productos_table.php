<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        if (!Schema::hasTable('productos')) {
            Schema::create('productos', function (Blueprint $table) {
                // Primary Key
                $table->id('id_productos');

                // Información básica del producto
                $table->string('nombre')->nullable();
                $table->text('descripcion')->nullable();
                $table->integer('cantidad_disponible')->nullable();

                // Relaciones con otras tablas
                $table->foreignId('id_laboratorio')
                    ->constrained('laboratorios', 'id_laboratorio')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->foreignId('id_categorias')
                    ->constrained('categorias', 'id_categorias')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                // Datos técnicos
                $table->string('numero_serie')->nullable();
                $table->date('fecha_adicion')->nullable();
                $table->date('fecha_adquisicion')->nullable();
                $table->date('fecha_solicitud')->nullable();
                $table->date('fecha_devolucion_estimada')->nullable();
                $table->date('fecha_devolucion_real')->nullable();
                $table->date('fecha_aprobacion')->nullable();
                $table->decimal('costo_unitario', 8, 2)->nullable();
                $table->string('ubicacion')->nullable();

                // Gestión de préstamos
                $table->boolean('disponible_para_prestamo')->default(true);


                // Tipos y estados
                $table->enum('tipo_producto', ['equipo', 'suministro'])->default('equipo');
                $table->enum('estado_producto', [
                    'nuevo',
                    'usado',
                    'dañado',
                    'dado_de_baja',
                    'perdido'
                ])->default('nuevo');

                $table->enum('estado_prestamo', [
                    'disponible',
                    'pendiente',
                    'aprobado',
                    'rechazado',
                    'devuelto',
                    'en_prestamo'
                ])->default('disponible');

                // Multimedia
                $table->string('imagen')->nullable();

                // Relación con usuario (corregida)
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')
                    ->references('user_id') // Asegúrate que users tenga columna 'id'
                    ->on('users');
                // Timestamps
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
