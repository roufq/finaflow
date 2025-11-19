<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\EducationModule;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\LearningPath;
use App\Models\SpendingTrigger;
use App\Models\Transaction;
use App\Models\User;
use App\Services\LearningPathPersonalizer;
use Database\Seeders\EducationModuleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EducationPersonalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_learning_path_personalizer_prioritizes_budget_relief_modules(): void
    {
        $this->seed(EducationModuleSeeder::class);

        $user = User::factory()->create();
        $this->actingAs($user);
        $incomeCategory = Category::create([
            'user_id' => $user->id,
            'name' => 'Salary',
            'type' => 'income',
            'description' => 'Primary income',
        ]);
        $expenseCategory = Category::create([
            'user_id' => $user->id,
            'name' => 'Lifestyle',
            'type' => 'expense',
            'description' => 'Everyday spending',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $incomeCategory->id,
            'transaction_date' => now(),
            'type' => 'income',
            'amount' => 5_000_000,
            'description' => 'Income',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'category_id' => $expenseCategory->id,
            'transaction_date' => now(),
            'type' => 'expense',
            'amount' => 6_000_000,
            'description' => 'Overspending',
        ]);

        Goal::create([
            'user_id' => $user->id,
            'name' => 'Pay Credit Card',
            'target_amount' => 10_000_000,
            'current_amount' => 1_000_000,
            'category' => 'debt_payoff',
            'type' => 'short_term',
            'target_date' => now()->addMonths(6),
            'status' => 'active',
        ]);

        Habit::create([
            'user_id' => $user->id,
            'habit_name' => 'Weekly Saving',
            'category' => 'saving',
            'target_amount' => 250_000,
            'current_streak' => 2,
            'best_streak' => 4,
            'start_date' => now()->subWeeks(4),
        ]);

        SpendingTrigger::create([
            'user_id' => $user->id,
            'trigger_type' => 'shopping',
            'description' => 'Flash sale purchases',
            'frequency' => 3,
        ]);

        $modules = EducationModule::all();
        $learningPath = LearningPath::create([
            'user_id' => $user->id,
            'education_module_id' => $modules->first()->id,
            'progress' => 100,
            'started_at' => now()->subDays(5),
            'completed_at' => now()->subDays(1),
        ]);

        $learningPaths = collect([$learningPath])->keyBy('education_module_id');

        $service = app(LearningPathPersonalizer::class);
        $recommendations = $service->recommend($modules, $learningPaths, $user->id);

        $this->assertNotNull($recommendations['next_module']);
        $this->assertContains($recommendations['next_module']->category, ['budgeting', 'debt']);
        $this->assertTrue(
            $recommendations['focus_modules']->contains(fn ($module) => in_array($module->category, ['budgeting', 'saving']))
        );
    }

    public function test_financial_news_command_populates_articles(): void
    {
        $this->seed(EducationModuleSeeder::class);

        $this->artisan('financial:sync-news')
            ->assertExitCode(0);

        $this->assertDatabaseHas('financial_news', ['category' => 'budgeting']);
    }
}
