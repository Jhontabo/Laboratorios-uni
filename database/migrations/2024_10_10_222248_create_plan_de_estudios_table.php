<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('plan_de_estudios', function (Blueprint $table) {
        $table->id('id_plan_de_estudios');
        $table->string('estado')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('plan_de_estudios');
}

};
