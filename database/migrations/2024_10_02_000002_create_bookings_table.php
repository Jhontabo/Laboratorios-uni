<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id(); // Primary key (id)

            // Guest user information
            $table->string('first_name')->nullable()->comment('First name of the user who made the booking');
            $table->string('last_name')->nullable()->comment('Last name of the user who made the booking');
            $table->string('email')->nullable()->comment('Email of the user who made the booking');

            // Rejection reason (optional)
            $table->text('rejection_reason')->nullable()->comment('Reason for booking rejection');

            // Foreign key to schedules
            $table->foreignId('schedule_id')
                ->constrained('schedules') // It assumes schedules table has a simple 'id'
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Foreign key to users
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete(); // If the user is deleted, set NULL

            // Foreign key to laboratories
            $table->foreignId('laboratory_id')
                ->nullable()
                ->constrained('laboratories')
                ->cascadeOnDelete();

            // Booking status

            $table->enum('status', ['pending', 'approved', 'reserved', 'rejected'])->default('pending');
            // Timestamps
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
