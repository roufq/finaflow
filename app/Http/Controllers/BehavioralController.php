<?php

namespace App\Http\Controllers;

use App\Models\SpendingTrigger;
use App\Models\Habit;
use App\Models\FinancialPersonality;
use App\Models\Gamification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BehavioralController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $spendingTriggers = SpendingTrigger::where('user_id', $userId)->get();
        $habits = Habit::where('user_id', $userId)->where('is_active', true)->get();
        $personality = FinancialPersonality::where('user_id', $userId)->latest()->first();
        $gamification = Gamification::where('user_id', $userId)->first();

        return view('behavioral.index', compact('spendingTriggers', 'habits', 'personality', 'gamification'));
    }

    public function triggers()
    {
        $triggers = SpendingTrigger::where('user_id', Auth::id())->get();
        return view('behavioral.triggers', compact('triggers'));
    }

    public function createTrigger()
    {
        return view('behavioral.create-trigger');
    }

    public function storeTrigger(Request $request)
    {
        $request->validate([
            'trigger_type' => 'required|string',
            'description' => 'required|string',
            'amount_threshold' => 'nullable|numeric|min:0',
        ]);

        SpendingTrigger::create([
            'user_id' => Auth::id(),
            'trigger_type' => $request->trigger_type,
            'description' => $request->description,
            'amount_threshold' => $request->amount_threshold,
            'frequency' => 0,
        ]);

        return redirect()->route('behavioral.triggers')->with('success', __('behavioral.messages.trigger_created'));
    }

    public function habits()
    {
        $habits = Habit::where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        return view('behavioral.habits', compact('habits'));
    }

    public function createHabit()
    {
        return view('behavioral.create-habit');
    }

    public function storeHabit(Request $request)
    {
        $request->validate([
            'habit_name' => 'required|string',
            'category' => 'required|string',
            'target_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
        ]);

        Habit::create([
            'user_id' => Auth::id(),
            'habit_name' => $request->habit_name,
            'category' => $request->category,
            'target_amount' => $request->target_amount,
            'start_date' => $request->start_date,
            'current_streak' => 0,
            'best_streak' => 0,
            'is_active' => true,
        ]);

        return redirect()->route('behavioral.habits')->with('success', __('behavioral.messages.habit_created'));
    }

    public function markHabitAchieved(Habit $habit)
    {
        if ($habit->user_id !== Auth::id()) {
            abort(403);
        }

        $habit->markAchieved();

        return redirect()->back()->with('success', __('behavioral.messages.habit_marked'));
    }

    public function personality()
    {
        $personality = FinancialPersonality::where('user_id', Auth::id())->latest()->first();
        return view('behavioral.personality', compact('personality'));
    }

    public function takePersonalityQuiz()
    {
        return view('behavioral.quiz');
    }

    public function storePersonalityQuiz(Request $request)
    {
        $request->validate([
            'personality_type' => 'required|string',
            'risk_tolerance' => 'required|integer|min:1|max:10',
            'spending_style' => 'required|string',
            'saving_habits' => 'required|string',
            'scores' => 'required|array',
        ]);

        FinancialPersonality::create([
            'user_id' => Auth::id(),
            'personality_type' => $request->personality_type,
            'risk_tolerance' => $request->risk_tolerance,
            'spending_style' => $request->spending_style,
            'saving_habits' => $request->saving_habits,
            'scores' => $request->scores,
            'assessment_date' => now(),
            'recommendations' => $this->generateRecommendations($request->personality_type),
        ]);

        return redirect()->route('behavioral.personality')->with('success', __('behavioral.messages.personality_saved'));
    }

    public function gamification()
    {
        $gamification = Gamification::where('user_id', Auth::id())->first();
        return view('behavioral.gamification', compact('gamification'));
    }

    private function generateRecommendations(string $personalityType): array
    {
        $recommendations = [
            'spender' => [
                'Set up automatic savings transfers to build emergency funds.',
                'Use cash instead of cards for discretionary spending.',
                'Track your spending for one week to identify patterns.',
            ],
            'saver' => [
                'Consider diversifying investments to maximize returns.',
                'Allow yourself some discretionary spending for enjoyment.',
                'Set up automatic bill payments to avoid late fees.',
            ],
            'investor' => [
                'Regularly review and rebalance your investment portfolio.',
                'Consider tax-advantaged investment accounts.',
                'Educate yourself about different investment options.',
            ],
            'avoider' => [
                'Set up automatic bill payments to avoid late fees.',
                'Create simple budgeting rules to reduce decision fatigue.',
                'Start with small financial goals to build confidence.',
            ],
            'balanced' => [
                'Continue your balanced approach to financial management.',
                'Regularly review your financial goals and progress.',
                'Consider consulting with a financial advisor.',
            ],
        ];

        return $recommendations[$personalityType] ?? [];
    }
}
