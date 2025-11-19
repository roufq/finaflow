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
        Schema::create('micro_learnings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('format')->default('article'); // article, video, quiz
            $table->string('language', 5)->default('id');
            $table->unsignedInteger('duration_minutes')->default(5);
            $table->string('difficulty')->default('beginner');
            $table->text('summary')->nullable();
            $table->text('content')->nullable();
            $table->string('content_url')->nullable();
            $table->json('persona_tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('micro_learnings');
    }
};
