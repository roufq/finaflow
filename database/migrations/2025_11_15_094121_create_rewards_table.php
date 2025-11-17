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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('card_type'); // visa, mastercard, amex, etc.
            $table->string('reward_type'); // points, cashback, miles
            $table->decimal('points_earned', 15, 2)->default(0);
            $table->decimal('points_redeemed', 15, 2)->default(0);
            $table->decimal('cashback_amount', 10, 2)->default(0);
            $table->date('expiry_date')->nullable();
            $table->string('status')->default('active'); // active, expired, redeemed
            $table->json('transaction_history')->nullable(); // history of reward transactions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
