<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Primary key

            // Basic structure
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('available_quantity')->default(0);

            // Relationships
            $table->foreignId('laboratory_id')
                ->constrained('laboratories')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            // Technical details
            $table->string('serial_number')->nullable()->unique();
            $table->decimal('unit_cost')->nullable();
            $table->string('location')->nullable();
            $table->date('acquisition_date')->nullable();

            // State management
            $table->enum('product_type', ['equipment', 'supply'])->default('equipment');
            $table->enum('product_condition', [
                'new',
                'used',
                'damaged',
                'decommissioned',
                'lost'
            ])->default('new');

            $table->boolean('available_for_loan')->default(true);

            // Media
            $table->string('image')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index('name');
            $table->index('serial_number');
            $table->index('available_for_loan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};

