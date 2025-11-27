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
        Schema::table('bank_integrations', function (Blueprint $table) {
            // Add account relationship
            $table->foreignId('account_id')->after('user_id')->constrained()->onDelete('cascade');

            // Ensure balance fields exist (in case they were dropped)
            if (! Schema::hasColumn('bank_integrations', 'current_balance')) {
                $table->decimal('current_balance', 15, 2)->nullable()->after('is_active');
            }
            if (! Schema::hasColumn('bank_integrations', 'available_balance')) {
                $table->decimal('available_balance', 15, 2)->nullable()->after('current_balance');
            }
            if (! Schema::hasColumn('bank_integrations', 'last_balance_sync_at')) {
                $table->timestamp('last_balance_sync_at')->nullable()->after('last_sync_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_integrations', function (Blueprint $table) {
            if (Schema::hasColumn('bank_integrations', 'account_id')) {
                $table->dropForeign(['account_id']);
                $table->dropColumn('account_id');
            }
        });
    }
};
