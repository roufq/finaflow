<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\EducationModule;
use App\Models\FinancialPersonality;
use App\Models\Goal;
use App\Models\Habit;
use App\Models\SpendingTrigger;
use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LearningPathPersonalizer
{
    public function recommend(Collection $modules, Collection $learningPaths, int $userId): array
    {
        $profile = $this->buildProfile($userId, $learningPaths);
        $focusAreas = $this->determineFocusAreas($profile);
        $profile['focus_categories'] = $focusAreas->all();

        $completedIds = $learningPaths->where('progress', '>=', 100)->keys();
        $rankedModules = $modules
            ->filter(fn (EducationModule $module) => ! $completedIds->contains($module->id))
            ->sortByDesc(fn (EducationModule $module) => $this->scoreModule($module, $profile))
            ->values();

        $focusRecommendations = $focusAreas->map(function (string $area) use ($rankedModules) {
            return $rankedModules->first(function (EducationModule $module) use ($area) {
                $tags = (array) $module->tag_list;

                return $module->category === $area || in_array($area, $tags, true);
            });
        })->filter()->values();

        $refreshModules = $modules
            ->filter(fn (EducationModule $module) => $completedIds->contains($module->id))
            ->sortByDesc(fn (EducationModule $module) => $this->scoreModule($module, $profile))
            ->take(3);

        return [
            'profile' => $profile,
            'focus_modules' => $focusRecommendations,
            'next_module' => $rankedModules->first(),
            'refresh_modules' => $refreshModules,
        ];
    }

    protected function buildProfile(int $userId, Collection $learningPaths): array
    {
        $income = Transaction::where('user_id', $userId)->where('type', 'income')->sum('amount');
        $expenses = Transaction::where('user_id', $userId)->where('type', 'expense')->sum('amount');
        $cashFlowGap = $income - $expenses;
        $savingsRate = $income > 0 ? max(0, $income - $expenses) / $income : 0;

        $habits = Habit::where('user_id', $userId)->get();
        $triggers = SpendingTrigger::where('user_id', $userId)->get();
        $goals = Goal::where('user_id', $userId)->get();
        $personality = FinancialPersonality::where('user_id', $userId)->latest('assessment_date')->first();

        $learningPathModules = $learningPaths
            ->map(function ($path) {
                if ($path->relationLoaded('module')) {
                    return $path->module;
                }

                return $path->module()->first();
            })
            ->filter();

        $preferredLanguage = $learningPathModules
            ->pluck('language')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first() ?? 'id';

        $preferredDifficulty = $learningPathModules
            ->pluck('difficulty')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first() ?? 'beginner';

        $priorityTags = collect();

        if ($cashFlowGap < 0) {
            $priorityTags->push('cash_flow_gap');
        }

        if ($savingsRate < 0.15) {
            $priorityTags->push('saving');
        }

        if ($goals->whereIn('category', ['debt_payment', 'debt_payoff'])->isNotEmpty()) {
            $priorityTags->push('debt');
        }

        if ($goals->whereIn('category', ['investment', 'retirement'])->isNotEmpty()) {
            $priorityTags->push('investing');
        }

        if ($habits->where('category', 'saving')->isNotEmpty()) {
            $priorityTags->push('habits');
        }

        if ($triggers->isNotEmpty()) {
            $priorityTags->push('triggers');
        }

        return [
            'cash_flow_gap' => $cashFlowGap,
            'savings_rate' => round($savingsRate, 3),
            'personality' => $personality?->personality_type,
            'risk_tolerance' => $personality?->risk_tolerance ?? 5,
            'active_habit_categories' => $habits->pluck('category')->filter()->unique()->values()->all(),
            'trigger_topics' => $triggers->pluck('trigger_type')->filter()->unique()->values()->all(),
            'goal_categories' => $goals->pluck('category')->filter()->unique()->values()->all(),
            'budget_pressure' => Budget::where('user_id', $userId)
                ->whereColumn('spent_amount', '>', 'total_budget')
                ->count(),
            'preferred_language' => $preferredLanguage,
            'preferred_difficulty' => $preferredDifficulty,
            'priority_tags' => $priorityTags->unique()->values()->all(),
        ];
    }

    protected function determineFocusAreas(array $profile): Collection
    {
        $focus = collect();

        if ($profile['cash_flow_gap'] < 0 || $profile['budget_pressure'] > 0) {
            $focus->push('budgeting');
        }

        if ($this->containsGoalCategory($profile['goal_categories'], ['debt_payment', 'debt_payoff'])) {
            $focus->push('debt');
        }

        if (in_array('investment', $profile['goal_categories'], true)
            || ($profile['risk_tolerance'] ?? 0) >= 7
        ) {
            $focus->push('investing');
        }

        if ($profile['savings_rate'] < 0.2) {
            $focus->push('saving');
        }

        if (in_array('education', $profile['goal_categories'], true)) {
            $focus->push('family_finance');
        }

        if (! empty($profile['trigger_topics'])) {
            $focus->push('behavioral');
        }

        foreach ($profile['active_habit_categories'] as $habitCategory) {
            $focus->push(Str::slug($habitCategory));
        }

        return $focus->filter()->map(function (string $value) {
            return str_replace('-', '_', $value);
        })->unique()->values();
    }

    /**
     * @param  array<int, string>  $goalCategories
     * @param  array<int, string>  $needles
     */
    protected function containsGoalCategory(array $goalCategories, array $needles): bool
    {
        foreach ($goalCategories as $category) {
            if (in_array($category, $needles, true)) {
                return true;
            }
        }

        return false;
    }

    protected function scoreModule(EducationModule $module, array $profile): float
    {
        $score = 0;
        $tags = (array) $module->tag_list;
        $metadata = (array) $module->metadata;

        if (in_array($module->category, $profile['focus_categories'] ?? [], true)) {
            $score += 35;
        }

        if (in_array($module->language, [$profile['preferred_language']], true)) {
            $score += 10;
        }

        if (in_array($module->difficulty, [$profile['preferred_difficulty']], true)) {
            $score += 5;
        }

        if (array_intersect($tags, $profile['priority_tags'])) {
            $score += 15;
        }

        if (array_intersect($metadata['best_for'] ?? [], $profile['priority_tags'])) {
            $score += 10;
        }

        if (($profile['personality'] ?? null) && in_array(
            $profile['personality'],
            $metadata['persona_focus'] ?? [],
            true
        )) {
            $score += 15;
        }

        if ($profile['cash_flow_gap'] >= 0 && $module->category === 'investing') {
            $score += 5;
        }

        return $score + (100 - min(100, $module->order ?? 50)) * 0.01;
    }
}
