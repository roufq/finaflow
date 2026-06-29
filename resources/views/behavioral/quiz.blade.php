@extends('layouts.app')

@section('content')
<div class="w-full">

    <!-- Page Heading -->
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
        <div class="flex items-center gap-4">
            <a href="{{ route('behavioral.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Financial Personality Quiz</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="rounded-3xl bg-white p-6 md:p-8 shadow-premium ring-1 ring-slate-100 mb-8">
                <h2 class="text-xl font-bold text-slate-900 mb-2 border-b border-slate-100 pb-4">Discover Your Financial Personality</h2>
                <p class="text-sm text-slate-600 mb-8 mt-2">Answer these questions to understand your financial behavior patterns and get personalized recommendations.</p>

                <form id="personality-quiz" method="POST" action="{{ route('behavioral.personality.quiz.store') }}" class="space-y-8">
                    @csrf

                    <!-- Question 1 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">1. When you receive a bonus or unexpected income, what do you typically do?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q1" value="spender" required>
                                <span class="text-sm text-slate-700 font-medium">Spend it immediately on something fun</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q1" value="saver">
                                <span class="text-sm text-slate-700 font-medium">Save most of it for future security</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q1" value="investor">
                                <span class="text-sm text-slate-700 font-medium">Invest it for long-term growth</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q1" value="avoider">
                                <span class="text-sm text-slate-700 font-medium">Pay off debts or bills</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">2. How comfortable are you with financial risk?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q2" value="1" required>
                                <span class="text-sm text-slate-700 font-medium">Very conservative - I prefer guaranteed returns</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q2" value="5">
                                <span class="text-sm text-slate-700 font-medium">Moderate - Some risk is okay for better returns</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q2" value="10">
                                <span class="text-sm text-slate-700 font-medium">Very aggressive - High risk for high rewards</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">3. When shopping, you typically:</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q3" value="impulsive" required>
                                <span class="text-sm text-slate-700 font-medium">Buy what you want when you see it</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q3" value="planned">
                                <span class="text-sm text-slate-700 font-medium">Plan purchases and stick to a budget</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q3" value="research">
                                <span class="text-sm text-slate-700 font-medium">Research and compare prices extensively</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 4 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">4. Your approach to saving money is:</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q4" value="minimal" required>
                                <span class="text-sm text-slate-700 font-medium">I save what's left after spending</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q4" value="automatic">
                                <span class="text-sm text-slate-700 font-medium">I set up automatic transfers to savings</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q4" value="strategic">
                                <span class="text-sm text-slate-700 font-medium">I have multiple savings goals and strategies</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 5 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">5. How often do you review your financial situation?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q5" value="rarely" required>
                                <span class="text-sm text-slate-700 font-medium">Rarely - only when there's a problem</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q5" value="monthly">
                                <span class="text-sm text-slate-700 font-medium">Monthly or when bills are due</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q5" value="weekly">
                                <span class="text-sm text-slate-700 font-medium">Weekly or more frequently</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-base font-bold text-white shadow-sm transition-all hover:bg-primary-600 hover:shadow-md active:scale-95">
                            <i class="fas fa-check-circle"></i> Submit Quiz
                        </button>
                        <a href="{{ route('behavioral.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-8 py-4 text-base font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-95">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-3xl bg-slate-50 p-6 shadow-inner ring-1 ring-slate-200/60">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary-500 shadow-sm">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">What You'll Learn</h3>
                </div>
                
                <ul class="space-y-3 mb-2">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-primary-500 mt-1 shrink-0 text-sm"></i>
                        <span class="text-sm text-slate-700">Your dominant financial personality type</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-primary-500 mt-1 shrink-0 text-sm"></i>
                        <span class="text-sm text-slate-700">Risk tolerance level (1-10 scale)</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-primary-500 mt-1 shrink-0 text-sm"></i>
                        <span class="text-sm text-slate-700">Spending and saving patterns</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-primary-500 mt-1 shrink-0 text-sm"></i>
                        <span class="text-sm text-slate-700">Personalized financial recommendations</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-primary-500 mt-1 shrink-0 text-sm"></i>
                        <span class="text-sm text-slate-700">Strategies to improve financial habits</span>
                    </li>
                </ul>
            </div>

            <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                <div class="mb-6 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-500 shadow-sm">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Personality Types</h3>
                </div>
                
                <div class="space-y-5">
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-500">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Spender</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Enjoys spending and experiences</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-500">
                            <i class="fas fa-piggy-bank"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Saver</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Prioritizes security and stability</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-500">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Investor</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Focuses on growth and returns</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-200 text-slate-600">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Avoider</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Prefers to avoid financial decisions</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-100 text-primary-500">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-900">Balanced</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Maintains healthy financial balance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('personality-quiz').addEventListener('submit', function(e) {
    // Calculate personality type based on answers
    const formData = new FormData(this);
    const answers = {};

    for (let [key, value] of formData.entries()) {
        answers[key] = value;
    }

    // Determine personality type
    let personalityType = 'balanced';
    const q1Answer = answers.q1;

    if (q1Answer === 'spender') {
        personalityType = 'spender';
    } else if (q1Answer === 'saver') {
        personalityType = 'saver';
    } else if (q1Answer === 'investor') {
        personalityType = 'investor';
    } else if (q1Answer === 'avoider') {
        personalityType = 'avoider';
    }

    // Add hidden fields for processing
    const addHiddenInput = (name, value) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        this.appendChild(input);
    };

    addHiddenInput('personality_type', personalityType);
    addHiddenInput('risk_tolerance', answers.q2 || 5);
    addHiddenInput('spending_style', answers.q3 || 'planned');
    addHiddenInput('saving_habits', answers.q4 || 'automatic');
    addHiddenInput('scores', JSON.stringify(answers));
});
</script>
@endsection
