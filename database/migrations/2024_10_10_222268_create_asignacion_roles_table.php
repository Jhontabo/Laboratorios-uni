<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up()
    {
        Schema::create('asignacion_roles', function (Blueprint $table) {
            $table->id('id_asignacionRoles');
            $table->dateTime('fechaAsignacion');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade')->onUpdate('cascade'); // Referencia correcta
            $table->foreignId('id_roles')->constrained('roles', 'id_roles')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
    

public function down()
{
    Schema::dropIfExists('asignacion_roles');
}

};
