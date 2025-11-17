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
            $table->decimal('current_balance', 15, 2)->nullable()->after('is_active');
            $table->decimal('available_balance', 15, 2)->nullable()->after('current_balance');
            $table->timestamp('last_balance_sync_at')->nullable()->after('last_sync_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_integrations', function (Blueprint $table) {
            $table->dropColumn(['current_balance', 'available_balance', 'last_balance_sync_at']);
        });
    }
};
