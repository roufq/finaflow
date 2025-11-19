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
        Schema::create('action_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('plan_month');
            $table->json('focus_priorities')->nullable();
            $table->json('recommended_actions')->nullable();
            $table->text('summary_notes')->nullable();
            $table->unsignedInteger('savings_target')->default(0);
            $table->unsignedInteger('debt_repayment_target')->default(0);
            $table->unsignedInteger('investment_target')->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'plan_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_plans');
    }
};
