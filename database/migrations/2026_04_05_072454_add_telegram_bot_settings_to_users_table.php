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
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_bot_token')->nullable()->after('telegram_id');
            $table->string('telegram_bot_username')->nullable()->after('telegram_bot_token');
            $table->string('telegram_webhook_token', 64)->nullable()->unique()->after('telegram_bot_username');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_bot_token', 'telegram_bot_username', 'telegram_webhook_token']);
        });
    }
};
