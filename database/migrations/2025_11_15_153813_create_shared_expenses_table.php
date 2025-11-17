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
        Schema::create('shared_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('expense_name');
            $table->text('description')->nullable();
            $table->decimal('total_amount', 15, 2);
            $table->string('category');
            $table->date('expense_date');
            $table->json('participants'); // array of family member IDs and their share amounts
            $table->string('split_method'); // equal, percentage, custom
            $table->boolean('is_settled')->default(false);
            $table->date('settlement_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_expenses');
    }
};
