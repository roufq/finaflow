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
        Schema::create('loyalty_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('program_name'); // airline, hotel, retail program name
            $table->string('program_type'); // airline, hotel, credit_card, retail
            $table->decimal('points_balance', 15, 2)->default(0);
            $table->string('tier_level')->nullable(); // silver, gold, platinum, etc.
            $table->json('benefits')->nullable(); // array of benefits like lounge access, bonus points
            $table->date('expiry_date')->nullable();
            $table->string('membership_number')->nullable();
            $table->json('redemption_history')->nullable(); // history of point redemptions
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_programs');
    }
};
