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
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('habit_name');
            $table->string('category'); // saving, spending_control, budgeting, investing
            $table->decimal('target_amount', 15, 2)->nullable(); // target savings per period
            $table->integer('current_streak')->default(0); // consecutive days/weeks achieved
            $table->integer('best_streak')->default(0); // best streak ever
            $table->date('start_date');
            $table->date('last_achieved')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('progress_data')->nullable(); // track progress over time
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
