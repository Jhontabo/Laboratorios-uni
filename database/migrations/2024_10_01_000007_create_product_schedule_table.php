<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Primero crear la tabla sin claves foráneas
        Schema::create('product_schedule', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('schedule_id');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['product_id', 'schedule_id']);
        });

        // Luego añadir las claves foráneas si existen las tablas
        if (Schema::hasTable('products') && Schema::hasTable('schedules')) {
            Schema::table('product_schedule', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade');

                $table->foreign('schedule_id')
                    ->references('id')
                    ->on('schedules')
                    ->onDelete('cascade');
            });
        } else {
            throw new RuntimeException('Las tablas products y schedules deben existir primero');
        }
    }

    public function down()
    {
        Schema::dropIfExists('product_schedule');
    }
};
