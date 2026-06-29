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
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Your Financial Personality</h1>
        </div>
    </div>

    @if($personality)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Personality Results -->
                <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-6">Assessment Results</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div>
                            <h4 class="text-2xl font-bold text-primary-600 mb-4">{{ ucfirst($personality->personality_type) }}</h4>
                            <div class="space-y-3 text-sm text-slate-600">
                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                    <span class="font-medium text-slate-500">Risk Tolerance:</span>
                                    <span class="font-bold text-slate-900">{{ $personality->risk_tolerance }}/10</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                    <span class="font-medium text-slate-500">Spending Style:</span>
                                    <span class="font-bold text-slate-900">{{ ucfirst($personality->spending_style) }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                                    <span class="font-medium text-slate-500">Saving Habits:</span>
                                    <span class="font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $personality->saving_habits)) }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-1">
                                    <span class="font-medium text-slate-500">Assessment Date:</span>
                                    <span class="font-medium text-slate-700">{{ $personality->assessment_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center p-6 rounded-2xl bg-slate-50 ring-1 ring-slate-200 text-center">
                            @if($personality->personality_type == 'spender')
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-500 mb-4 shadow-inner">
                                    <i class="fas fa-shopping-cart text-4xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">You enjoy spending and experiences</p>
                            @elseif($personality->personality_type == 'saver')
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-blue-500 mb-4 shadow-inner">
                                    <i class="fas fa-piggy-bank text-4xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">You prioritize security and stability</p>
                            @elseif($personality->personality_type == 'investor')
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-amber-100 text-amber-500 mb-4 shadow-inner">
                                    <i class="fas fa-chart-line text-4xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">You focus on growth and returns</p>
                            @elseif($personality->personality_type == 'avoider')
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-slate-200 text-slate-500 mb-4 shadow-inner">
                                    <i class="fas fa-shield-alt text-4xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">You prefer to avoid financial decisions</p>
                            @else
                                <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary-100 text-primary-500 mb-4 shadow-inner">
                                    <i class="fas fa-balance-scale text-4xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-600">You maintain healthy financial balance</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500 shadow-sm">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h2 class="text-lg font-bold text-slate-900">Personalized Recommendations</h2>
                    </div>
                    
                    <ul class="space-y-3">
                        @foreach($personality->recommendations as $recommendation)
                            <li class="flex items-start gap-3 p-4 rounded-xl bg-slate-50 ring-1 ring-slate-200/60 hover:bg-white hover:shadow-sm transition-all">
                                <i class="fas fa-check-circle text-emerald-500 mt-0.5 shrink-0"></i>
                                <span class="text-sm text-slate-700 leading-relaxed">{{ $recommendation }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-8">
                <!-- Risk Tolerance Gauge -->
                <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 text-center">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Risk Tolerance</h2>
                    
                    <div class="relative pt-1 mb-4">
                        <div class="overflow-hidden h-4 text-xs flex rounded-full bg-slate-100 ring-1 ring-inset ring-slate-200">
                            @php
                                $riskColor = 'bg-emerald-500';
                                if ($personality->risk_tolerance > 3) $riskColor = 'bg-amber-400';
                                if ($personality->risk_tolerance > 7) $riskColor = 'bg-red-500';
                            @endphp
                            <div style="width: {{ ($personality->risk_tolerance / 10) * 100 }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $riskColor }} transition-all duration-1000"></div>
                        </div>
                        <div class="flex justify-between text-xs font-bold text-slate-400 mt-2">
                            <span>0</span>
                            <span>10</span>
                        </div>
                    </div>
                    
                    <div class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-slate-50 ring-1 ring-slate-200 text-xs font-bold text-slate-600">
                        @if($personality->risk_tolerance <= 3)
                            <i class="fas fa-shield-alt text-emerald-500 mr-1"></i> Conservative
                        @elseif($personality->risk_tolerance <= 7)
                            <i class="fas fa-balance-scale text-amber-500 mr-1"></i> Moderate
                        @else
                            <i class="fas fa-fire text-red-500 mr-1"></i> Aggressive
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-2">
                        @if($personality->risk_tolerance <= 3)
                            Prefers safe investments
                        @elseif($personality->risk_tolerance <= 7)
                            Balanced risk and return
                        @else
                            Willing to take higher risks
                        @endif
                    </p>
                </div>

                <!-- Strengths & Weaknesses -->
                <div class="rounded-3xl bg-slate-50 p-6 shadow-inner ring-1 ring-slate-200/60">
                    <h2 class="text-lg font-bold text-slate-900 mb-6">Your Profile</h2>
                    
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-widest text-emerald-600 mb-3 flex items-center gap-2">
                                <i class="fas fa-arrow-up"></i> Strengths
                            </h3>
                            <ul class="space-y-2 text-sm text-slate-600">
                                @if($personality->personality_type == 'spender')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Enjoys life and experiences</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Generous with others</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Optimistic about future</span></li>
                                @elseif($personality->personality_type == 'saver')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Financial security focused</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Disciplined with money</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Prepared for emergencies</span></li>
                                @elseif($personality->personality_type == 'investor')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Long-term thinking</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Knowledge of markets</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Goal-oriented</span></li>
                                @elseif($personality->personality_type == 'avoider')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Simple lifestyle</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Avoids debt</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Practical approach</span></li>
                                @else
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Well-balanced approach</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Flexible with changes</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i><span>Comprehensive planning</span></li>
                                @endif
                            </ul>
                        </div>
                        
                        <div class="pt-4 border-t border-slate-200">
                            <h3 class="text-sm font-bold uppercase tracking-widest text-amber-600 mb-3 flex items-center gap-2">
                                <i class="fas fa-arrow-down"></i> Areas for Growth
                            </h3>
                            <ul class="space-y-2 text-sm text-slate-600">
                                @if($personality->personality_type == 'spender')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Building emergency savings</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Creating spending boundaries</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Long-term financial planning</span></li>
                                @elseif($personality->personality_type == 'saver')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Enjoying life experiences</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Considering investment opportunities</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Balancing saving with spending</span></li>
                                @elseif($personality->personality_type == 'investor')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Diversification strategies</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Risk management</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Emergency fund building</span></li>
                                @elseif($personality->personality_type == 'avoider')
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Financial education</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Goal setting</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Automated systems</span></li>
                                @else
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Advanced investment strategies</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Tax optimization</span></li>
                                    <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] text-amber-400 mt-2 shrink-0"></i><span>Estate planning</span></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 flex flex-col gap-3">
                    <a href="{{ route('behavioral.take-quiz') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary-500 px-4 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-600 active:scale-95">
                        <i class="fas fa-redo"></i> Retake Quiz
                    </a>
                    <a href="{{ route('behavioral.index') }}" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 transition-all hover:bg-slate-50 active:scale-95">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- No Assessment Yet -->
        <div class="flex flex-col items-center justify-center py-20 text-center rounded-3xl bg-white shadow-premium ring-1 ring-slate-100">
            <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                <i class="fas fa-user-circle text-5xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">No Personality Assessment Yet</h3>
            <p class="text-slate-500 max-w-md mb-8">Take our financial personality quiz to understand your money habits and get personalized recommendations.</p>
            <a href="{{ route('behavioral.take-quiz') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-base font-bold text-white shadow-sm transition-all hover:bg-primary-600 hover:shadow-md active:scale-95">
                Take Personality Quiz
            </a>
        </div>
    @endif

</div>
@endsection
