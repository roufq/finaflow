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
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Financial Gamification</h1>
        </div>
    </div>

    @if($gamification)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Current Stats & Achievements -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Current Stats Section -->
                <div>
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Your Progress</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Points -->
                        <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm border-l-4 border-amber-400 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-amber-500 uppercase tracking-wider mb-1">Total Points</div>
                                <div class="text-2xl font-bold text-slate-900">{{ number_format($gamification->points, 0, ',', '.') }}</div>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-50 text-amber-400">
                                <i class="fas fa-star text-2xl"></i>
                            </div>
                        </div>

                        <!-- Level -->
                        <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm border-l-4 border-emerald-500 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-1">Current Level</div>
                                <div class="text-2xl font-bold text-slate-900">{{ $gamification->level }}</div>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                                <i class="fas fa-trophy text-2xl"></i>
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-blue-500 uppercase tracking-wider mb-1">Badges Earned</div>
                                <div class="text-2xl font-bold text-slate-900">{{ count($gamification->badges ?? []) }}</div>
                            </div>
                            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-500">
                                <i class="fas fa-medal text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Achievements -->
                <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-6">Achievements</h2>
                    
                    @if($gamification->achievements && count($gamification->achievements) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($gamification->achievements as $achievement)
                                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 relative overflow-hidden group hover:border-amber-200 hover:bg-amber-50 transition-colors">
                                    <div class="absolute -right-4 -top-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                        <i class="fas fa-award text-8xl text-amber-500"></i>
                                    </div>
                                    <h4 class="font-bold text-slate-900 mb-1 relative z-10">{{ $achievement['name'] ?? 'Achievement' }}</h4>
                                    <p class="text-sm text-slate-600 mb-2 relative z-10">{{ $achievement['description'] ?? '' }}</p>
                                    <span class="inline-flex rounded-lg bg-white px-2 py-1 text-[10px] font-bold text-slate-400 ring-1 ring-inset ring-slate-200 relative z-10">
                                        <i class="fas fa-check text-emerald-500 mr-1"></i> Earned {{ $achievement['earned_date'] ?? '' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-center border-2 border-dashed border-slate-200 rounded-2xl">
                            <i class="fas fa-unlock text-4xl text-slate-300 mb-3"></i>
                            <p class="text-sm font-medium text-slate-500">No achievements yet.<br>Keep tracking your finances to earn badges!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column: Sidebar info -->
            <div class="lg:col-span-1 space-y-8">
                
                <!-- Level Progress -->
                <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 text-center">
                    <h2 class="text-lg font-bold text-slate-900 mb-2">Level {{ $gamification->level }}</h2>
                    <p class="text-sm text-slate-500 mb-6">{{ $gamification->getPointsToNextLevel() }} points to next level</p>
                    
                    <div class="relative pt-1">
                        <div class="overflow-hidden h-3 mb-2 text-xs flex rounded-full bg-slate-100">
                            <div style="width: {{ $gamification->getLevelProgress() }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all duration-1000"></div>
                        </div>
                        <div class="flex justify-between text-xs font-bold text-slate-400">
                            <span>Lvl {{ $gamification->level }}</span>
                            <span>Lvl {{ $gamification->level + 1 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100">
                    <h2 class="text-lg font-bold text-slate-900 mb-4">Recent Activity</h2>
                    
                    @if($gamification->activity_log && count($gamification->activity_log) > 0)
                        <ul class="space-y-4">
                            @foreach(array_slice($gamification->activity_log, 0, 5) as $activity)
                                <li class="flex items-start gap-3 text-sm">
                                    <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-emerald-50 text-emerald-500 mt-0.5">
                                        <i class="fas fa-plus text-[10px]"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800">{{ $activity['description'] ?? 'Points earned' }}</div>
                                        <div class="text-xs text-slate-400">{{ $activity['date'] ?? '' }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center text-sm text-slate-500 py-4">No recent activity</p>
                    @endif
                </div>

                <!-- How to Earn Points -->
                <div class="rounded-3xl bg-slate-50 p-6 shadow-inner ring-1 ring-slate-200/60">
                    <h3 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-4">How to Earn Points</h3>
                    <ul class="space-y-3 text-sm text-slate-600">
                        <li class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span>Add a new transaction</span>
                            <span class="font-bold text-amber-500">+10</span>
                        </li>
                        <li class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span>Create a budget</span>
                            <span class="font-bold text-amber-500">+25</span>
                        </li>
                        <li class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span>Set up a savings goal</span>
                            <span class="font-bold text-amber-500">+50</span>
                        </li>
                        <li class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span>Complete a habit streak</span>
                            <span class="font-bold text-amber-500">+100</span>
                        </li>
                        <li class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span>Pay off debt</span>
                            <span class="font-bold text-amber-500">+200</span>
                        </li>
                        <li class="flex items-center justify-between">
                            <span>Reach savings milestone</span>
                            <span class="font-bold text-amber-500">+500</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <!-- Initialize Gamification -->
        <div class="flex flex-col items-center justify-center py-20 text-center rounded-3xl bg-white shadow-premium ring-1 ring-slate-100">
            <div class="mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                <i class="fas fa-gamepad text-5xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-2">Gamification Not Initialized</h3>
            <p class="text-slate-500 max-w-md mb-8">Start earning points and unlocking achievements by using the financial tracker features. Make personal finance fun!</p>
            <form method="POST" action="{{ route('behavioral.initialize-gamification') }}">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-8 py-4 text-base font-bold text-white shadow-sm transition-all hover:bg-primary-600 hover:shadow-md active:scale-95">
                    Initialize Gamification
                </button>
            </form>
        </div>
    @endif

</div>
@endsection
