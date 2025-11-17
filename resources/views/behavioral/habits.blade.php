@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('behavioral.habits.title') }}</h1>
        <a href="{{ route('behavioral.habits.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('behavioral.habits.add_habit') }}
        </a>
    </div>

    <!-- Habits List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.habits.title') }}</h6>
        </div>
        <div class="card-body">
            @if($habits->count() > 0)
                <div class="row">
                    @foreach($habits as $habit)
                    <div class="col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            {{ $habit->habit_name }}</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $habit->category }}</div>
                                        <div class="text-xs text-muted mt-2">
                                            {{ __('behavioral.habits.current_streak') }}: <span class="font-weight-bold">{{ $habit->current_streak }} days</span><br>
                                            {{ __('behavioral.habits.best_streak') }}: {{ $habit->best_streak }} days<br>
                                            {{ __('behavioral.habits.started') }}: {{ $habit->start_date->format('d M Y') }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-sm btn-success mb-2" onclick="markAchieved({{ $habit->id }})">
                                            <i class="fas fa-check"></i> {{ __('behavioral.habits.mark_today') }}
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteHabit({{ $habit->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-seedling fa-4x text-success mb-3"></i>
                    <h4>{{ __('behavioral.habits.no_habits') }}</h4>
                    <p class="text-muted">{{ __('behavioral.habits.start_building') }}</p>
                    <a href="{{ route('behavioral.habits.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> {{ __('behavioral.habits.create_first') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Habit Insights -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.habits.tips_title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('behavioral.habits.effective_strategies') }}</h6>
                            <ul>
                                <li><strong>{{ __('behavioral.habits.start_small') }}:</strong> {{ __('behavioral.habits.start_small_desc') }}</li>
                                <li><strong>{{ __('behavioral.habits.track_progress') }}:</strong> {{ __('behavioral.habits.track_progress_desc') }}</li>
                                <li><strong>{{ __('behavioral.habits.be_consistent') }}:</strong> {{ __('behavioral.habits.be_consistent_desc') }}</li>
                                <li><strong>{{ __('behavioral.habits.reward_yourself') }}:</strong> {{ __('behavioral.habits.reward_yourself_desc') }}</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('behavioral.habits.popular_habits') }}</h6>
                            <ul>
                                <li>{{ __('behavioral.habits.habit_1') }}</li>
                                <li>{{ __('behavioral.habits.habit_2') }}</li>
                                <li>{{ __('behavioral.habits.habit_3') }}</li>
                                <li>{{ __('behavioral.habits.habit_4') }}</li>
                                <li>{{ __('behavioral.habits.habit_5') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function markAchieved(habitId) {
    if (confirm('{{ __('behavioral.habits.confirm_mark') }}')) {
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
                alert('{{ __('behavioral.habits.error_updating') }}');
            }
        });
    }
}

function deleteHabit(id) {
    if (confirm('{{ __('behavioral.habits.confirm_delete') }}')) {
        // Implement delete functionality
        alert('{{ __('behavioral.habits.delete_implement') }}');
    }
}
</script>
@endsection
