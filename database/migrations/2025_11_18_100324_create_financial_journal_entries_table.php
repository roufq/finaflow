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
        Schema::create('financial_journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('habit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('spending_trigger_id')->nullable()->constrained()->nullOnDelete();
            $table->string('mood')->nullable();
            $table->text('reflection');
            $table->text('commitment')->nullable();
            $table->json('insights')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_journal_entries');
    }
};
