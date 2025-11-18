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
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('anomaly_type'); // 'unusual_amount', 'unusual_category', 'unusual_time', 'duplicate', 'suspicious_pattern'
            $table->text('description');
            $table->integer('severity')->default(1); // 1=low, 2=medium, 3=high
            $table->boolean('is_resolved')->default(false);
            $table->json('metadata')->nullable(); // Additional anomaly data
            $table->timestamp('detected_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anomalies');
    }
};
