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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['credit_card', 'personal_loan', 'student_loan', 'mortgage', 'car_loan', 'business_loan', 'other']);
            $table->string('lender');
            $table->decimal('original_amount', 15, 2);
            $table->decimal('current_balance', 15, 2);
            $table->decimal('interest_rate', 5, 2); // Annual interest rate
            $table->decimal('minimum_payment', 15, 2);
            $table->date('due_date');
            $table->enum('status', ['active', 'paid_off', 'defaulted', 'settled'])->default('active');
            $table->enum('payoff_strategy', ['snowball', 'avalanche', 'custom'])->default('avalanche');
            $table->json('payment_history')->nullable(); // Store payment history as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('debts');
    }
};
