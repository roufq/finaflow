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
        Schema::table('education_modules', function (Blueprint $table) {
            $table->string('language', 5)->default('id')->after('difficulty');
            $table->json('tags')->nullable()->after('language');
            $table->json('learning_objectives')->nullable()->after('tags');
            $table->json('resource_links')->nullable()->after('learning_objectives');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('education_modules', function (Blueprint $table) {
            $table->dropColumn(['language', 'tags', 'learning_objectives', 'resource_links']);
        });
    }
};
