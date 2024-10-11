<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
{
    Schema::create('laboratorio', function (Blueprint $table) {
        $table->id('id_laboratorio');
        $table->string('nombre')->nullable();
        $table->string('ubicacion')->nullable();
        $table->integer('capacidad')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('laboratorio');
}

    
};
