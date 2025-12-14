<?php

namespace Tests\Feature;

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
        $user = \App\Models\User::factory()->create();
        $user->assignRole('user');
        \App\Models\Account::factory()->for($user)->create();

        $response = $this->actingAs($user)->get('/transactions');

        $response->assertStatus(200);
        $response->assertViewIs('transactions.index');
    }

    public function test_api_transactions_returns_json_for_user(): void
    {
        $user = \App\Models\User::factory()->create();
        $user->assignRole('user');
        \App\Models\Transaction::factory()->for($user)->for(\App\Models\Account::factory()->for($user))->for(\App\Models\Category::factory()->for($user))->create();

        $response = $this->actingAs($user)->get('/api/v1/transactions');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }
}
