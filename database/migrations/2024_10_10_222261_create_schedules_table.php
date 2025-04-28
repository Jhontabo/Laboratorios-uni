<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id(); // Primary Key

            $table->string('title'); // Title of the event
            $table->dateTime('start_at'); // Start date and time
            $table->dateTime('end_at'); // End date and time
            $table->string('color')->nullable(); // Optional color
            $table->text('description')->nullable(); // Optional description
            $table->boolean('is_available')->default(true); // Availability status

            // Foreign Key for laboratory
            $table->foreignId('laboratory_id')
                ->nullable()
                ->constrained('laboratories') // References laboratories.id
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Related to laboratories');

            // Foreign Key for user
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users') // References users.id
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Related to users');

            $table->timestamps(); // created_at and updated_at

            // Additional indexes
            $table->index('is_available');
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedules');
    }
};

