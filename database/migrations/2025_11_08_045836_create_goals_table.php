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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('category', ['emergency_fund', 'vacation', 'house_down_payment', 'car_purchase', 'education', 'retirement', 'investment', 'debt_payoff', 'business', 'other']);
            $table->enum('type', ['short_term', 'medium_term', 'long_term']);
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->date('target_date');
            $table->enum('status', ['active', 'completed', 'paused', 'cancelled'])->default('active');
            $table->json('milestones')->nullable(); // Store milestone data as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
