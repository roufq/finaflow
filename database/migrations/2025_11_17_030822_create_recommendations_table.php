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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'saving', 'budgeting', 'investment', 'debt', 'general'
            $table->text('content');
            $table->integer('priority')->default(1); // 1=low, 2=medium, 3=high
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable(); // Additional data for the recommendation
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
