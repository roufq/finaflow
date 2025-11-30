<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'deactivation_message')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->text('deactivation_message')->nullable()->after('is_active');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'deactivation_message')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropColumn('deactivation_message');
            });
        }
    }
};
