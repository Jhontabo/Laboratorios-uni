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
        Schema::create('schedule_equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained()->onDelete('cascade');

            // Primero como unsignedBigInteger
            $table->unsignedBigInteger('equipment_id');

            $table->integer('quantity')->default(1);
            $table->timestamps();
        });

        // Luego añade la FK solo si existe la tabla equipments
        if (Schema::hasTable('equipments')) {
            Schema::table('schedule_equipment', function (Blueprint $table) {
                $table->foreign('equipment_id')
                    ->references('id')
                    ->on('equipments')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_equipment');
    }
};
