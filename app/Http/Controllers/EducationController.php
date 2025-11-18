<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\EducationModule;
use App\Models\FinancialNews;
use App\Models\Goal;
use App\Models\LearningPath;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EducationController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $modules = EducationModule::active()->get();
        $learningPaths = LearningPath::with('module')
            ->where('user_id', $userId)
            ->get()
            ->keyBy('education_module_id');
        $news = FinancialNews::recent()->limit(4)->get();

        $progress = $this->progressOverview($learningPaths, $modules);
        $recommendations = $this->recommendModules($modules, $learningPaths, $userId);
        $communityHighlights = $this->communityHighlights();

        return view('education.index', compact('modules', 'learningPaths', 'news', 'progress', 'recommendations', 'communityHighlights'));
    }

    public function showModule(EducationModule $module): View
    {
        if (! $module->is_active) {
            abort(404);
        }

        $path = LearningPath::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'education_module_id' => $module->id,
            ],
            [
                'started_at' => now(),
                'recommendations' => [],
            ]
        );

        $relatedModules = EducationModule::where('category', $module->category)
            ->where('id', '!=', $module->id)
            ->limit(3)
            ->get();

        return view('education.module', compact('module', 'path', 'relatedModules'));
    }

    public function updateProgress(Request $request, EducationModule $module): RedirectResponse
    {
        $data = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $path = LearningPath::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'education_module_id' => $module->id,
            ],
            [
                'started_at' => now(),
            ]
        );

        $path->markProgress($data['progress']);

        return back()->with('success', __('education.messages.progress_updated'));
    }

    public function news(): View
    {
        $news = FinancialNews::recent()->paginate(10);

        return view('education.news', compact('news'));
    }

    protected function progressOverview(Collection $learningPaths, Collection $modules): array
    {
        $completed = $learningPaths->where('progress', '>=', 100)->count();
        $inProgress = $learningPaths->where('progress', '>', 0)->where('progress', '<', 100)->count();

        return [
            'completed' => $completed,
            'in_progress' => $inProgress,
            'total_modules' => $modules->count(),
            'completion_rate' => $modules->count() ? round(($completed / max(1, $modules->count())) * 100) : 0,
        ];
    }

    protected function recommendModules(Collection $modules, Collection $learningPaths, int $userId): array
    {
        $completedIds = $learningPaths->where('progress', '>=', 100)->keys();
        $focusAreas = $this->determineFocusAreas($userId);

        $byCategory = $focusAreas->map(function ($category) use ($modules, $completedIds) {
            return $modules->first(function ($module) use ($category, $completedIds) {
                return $module->category === $category && ! $completedIds->contains($module->id);
            });
        })->filter();

        $nextModule = $modules->first(function ($module) use ($completedIds) {
            return ! $completedIds->contains($module->id);
        });

        return [
            'focus_modules' => $byCategory,
            'next_module' => $nextModule,
        ];
    }

    protected function determineFocusAreas(int $userId): Collection
    {
        $income = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $expenses = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');

        $focus = collect();

        if ($expenses > $income) {
            $focus->push('budgeting');
        }

        $debtGoals = Goal::where('user_id', $userId)->where('category', 'debt_payment')->exists();
        if ($debtGoals) {
            $focus->push('debt');
        }

        $investmentGoals = Goal::where('user_id', $userId)->where('category', 'investment')->exists();
        if ($investmentGoals) {
            $focus->push('investing');
        }

        $overBudget = Budget::where('user_id', $userId)
            ->whereColumn('spent_amount', '>', 'total_budget')
            ->exists();
        if ($overBudget) {
            $focus->push('saving');
        }

        if ($focus->isEmpty()) {
            $focus->push('general');
        }

        return $focus->unique();
    }

    protected function communityHighlights(): array
    {
        return [
            [
                'name' => 'Alya',
                'achievement' => 'Closed 3 credit cards in 6 months',
                'tip' => 'Followed debt avalanche plan and negotiated lower interest.',
            ],
            [
                'name' => 'Dimas',
                'achievement' => 'Built 6 month emergency fund',
                'tip' => 'Automated savings transfers every payday with envelope budgeting.',
            ],
            [
                'name' => 'Sari & Budi',
                'achievement' => 'Invested consistently for child education',
                'tip' => 'Used goal-based investing module and monthly review rituals.',
            ],
        ];
    }
}
