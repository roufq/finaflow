<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['real_estate', 'vehicle', 'electronics', 'jewelry', 'collectibles', 'insurance', 'other']);
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('purchase_value', 15, 2); // Original purchase price
            $table->decimal('current_value', 15, 2); // Current estimated value
            $table->date('purchase_date');
            $table->decimal('depreciation_rate', 5, 2)->nullable(); // Annual depreciation rate (percentage)
            $table->decimal('monthly_income', 15, 2)->default(0); // For rental income, etc.
            $table->string('location')->nullable(); // Address for real estate, etc.
            $table->string('serial_number')->nullable(); // For electronics, vehicles
            $table->date('insurance_expiry')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
