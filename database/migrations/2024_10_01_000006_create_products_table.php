<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            // ID principal
            $table->id();

            // 1. ESTRUCTURA BÁSICA
            $table->string('name')
                ->comment('Nombre del producto/equipo');

            $table->text('description')
                ->nullable()
                ->comment('Descripción detallada del producto');

            $table->unsignedInteger('available_quantity')
                ->default(0)
                ->comment('Cantidad disponible en inventario');

            // 2. RELACIONES
            $table->foreignId('laboratory_id')
                ->constrained('laboratories')
                ->cascadeOnDelete()
                ->comment('Laboratorio al que pertenece el equipo');

            // 3. DETALLES TÉCNICOS
            $table->string('serial_number')
                ->nullable()
                ->unique()
                ->comment('Número de serie del equipo');

            $table->decimal('unit_cost', 10, 2)
                ->nullable()
                ->comment('Costo unitario del equipo');

            $table->string('location')
                ->nullable()
                ->comment('Ubicación física del equipo');

            $table->date('acquisition_date')
                ->nullable()
                ->comment('Fecha de adquisición del equipo');

            // 4. ESTADO Y TIPO
            $table->enum('product_type', [
                'equipment',    // Equipo permanente
                'supply',      // Suministro consumible
                'consumable'    // Material de consumo (corregido el typo)
            ])->default('equipment')
                ->comment('Tipo de producto');

            $table->enum('status', [
                'new',             // Nuevo
                'used',            // Usado
                'damaged',         // Dañado
                'decommissioned',  // Dado de baja
                'lost',            // Perdido
                'maintenance'      // En mantenimiento (corregido el typo)
            ])->default('new')
                ->comment('Estado actual del producto');

            $table->boolean('available_for_loan')
                ->default(true)
                ->comment('¿Disponible para préstamos?');

            // 5. REGISTRO DE BAJA (CAMPOS NUEVOS)
            $table->timestamp('decommissioned_at')
                ->nullable()
                ->comment('Fecha en que se dio de baja el equipo');

            $table->foreignId('decommissioned_by')
                ->nullable()
                ->constrained('users')
                ->comment('Usuario que dio de baja el equipo');

            // 6. AUDITORÍA
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->comment('Usuario que creó el registro');

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->comment('Usuario que actualizó el registro');

            // 7. MEDIA
            $table->string('image')
                ->nullable()
                ->comment('Imagen del producto');

            // 8. MARCAS DE TIEMPO
            $table->timestamps();
            $table->softDeletes()
                ->comment('Fecha de eliminación (soft delete)');

            // 9. ÍNDICES
            $table->index('name');
            $table->index('serial_number');
            $table->index('available_for_loan');
            $table->index('status');
            $table->index('decommissioned_at');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Eliminar las claves foráneas primero
            $table->dropForeign(['laboratory_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropForeign(['decommissioned_by']);
        });

        // Luego eliminar la tabla
        Schema::dropIfExists('products');
    }
};
