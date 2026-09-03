<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_transactions_requires_authentication(): void
    {
        $response = $this->get('/transactions');

        $response->assertRedirect('/login');
    }

    public function test_transactions_index_displays_for_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertStatus(200);
        $response->assertViewIs('transactions.index');
    }

    public function test_api_transactions_returns_json_for_user(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');
        Transaction::factory()->for($user)->for(Account::factory()->for($user))->for(Category::factory()->for($user))->create();

        $response = $this->actingAs($user)->get('/api/v1/transactions');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    public function test_api_transaction_store_returns_created_status(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['balance' => 100000]);
        $category = Category::factory()->for($user)->create(['type' => 'expense']);

        $response = $this->actingAs($user)->postJson('/api/v1/transactions', [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'transaction_date' => now()->toDateString(),
            'type' => 'expense',
            'amount' => 25000,
            'description' => 'Lunch',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Transaction created successfully');
    }
}
