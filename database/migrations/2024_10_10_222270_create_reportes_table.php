<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id('idReportes');
            $table->foreignId('id_usuario')->constrained('usuarios','id_usuario')->onDelete('cascade')->onUpdate('cascade');
            $table->string('fecha_generacion');
            $table->text('resultado');
            $table->string('formato');
            $table->foreignId('id_tipo_reporte')->constrained('tipo_reporte','id_tipo_reporte')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('reportes');
    }
    

    
};
