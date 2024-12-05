<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        Schema::create('laboratorios', function (Blueprint $table) {
            $table->id('id_laboratorio');
            $table->string('nombre')->nullable();
            $table->string('ubicacion')->nullable();
            $table->integer('capacidad')->nullable();
            $table->foreignId('id_usuario') // Usuario asignado al laboratorio
                ->constrained('users', 'id_usuario') // Relación con la tabla users
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('laboratorios');
    }
};
