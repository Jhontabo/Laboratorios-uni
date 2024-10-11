<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id('id_roles');
        $table->string('nombre_rol');
        $table->string('Estado');
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('roles');
}

};
