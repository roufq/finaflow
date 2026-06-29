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
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ __('behavioral.habits.title') ?? 'Financial Habits' }}</h1>
        </div>
        <a href="{{ route('behavioral.habits.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-500 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-600 active:scale-95">
            <i class="fas fa-plus"></i> {{ __('behavioral.habits.add_habit') ?? 'New Habit' }}
        </a>
    </div>

    <!-- Habits List -->
    <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 mb-8">
        <h2 class="text-lg font-bold text-slate-900 mb-6">{{ __('behavioral.habits.title') ?? 'Your Habits' }}</h2>
        
        @if($habits->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($habits as $habit)
                <div class="rounded-2xl bg-white p-5 ring-1 ring-slate-200 shadow-sm border-l-4 border-emerald-500 flex flex-col justify-between hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <div class="text-xs font-bold text-emerald-500 uppercase tracking-wider mb-1">{{ $habit->habit_name }}</div>
                            <div class="text-base font-bold text-slate-900 capitalize">{{ str_replace('_', ' ', $habit->category) }}</div>
                            <div class="text-sm text-slate-500 mt-3 space-y-1">
                                <div>{{ __('behavioral.habits.current_streak') ?? 'Current Streak' }}: <span class="font-bold text-slate-700">{{ $habit->current_streak }} days</span></div>
                                <div>{{ __('behavioral.habits.best_streak') ?? 'Best Streak' }}: <span class="font-bold text-slate-700">{{ $habit->best_streak }} days</span></div>
                                <div>{{ __('behavioral.habits.started') ?? 'Started' }}: <span class="font-medium text-slate-700">{{ $habit->start_date->format('d M Y') }}</span></div>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2 shrink-0">
                            <button class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500 transition-colors hover:bg-emerald-500 hover:text-white" onclick="markAchieved({{ $habit->id }})" title="{{ __('behavioral.habits.mark_today') ?? 'Mark Achieved Today' }}">
                                <i class="fas fa-check"></i>
                            </button>
                            <button class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-500 transition-colors hover:bg-red-500 hover:text-white" onclick="deleteHabit({{ $habit->id }})" title="Delete">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-500">
                    <i class="fas fa-seedling text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900">{{ __('behavioral.habits.no_habits') ?? 'No Habits Yet' }}</h3>
                <p class="mt-2 text-sm text-slate-500 max-w-md">{{ __('behavioral.habits.start_building') ?? 'Start building better financial routines by creating your first habit.' }}</p>
                <a href="{{ route('behavioral.habits.create') }}" class="mt-6 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-500 px-6 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-emerald-600 active:scale-95">
                    <i class="fas fa-plus"></i> {{ __('behavioral.habits.create_first') ?? 'Create First Habit' }}
                </a>
            </div>
        @endif
    </div>

    <!-- Habit Insights -->
    <div class="rounded-3xl bg-slate-50 p-6 shadow-inner ring-1 ring-slate-200/60">
        <div class="mb-6 flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-900">{{ __('behavioral.habits.tips_title') ?? 'Habit Building Tips' }}</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-900 mb-3">{{ __('behavioral.habits.effective_strategies') ?? 'Effective Strategies' }}</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-emerald-500 mt-1 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.habits.start_small') ?? 'Start Small' }}:</strong> {{ __('behavioral.habits.start_small_desc') ?? 'Begin with a goal that is too easy to fail.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-emerald-500 mt-1 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.habits.track_progress') ?? 'Track Progress' }}:</strong> {{ __('behavioral.habits.track_progress_desc') ?? 'Keep a visual record of your streaks.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-emerald-500 mt-1 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.habits.be_consistent') ?? 'Be Consistent' }}:</strong> {{ __('behavioral.habits.be_consistent_desc') ?? 'Do it at the same time or place every day.' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-check text-emerald-500 mt-1 shrink-0"></i>
                        <span><strong class="text-slate-800">{{ __('behavioral.habits.reward_yourself') ?? 'Reward Yourself' }}:</strong> {{ __('behavioral.habits.reward_yourself_desc') ?? 'Celebrate small wins along the way.' }}</span>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-900 mb-3">{{ __('behavioral.habits.popular_habits') ?? 'Popular Financial Habits' }}</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i>
                        <span>{{ __('behavioral.habits.habit_1') ?? 'Review bank statements weekly' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i>
                        <span>{{ __('behavioral.habits.habit_2') ?? 'Transfer 10% to savings on payday' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i>
                        <span>{{ __('behavioral.habits.habit_3') ?? 'Pack lunch for work 4 days a week' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i>
                        <span>{{ __('behavioral.habits.habit_4') ?? 'Wait 24h before non-essential purchases' }}</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-circle text-[6px] text-emerald-400 mt-2 shrink-0"></i>
                        <span>{{ __('behavioral.habits.habit_5') ?? 'Check budget before grocery shopping' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>

<script>
function markAchieved(habitId) {
    if (confirm('{{ __('behavioral.habits.confirm_mark') ?? 'Mark this habit as achieved today?' }}')) {
        fetch('{{ url("/behavioral/habits") }}/' + habitId + '/achieved', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('{{ __('behavioral.habits.error_updating') ?? 'Error updating habit.' }}');
            }
        });
    }
}

function deleteHabit(id) {
    if (confirm('{{ __('behavioral.habits.confirm_delete') ?? 'Are you sure you want to delete this habit?' }}')) {
        // Implement delete functionality
        alert('{{ __('behavioral.habits.delete_implement') ?? 'Delete functionality will be implemented here.' }}');
    }
}
</script>
@endsection
