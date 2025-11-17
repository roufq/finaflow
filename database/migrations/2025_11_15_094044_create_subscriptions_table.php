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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('provider');
            $table->decimal('amount', 10, 2);
            $table->string('frequency'); // monthly, yearly, weekly, etc.
            $table->date('next_billing_date');
            $table->string('category')->nullable(); // entertainment, productivity, finance, etc.
            $table->boolean('auto_renewal')->default(true);
            $table->string('status')->default('active'); // active, cancelled, paused
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // additional data like trial end, discount codes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
