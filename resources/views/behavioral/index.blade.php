@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('behavioral.index') }}</h1>
            <p class="text-sm font-medium text-slate-500">Understand your financial psychology and build healthier habits</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Overview Metrics Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Triggers -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('behavioral.triggers.title') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ $spendingTriggers->count() }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Emotional touchpoints</p>
            </div>
        </div>

        <!-- Habits -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-emerald-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('behavioral.habits.title') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                    <i class="fas fa-check-circle text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ $habits->count() }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Daily recurring actions</p>
            </div>
        </div>

        <!-- Personality -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('behavioral.personality.title') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <i class="fas fa-user-circle text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-lg font-extrabold text-slate-900 truncate">
                    {{ $personality ? $personality->personality_type : __('behavioral.personality.quiz') }}
                </h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Your financial archetype</p>
            </div>
        </div>

        <!-- Gamification -->
        <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('behavioral.gamification.points') }}</span>
                <i class="fas fa-trophy text-primary-400"></i>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-white tracking-tight">{{ $gamification ? $gamification->points : 0 }} <span class="text-xs font-medium text-slate-500">XP</span></h3>
                <div class="mt-2 h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                    <div class="h-full bg-primary-500" style="width: 45%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Bar -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('behavioral.triggers.create') }}" class="group flex items-center justify-between p-4 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 hover:ring-primary-500/30 transition-all">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-plus"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Add Trigger</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
        </a>
        <a href="{{ route('behavioral.habits.create') }}" class="group flex items-center justify-between p-4 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 hover:ring-primary-500/30 transition-all">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-plus"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">New Habit</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
        </a>
        <a href="{{ route('behavioral.personality.quiz') }}" class="group flex items-center justify-between p-4 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 hover:ring-primary-500/30 transition-all">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-brain"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Take Quiz</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
        </a>
        <a href="{{ route('behavioral.gamification') }}" class="group flex items-center justify-between p-4 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 hover:ring-primary-500/30 transition-all">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-50 text-purple-600 group-hover:scale-110 transition-transform">
                    <i class="fas fa-trophy"></i>
                </div>
                <span class="text-xs font-bold text-slate-700">Achivements</span>
            </div>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
        </a>
    </div>

    <!-- Recent Psychology Insights Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Spending Triggers -->
        <div class="rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">{{ __('behavioral.triggers.title') }}</h3>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Psychological Audit</span>
            </div>
            <div class="p-6 space-y-4">
                @forelse($spendingTriggers->take(5) as $trigger)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-amber-500 shadow-sm ring-1 ring-amber-100">
                            <i class="fas fa-bolt text-[10px]"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $trigger->trigger_type }}</p>
                            <p class="text-[10px] text-slate-400 truncate max-w-[200px]">{{ $trigger->description }}</p>
                        </div>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-[9px] font-black text-amber-600 ring-1 ring-amber-100 uppercase">{{ $trigger->frequency }} Events</span>
                </div>
                @empty
                <p class="text-xs font-medium text-slate-400 italic text-center py-4">No behavioral triggers recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Habits Streak Tracker -->
        <div class="rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
            <div class="p-5 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">{{ __('behavioral.habits.title') }}</h3>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Engagement Streaks</span>
            </div>
            <div class="p-6 space-y-4">
                @forelse($habits->take(5) as $habit)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50/50 hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
                            <i class="fas fa-leaf text-[10px]"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ $habit->habit_name }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-tighter">{{ $habit->category }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-black text-emerald-600">{{ $habit->current_streak }}d Streak</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Best: {{ $habit->best_streak }}d</p>
                    </div>
                </div>
                @empty
                <p class="text-xs font-medium text-slate-400 italic text-center py-4">Establish financial habits to see your progress.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Deep Personality Insight -->
    @if($personality)
    <div class="rounded-2xl bg-white p-8 shadow-premium ring-1 ring-slate-100 border-t-8 border-primary-500">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
            <div>
                <span class="text-[10px] font-bold text-primary-500 uppercase tracking-[0.2em] mb-2 block">Psychological Profile</span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mb-6">{{ $personality->personality_type }}</h2>
                
                <div class="space-y-6">
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Core Description</h4>
                        <p class="text-sm font-medium text-slate-600 leading-relaxed">{{ $personality->description }}</p>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Primary Strengths</h4>
                        <p class="text-sm font-bold text-emerald-600 bg-emerald-50 p-3 rounded-xl border border-emerald-100">{{ $personality->strengths }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-50/50 rounded-2xl p-6 border border-slate-100">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-widest mb-4 flex items-center gap-2">
                    <i class="fas fa-lightbulb text-primary-500"></i>
                    AI Recommendations
                </h4>
                <ul class="space-y-3">
                    @foreach($personality->recommendations as $recommendation)
                    <li class="flex items-start gap-3 p-3 rounded-xl bg-white shadow-sm text-xs font-bold text-slate-700 leading-relaxed">
                        <span class="h-1.5 w-1.5 rounded-full bg-primary-500 shrink-0 mt-1.5"></span>
                        {{ $recommendation }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
