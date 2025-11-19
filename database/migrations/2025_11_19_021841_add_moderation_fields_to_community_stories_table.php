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
        Schema::table('community_stories', function (Blueprint $table) {
            $table->foreignId('moderated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('status');
            $table->timestamp('moderated_at')->nullable()->after('moderated_by');
            $table->json('moderation_flags')->nullable()->after('moderated_at');
            $table->text('moderator_notes')->nullable()->after('moderation_flags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('community_stories', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
            $table->dropColumn(['moderated_by', 'moderated_at', 'moderation_flags', 'moderator_notes']);
        });
    }
};
