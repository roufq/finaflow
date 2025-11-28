<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            if (! $this->foreignKeyExists('transactions_user_id_foreign')) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }

            if (! $this->foreignKeyExists('transactions_account_id_foreign')) {
                $table->foreign('account_id')->references('id')->on('accounts')->nullOnDelete();
            }

            if (! $this->foreignKeyExists('transactions_category_id_foreign')) {
                $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('transactions', function (Blueprint $table) {
            if ($this->foreignKeyExists('transactions_user_id_foreign')) {
                $table->dropForeign('transactions_user_id_foreign');
            }

            if ($this->foreignKeyExists('transactions_account_id_foreign')) {
                $table->dropForeign('transactions_account_id_foreign');
            }

            if ($this->foreignKeyExists('transactions_category_id_foreign')) {
                $table->dropForeign('transactions_category_id_foreign');
            }
        });
    }

    private function foreignKeyExists(string $constraint): bool
    {
        return DB::table('information_schema.referential_constraints')
            ->where('constraint_schema', DB::getDatabaseName())
            ->where('constraint_name', $constraint)
            ->exists();
    }
};
