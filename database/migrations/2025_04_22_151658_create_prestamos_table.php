<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('prestamos', function (Blueprint $table) {

            $table->id();

            // Relación con productos (NO nullable)
            $table->unsignedBigInteger('id_producto');
            $table->foreign('id_producto')
                ->references('id_producto')
                ->on('productos')
                ->onDelete('cascade');

            // Relación con usuarios
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            // Datos del préstamo
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->dateTime('fecha_devolucion_estimada')->nullable();
            $table->dateTime('fecha_devolucion_real')->nullable();

            // Estados
            $table->enum('estado', [
                'pendiente',
                'aprobado',
                'rechazado',
                'devuelto',
                'vencido'
            ])->default('pendiente');

            // Observaciones
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('prestamos');
    }
};