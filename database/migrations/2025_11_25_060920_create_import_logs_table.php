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
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('bank_integration_id')->constrained()->onDelete('cascade');
            $table->string('status'); // success, failed
            $table->string('source')->nullable(); // api, csv, ofx, manual
            $table->string('message')->nullable();
            $table->json('context')->nullable(); // details: counts, errors
            $table->timestamps();

            $table->index(['user_id', 'bank_integration_id']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_logs');
    }
};
