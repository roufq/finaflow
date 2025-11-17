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
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('symbol', 10); // Stock ticker, crypto symbol, etc.
            $table->string('name'); // Full name of the investment
            $table->enum('type', ['stock', 'mutual_fund', 'crypto', 'bond', 'etf', 'other']);
            $table->decimal('quantity', 15, 8); // Support for fractional shares
            $table->decimal('purchase_price', 15, 4); // Price per unit at purchase
            $table->decimal('current_price', 15, 4)->nullable(); // Current market price
            $table->date('purchase_date');
            $table->decimal('dividends_received', 15, 2)->default(0); // Total dividends received
            $table->decimal('fees', 15, 2)->default(0); // Transaction fees
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
