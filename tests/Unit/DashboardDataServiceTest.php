<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use App\Services\Dashboard\DashboardDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardDataServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_monthly_totals_sums_income_and_expense(): void
    {
        $service = $this->app->make(DashboardDataService::class);
        $user = User::factory()->create();
        $this->actingAs($user);

        Transaction::factory()->for($user)->create([
            'type' => 'income',
            'amount' => 10000,
            'transaction_date' => now()->startOfMonth()->addDay(),
        ]);

        Transaction::factory()->for($user)->create([
            'type' => 'expense',
            'amount' => 4000,
            'transaction_date' => now()->startOfMonth()->addDay(),
        ]);

        $totals = $service->getMonthlyTotals($user->id, now()->startOfMonth());

        $this->assertEquals(10000.0, $totals['income']);
        $this->assertEquals(4000.0, $totals['expense']);
        $this->assertEquals(6000.0, $totals['net']);
    }
}
