<?php

namespace App\Http\Controllers;

use App\Models\ActionPlan;
use App\Models\ActionPlanTask;
use App\Models\Budget;
use App\Models\FinancialJournalEntry;
use App\Models\FinancialPersonality;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\MicroLearning;
use App\Models\MicroLearningProgress;
use App\Models\SpendingTrigger;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FinancialCoachingController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $userId = $user->id;
        $planMonth = $request->input('plan_month')
            ? Carbon::parse($request->input('plan_month'))->startOfMonth()
            : null;

        $actionPlanQuery = ActionPlan::with('tasks')
            ->where('user_id', $userId)
            ->orderByDesc('plan_month');

        if ($planMonth) {
            $actionPlanQuery->whereDate('plan_month', $planMonth);
        }

        $currentPlan = $actionPlanQuery->first();
        $persona = FinancialPersonality::where('user_id', $userId)->latest('assessment_date')->first();
        $personaTags = $this->extractPersonaTags($persona);

        $lessons = $this->loadRecommendedLessons($personaTags);
        $lessonProgress = MicroLearningProgress::where('user_id', $userId)
            ->get()
            ->keyBy('micro_learning_id');

        $journalEntries = FinancialJournalEntry::with(['habit', 'spendingTrigger'])
            ->where('user_id', $userId)
            ->latest('logged_at')
            ->limit(5)
            ->get();

        $habits = Habit::where('user_id', $userId)->orderBy('habit_name')->get();
        $triggers = SpendingTrigger::where('user_id', $userId)->orderBy('trigger_type')->get();

        $suggestedPriorities = $this->determineFocusPriorities($userId);
        $recommendedActions = $this->buildRecommendedActions($userId, $suggestedPriorities);

        return view('coaching.index', [
            'user' => $user,
            'currentPlan' => $currentPlan,
            'suggestedPriorities' => $suggestedPriorities,
            'recommendedActions' => $recommendedActions,
            'lessons' => $lessons,
            'lessonProgress' => $lessonProgress,
            'journalEntries' => $journalEntries,
            'persona' => $persona,
            'habits' => $habits,
            'triggers' => $triggers,
            'planMonth' => $planMonth,
        ]);
    }

    public function storeActionPlan(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plan_month' => ['required', 'date'],
            'summary_notes' => ['nullable', 'string'],
            'savings_target' => ['nullable', 'integer', 'min:0'],
            'debt_repayment_target' => ['nullable', 'integer', 'min:0'],
            'investment_target' => ['nullable', 'integer', 'min:0'],
            'generate_tasks' => ['nullable', 'boolean'],
        ]);

        $userId = Auth::id();
        $planMonth = Carbon::parse($data['plan_month'])->startOfMonth();
        $priorities = $this->determineFocusPriorities($userId);
        $recommendations = $this->buildRecommendedActions($userId, $priorities);

        $plan = ActionPlan::updateOrCreate(
            [
                'user_id' => $userId,
                'plan_month' => $planMonth,
            ],
            [
                'focus_priorities' => $priorities,
                'recommended_actions' => $recommendations,
                'summary_notes' => $data['summary_notes'] ?? null,
                'savings_target' => $data['savings_target'] ?? 0,
                'debt_repayment_target' => $data['debt_repayment_target'] ?? 0,
                'investment_target' => $data['investment_target'] ?? 0,
            ]
        );

        if ($request->boolean('generate_tasks')) {
            $this->generateWeeklyTasks($plan, $recommendations);
        }

        return back()->with('success', __('coaching.messages.plan_saved'));
    }

    public function storeTask(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'action_plan_id' => ['required', 'exists:action_plans,id'],
            'title' => ['required', 'string', 'max:255'],
            'week_index' => ['nullable', 'integer', 'min:1', 'max:6'],
            'due_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $plan = ActionPlan::where('id', $data['action_plan_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $plan->tasks()->create([
            'title' => $data['title'],
            'week_index' => $data['week_index'] ?? 1,
            'due_date' => $data['due_date'] ?? $plan->plan_month->copy()->addWeeks(($data['week_index'] ?? 1) - 1)->endOfWeek(),
            'reminder_at' => $plan->plan_month->copy()->addWeeks(($data['week_index'] ?? 1) - 1)->subDay()->setTime(8, 0),
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success', __('coaching.messages.task_added'));
    }

    public function updateTask(ActionPlanTask $task, Request $request): RedirectResponse
    {
        abort_if($task->actionPlan->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed,skipped'],
            'notes' => ['nullable', 'string'],
        ]);

        $task->update($data);

        return back()->with('success', __('coaching.messages.task_updated'));
    }

    public function updateLessonProgress(MicroLearning $lesson, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:not_started,in_progress,completed'],
            'comprehension_score' => ['nullable', 'integer', 'between:0,100'],
        ]);

        MicroLearningProgress::updateOrCreate(
            [
                'micro_learning_id' => $lesson->id,
                'user_id' => Auth::id(),
            ],
            [
                'status' => $data['status'],
                'comprehension_score' => $data['comprehension_score'],
                'last_accessed_at' => now(),
                'completed_at' => $data['status'] === 'completed' ? now() : null,
            ]
        );

        return back()->with('success', __('coaching.messages.lesson_progressed'));
    }

    public function storeJournalEntry(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reflection' => ['required', 'string'],
            'commitment' => ['nullable', 'string'],
            'mood' => ['nullable', 'string', 'max:50'],
            'habit_id' => ['nullable', 'exists:habits,id'],
            'spending_trigger_id' => ['nullable', 'exists:spending_triggers,id'],
        ]);

        FinancialJournalEntry::create([
            'user_id' => Auth::id(),
            'habit_id' => $data['habit_id'] ?? null,
            'spending_trigger_id' => $data['spending_trigger_id'] ?? null,
            'reflection' => $data['reflection'],
            'commitment' => $data['commitment'] ?? null,
            'mood' => $data['mood'] ?? null,
            'insights' => $this->buildJournalInsights($data),
        ]);

        return back()->with('success', __('coaching.messages.journal_saved'));
    }

    public function exportSummary(): StreamedResponse
    {
        $user = Auth::user();
        $plan = ActionPlan::with('tasks')->where('user_id', $user->id)->orderByDesc('plan_month')->first();
        $lessons = MicroLearningProgress::with('lesson')
            ->where('user_id', $user->id)
            ->latest('updated_at')
            ->get();
        $journals = FinancialJournalEntry::where('user_id', $user->id)->latest('logged_at')->limit(20)->get();

        $summary = [
            'generated_at' => now()->toIso8601String(),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'action_plan' => $plan ? [
                'month' => $plan->month_label,
                'focus_priorities' => $plan->focus_priorities,
                'targets' => [
                    'savings' => $plan->savings_target,
                    'debt' => $plan->debt_repayment_target,
                    'investment' => $plan->investment_target,
                ],
                'tasks' => $plan->tasks->map(fn (ActionPlanTask $task) => [
                    'title' => $task->title,
                    'status' => $task->status,
                    'due_date' => optional($task->due_date)->toDateString(),
                ]),
            ] : null,
            'lesson_progress' => $lessons->map(fn (MicroLearningProgress $progress) => [
                'lesson' => $progress->lesson?->title,
                'status' => $progress->status,
                'score' => $progress->comprehension_score,
                'completed_at' => optional($progress->completed_at)->toIso8601String(),
            ]),
            'journal_entries' => $journals->map(fn (FinancialJournalEntry $entry) => [
                'logged_at' => $entry->logged_at->toIso8601String(),
                'reflection' => Str::limit($entry->reflection, 200),
                'commitment' => $entry->commitment,
                'mood' => $entry->mood,
            ]),
        ];

        $fileName = 'coaching-summary-'.now()->format('Ymd_His').'.json';

        return response()->streamDownload(function () use ($summary) {
            echo json_encode($summary, JSON_PRETTY_PRINT);
        }, $fileName, [
            'Content-Type' => 'application/json',
        ]);
    }

    protected function determineFocusPriorities(int $userId): array
    {
        $windowStart = now()->subDays(90);
        $income = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->where('transaction_date', '>=', $windowStart)
            ->sum('amount');
        $expenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->where('transaction_date', '>=', $windowStart)
            ->sum('amount');

        $priorities = collect();

        if ($expenses > $income) {
            $priorities->push('expense_control');
        }

        $debtGoals = Goal::where('user_id', $userId)->where('category', 'debt_payment')->exists();
        if ($debtGoals) {
            $priorities->push('debt_repayment');
        }

        $savingsGoals = Goal::where('user_id', $userId)->whereIn('category', ['emergency', 'savings'])->exists();
        if ($savingsGoals || $income - $expenses < ($income * 0.2)) {
            $priorities->push('emergency_savings');
        }

        $investmentGoals = Goal::where('user_id', $userId)->where('category', 'investment')->exists();
        if ($investmentGoals) {
            $priorities->push('long_term_investment');
        }

        if ($priorities->isEmpty()) {
            $priorities->push('financial_habits');
        }

        return $priorities->unique()->values()->all();
    }

    protected function buildRecommendedActions(int $userId, array $priorities): array
    {
        $budgetPressure = Budget::where('user_id', $userId)
            ->whereColumn('spent_amount', '>', 'total_budget')
            ->exists();

        $actions = collect();

        if (in_array('expense_control', $priorities, true) || $budgetPressure) {
            $actions->push([
                'title' => __('coaching.actions.review_spending'),
                'description' => __('coaching.actions.review_spending_desc'),
            ]);
        }

        if (in_array('debt_repayment', $priorities, true)) {
            $actions->push([
                'title' => __('coaching.actions.debt_snowball'),
                'description' => __('coaching.actions.debt_snowball_desc'),
            ]);
        }

        if (in_array('emergency_savings', $priorities, true)) {
            $actions->push([
                'title' => __('coaching.actions.emergency_savings'),
                'description' => __('coaching.actions.emergency_savings_desc'),
            ]);
        }

        if (in_array('long_term_investment', $priorities, true)) {
            $actions->push([
                'title' => __('coaching.actions.investment_review'),
                'description' => __('coaching.actions.investment_review_desc'),
            ]);
        }

        $actions->push([
            'title' => __('coaching.actions.weekly_checkin'),
            'description' => __('coaching.actions.weekly_checkin_desc'),
        ]);

        return $actions->take(4)->values()->all();
    }

    protected function generateWeeklyTasks(ActionPlan $plan, array $recommendedActions): void
    {
        if ($plan->tasks()->exists()) {
            return;
        }

        $actions = collect($recommendedActions);
        for ($week = 1; $week <= 4; $week++) {
            $action = $actions->get($week - 1, $actions->first());
            $dueDate = $plan->plan_month->copy()->addWeeks($week - 1)->endOfWeek();

            $plan->tasks()->create([
                'title' => $action['title'] ?? __('coaching.actions.weekly_focus'),
                'week_index' => $week,
                'due_date' => $dueDate,
                'reminder_at' => $dueDate->copy()->subDays(1)->setTime(8, 0),
                'notes' => $action['description'] ?? null,
            ]);
        }
    }

    protected function loadRecommendedLessons(Collection $personaTags): Collection
    {
        $lessons = MicroLearning::active();

        if ($personaTags->isNotEmpty()) {
            $lessons->where(function ($query) use ($personaTags) {
                foreach ($personaTags as $tag) {
                    $query->orWhereJsonContains('persona_tags', $tag);
                }
            });
        }

        return $lessons->orderBy('difficulty')->limit(8)->get();
    }

    protected function extractPersonaTags(?FinancialPersonality $persona): Collection
    {
        if (! $persona) {
            return collect();
        }

        return collect([
            $persona->personality_type,
            $persona->spending_style,
        ])->filter();
    }

    protected function buildJournalInsights(array $data): array
    {
        $insights = [];

        if (! empty($data['mood'])) {
            $insights[] = __('coaching.journal.mood_note', ['mood' => $data['mood']]);
        }

        if (! empty($data['commitment'])) {
            $insights[] = __('coaching.journal.commitment_note');
        }

        return $insights;
    }
}
