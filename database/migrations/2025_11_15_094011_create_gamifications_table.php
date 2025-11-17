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
        Schema::create('gamifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points')->default(0);
            $table->integer('level')->default(1);
            $table->json('badges')->nullable(); // array of earned badges
            $table->json('achievements')->nullable(); // array of unlocked achievements
            $table->integer('streak_days')->default(0); // current login/activity streak
            $table->date('last_activity')->nullable();
            $table->json('progress')->nullable(); // progress towards next level/badge
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gamifications');
    }
};
