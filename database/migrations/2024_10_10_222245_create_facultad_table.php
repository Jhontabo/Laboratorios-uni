<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
     
    public function up()
{
    Schema::create('facultad', function (Blueprint $table) {
        $table->id('id_facultad');
        $table->string('nombre')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('facultad');
}

};
