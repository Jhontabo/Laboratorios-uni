<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('usuarios', function (Blueprint $table) {
        $table->string('name')->virtualAs('CONCAT(nombre, " ", apellido)'); // Nuevo campo virtual
    });
}

public function down()
{
    Schema::table('usuarios', function (Blueprint $table) {
        $table->dropColumn('name');
    });
}

};
