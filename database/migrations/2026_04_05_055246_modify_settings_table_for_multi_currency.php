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
        Schema::table('settings', function (Blueprint $table) {
            // Drop foreign key and unique index first
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);

            // Re-add foreign key as a standard index (non-unique)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Add metadata columns for multi-currency support
            $table->string('label')->default('Default')->after('user_id');
            $table->decimal('exchange_rate', 15, 6)->default(1.000000)->after('currency_symbol');
            $table->boolean('is_default')->default(false)->after('exchange_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // This is harder to reverse strictly, but we'll try
            // We can't really restore unique constraint if there's now duplicate data
            $table->dropForeign(['user_id']);
            $table->unique('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->dropColumn(['label', 'exchange_rate', 'is_default']);
        });
    }
};
