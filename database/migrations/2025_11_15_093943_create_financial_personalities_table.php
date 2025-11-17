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
        Schema::create('financial_personalities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('personality_type'); // spender, saver, investor, avoider, etc.
            $table->integer('risk_tolerance')->default(5); // 1-10 scale
            $table->string('spending_style'); // impulsive, planned, emotional, rational
            $table->string('saving_habits'); // consistent, irregular, goal-oriented, none
            $table->json('scores'); // detailed scores for different aspects
            $table->date('assessment_date');
            $table->json('recommendations')->nullable(); // personalized recommendations
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_personalities');
    }
};
