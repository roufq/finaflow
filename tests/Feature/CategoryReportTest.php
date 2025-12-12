<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_report_respects_period_filter(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 12, 15));
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $food = Category::factory()->for($user)->create(['name' => 'Makan']);
        $travel = Category::factory()->for($user)->create(['name' => 'Travel']);

        Transaction::factory()->for($user)->for($account)->for($food)->create([
            'transaction_date' => CarbonImmutable::now()->startOfMonth()->addDay(),
            'type' => 'expense',
            'amount' => 250000,
        ]);

        Transaction::factory()->for($user)->for($account)->for($food)->create([
            'transaction_date' => CarbonImmutable::now()->subMonth()->startOfMonth()->addDay(),
            'type' => 'expense',
            'amount' => 100000,
        ]);

        Transaction::factory()->for($user)->for($account)->for($travel)->create([
            'transaction_date' => CarbonImmutable::now()->startOfMonth()->addDays(2),
            'type' => 'expense',
            'amount' => 50000,
        ]);

        $response = $this->actingAs($user)->get(route('reports.categories.index', [
            'period' => 'this_month',
        ]));

        $response->assertOk()->assertViewIs('reports.categories')->assertViewHas('summary', function ($summary) {
            $foodSummary = $summary->firstWhere('category_name', 'Makan');
            $travelSummary = $summary->firstWhere('category_name', 'Travel');

            return $foodSummary
                && $travelSummary
                && $foodSummary['expense_total'] === 250000.0
                && $foodSummary['transactions_count'] === 1
                && $travelSummary['expense_total'] === 50000.0;
        });
    }

    public function test_category_report_detail_endpoint_matches_summary(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 12, 15));
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $food = Category::factory()->for($user)->create(['name' => 'Makan']);

        Transaction::factory()->for($user)->for($account)->for($food)->create([
            'transaction_date' => CarbonImmutable::now()->startOfMonth()->addDay(),
            'type' => 'expense',
            'amount' => 250000,
        ]);

        Transaction::factory()->for($user)->for($account)->for($food)->create([
            'transaction_date' => CarbonImmutable::now()->subMonth()->startOfMonth()->addDay(),
            'type' => 'expense',
            'amount' => 75000,
        ]);

        $response = $this->actingAs($user)->getJson(route('reports.categories.detail', $food, [
            'period' => 'this_month',
        ]));

        $response->assertOk()
            ->assertJsonPath('category.name', 'Makan')
            ->assertJsonPath('totals.transactions', 1)
            ->assertJsonPath('totals.amount', 250000);
    }

    public function test_category_report_pdf_export_is_generated(): void
    {
        CarbonImmutable::setTestNow(CarbonImmutable::create(2024, 12, 15));
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $category = Category::factory()->for($user)->create(['name' => 'Utilities']);

        Transaction::factory()->for($user)->for($account)->for($category)->create([
            'transaction_date' => CarbonImmutable::now()->startOfMonth()->addDay(),
            'type' => 'expense',
            'amount' => 150000,
        ]);

        $response = $this->actingAs($user)->get(route('reports.categories.export', [
            'period' => 'this_month',
        ]));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
        $this->assertStringContainsString('category-report.pdf', $response->headers->get('content-disposition'));
    }
}
