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
        DB::table('transactions')
            ->whereNull('account_id')
            ->orderBy('id')
            ->chunkById(100, function ($transactions) {
                foreach ($transactions as $transaction) {
                    $accountId = DB::table('accounts')
                        ->where('user_id', $transaction->user_id)
                        ->value('id');

                    if (! $accountId) {
                        $accountId = DB::table('accounts')->insertGetId([
                            'user_id' => $transaction->user_id,
                            'name' => 'Default Cash',
                            'type' => 'cash',
                            'balance' => 0,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    DB::table('transactions')
                        ->where('id', $transaction->id)
                        ->update(['account_id' => $accountId]);
                }
            });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->change();
        });
    }
};
