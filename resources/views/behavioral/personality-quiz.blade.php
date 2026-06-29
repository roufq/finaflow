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
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Financial Personality Assessment</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="rounded-3xl bg-white p-6 md:p-8 shadow-premium ring-1 ring-slate-100 mb-8">
                <h2 class="text-xl font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4">Discover Your Financial Personality</h2>
                
                <form id="personality-quiz" method="POST" action="{{ route('behavioral.personality.store') }}" class="space-y-8">
                    @csrf

                    <!-- Question 1 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">1. When you receive unexpected money (bonus, gift), what do you usually do?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q1" value="save" required>
                                <span class="text-sm text-slate-700 font-medium">Save it for future needs or investments</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q1" value="spend">
                                <span class="text-sm text-slate-700 font-medium">Spend it on something fun or needed</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q1" value="invest">
                                <span class="text-sm text-slate-700 font-medium">Invest it in stocks, crypto, or other assets</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 2 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">2. How do you feel about debt?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q2" value="avoid" required>
                                <span class="text-sm text-slate-700 font-medium">I avoid debt at all costs</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q2" value="strategic">
                                <span class="text-sm text-slate-700 font-medium">I use debt strategically (mortgage, education)</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q2" value="comfortable">
                                <span class="text-sm text-slate-700 font-medium">I'm comfortable with some debt for lifestyle</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 3 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">3. What's your approach to budgeting?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q3" value="strict" required>
                                <span class="text-sm text-slate-700 font-medium">I track every expense meticulously</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q3" value="flexible">
                                <span class="text-sm text-slate-700 font-medium">I have rough guidelines but stay flexible</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q3" value="minimal">
                                <span class="text-sm text-slate-700 font-medium">I don't budget much, I just spend what I need</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 4 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">4. How do you react to sales or discounts?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q4" value="ignore" required>
                                <span class="text-sm text-slate-700 font-medium">I buy only what I need, regardless of price</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q4" value="smart">
                                <span class="text-sm text-slate-700 font-medium">I look for good deals but don't overspend</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q4" value="enthusiastic">
                                <span class="text-sm text-slate-700 font-medium">I love sales and often buy things I don't need</span>
                            </label>
                        </div>
                    </div>

                    <!-- Question 5 -->
                    <div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">5. What's your investment risk tolerance?</h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q5" value="low" required>
                                <span class="text-sm text-slate-700 font-medium">I prefer safe, guaranteed returns</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q5" value="moderate">
                                <span class="text-sm text-slate-700 font-medium">I'm willing to take some risk for better returns</span>
                            </label>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 hover:border-primary-300 transition-colors has-[:checked]:bg-primary-50 has-[:checked]:border-primary-500">
                                <input class="mt-1 h-4 w-4 border-slate-300 text-primary-600 focus:ring-primary-600" type="radio" name="q5" value="high">
                                <span class="text-sm text-slate-700 font-medium">I'm comfortable with high risk for high rewards</span>
                            </label>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100">
                        <button type="submit" class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-base font-bold text-white shadow-sm transition-all hover:bg-primary-600 hover:shadow-md active:scale-95">
                            Get My Financial Personality <i class="fas fa-arrow-right ml-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="rounded-3xl bg-slate-50 p-6 shadow-inner ring-1 ring-slate-200/60 sticky top-6">
                <div class="mb-4 flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-primary-500 shadow-sm">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">What You'll Learn</h3>
                </div>
                
                <p class="text-sm text-slate-600 mb-4">This assessment will help you understand:</p>
                <ul class="space-y-3 mb-6">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span class="text-sm text-slate-700">Your natural spending tendencies</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span class="text-sm text-slate-700">Risk tolerance for investments</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span class="text-sm text-slate-700">Approach to saving and budgeting</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-primary-400 mt-2 shrink-0"></i>
                        <span class="text-sm text-slate-700">Personalized financial recommendations</span>
                    </li>
                </ul>

                <div class="rounded-xl bg-blue-50 p-4 border border-blue-100">
                    <h4 class="font-bold text-blue-900 mb-1 flex items-center gap-2 text-sm">
                        <i class="fas fa-shield-alt text-blue-500"></i> Disclaimer
                    </h4>
                    <p class="text-xs leading-relaxed text-blue-800">This is for educational purposes. Financial decisions should consider your full financial situation and personal context.</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
