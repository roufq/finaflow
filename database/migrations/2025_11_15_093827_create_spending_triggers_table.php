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
        Schema::create('spending_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('trigger_type'); // emotional, social, habitual, situational
            $table->text('description');
            $table->integer('frequency')->default(0); // how many times triggered
            $table->decimal('amount_threshold', 15, 2)->nullable(); // spending amount that triggers
            $table->json('metadata')->nullable(); // additional data like time, location, category
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spending_triggers');
    }
};
