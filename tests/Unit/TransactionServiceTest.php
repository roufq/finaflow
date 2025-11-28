<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Finance\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_transaction_updates_balance(): void
    {
        $service = $this->app->make(TransactionService::class);
        $user = User::factory()->create();
        $this->actingAs($user);
        $account = Account::factory()->for($user)->create(['balance' => 0]);
        $category = Category::factory()->for($user)->create();

        $service->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'transaction_date' => now(),
            'type' => 'income',
            'amount' => 5000,
            'description' => 'Test income',
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'income',
            'amount' => 5000,
        ]);

        $this->assertSame(5000.00, (float) $account->fresh()->balance);
    }

    public function test_update_transaction_recalculates_balances(): void
    {
        $service = $this->app->make(TransactionService::class);
        $user = User::factory()->create();
        $this->actingAs($user);
        $account = Account::factory()->for($user)->create(['balance' => 0]);
        $category = Category::factory()->for($user)->create();

        $service->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'transaction_date' => now(),
            'type' => 'income',
            'amount' => 5000,
            'description' => 'Initial',
        ]);

        $transaction = Transaction::firstOrFail();

        $service->update($transaction, [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'transaction_date' => now(),
            'type' => 'expense',
            'amount' => 2000,
            'description' => 'Updated',
        ]);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'type' => 'expense',
            'amount' => 2000,
        ]);

        $this->assertSame(-2000.00, (float) $account->fresh()->balance);
    }

    public function test_delete_transaction_restores_balance(): void
    {
        $service = $this->app->make(TransactionService::class);
        $user = User::factory()->create();
        $this->actingAs($user);
        $account = Account::factory()->for($user)->create(['balance' => 0]);
        $category = Category::factory()->for($user)->create();

        $service->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'transaction_date' => now(),
            'type' => 'expense',
            'amount' => 3000,
            'description' => 'To delete',
        ]);

        $transaction = Transaction::firstOrFail();
        $this->assertSame(-3000.00, (float) $account->fresh()->balance);

        $service->delete($transaction);

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
        $this->assertSame(0.00, (float) $account->fresh()->balance);
    }
}
