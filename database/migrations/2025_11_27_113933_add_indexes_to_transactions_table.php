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
        Schema::table('transactions', function (Blueprint $table) {
            $table->index(['user_id', 'transaction_date'], 'transactions_user_date_index');
            $table->index(['type', 'transaction_date'], 'transactions_type_date_index');
            $table->index(['account_id', 'transaction_date'], 'transactions_account_date_index');
            $table->index(['category_id', 'transaction_date'], 'transactions_category_date_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_user_date_index');
            $table->dropIndex('transactions_type_date_index');
            $table->dropIndex('transactions_account_date_index');
            $table->dropIndex('transactions_category_date_index');
        });
    }
};
