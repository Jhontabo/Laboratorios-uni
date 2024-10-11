<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('rol_permisos', function (Blueprint $table) {
            $table->id('id_rol_permiso');
            $table->foreignId('id_roles')->constrained('roles','id_roles')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_permiso')->constrained('permisos','id_permiso')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('rol_permisos');
    }
    
};
