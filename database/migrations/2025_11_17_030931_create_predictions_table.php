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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category'); // expense category or 'income'
            $table->decimal('predicted_amount', 15, 2);
            $table->decimal('confidence', 5, 2)->default(0.50); // 0.00 to 1.00
            $table->string('period'); // 'weekly', 'monthly', 'quarterly', 'yearly'
            $table->date('prediction_date'); // Date this prediction is for
            $table->json('factors')->nullable(); // Factors used in prediction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
