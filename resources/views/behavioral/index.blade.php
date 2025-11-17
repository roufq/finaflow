@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('behavioral.index') }}</h1>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Spending Triggers Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('behavioral.triggers.title') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $spendingTriggers->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Habits Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('behavioral.habits.title') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $habits->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Personality Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('behavioral.personality.title') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $personality ? $personality->personality_type : __('behavioral.personality.quiz') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gamification Points Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('behavioral.gamification.points') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $gamification ? $gamification->points : 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.index') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('behavioral.triggers.create') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-plus"></i> {{ __('behavioral.triggers.add_trigger') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('behavioral.habits.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-plus"></i> {{ __('behavioral.habits.add_habit') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('behavioral.personality.quiz') }}" class="btn btn-info btn-block">
                                <i class="fas fa-brain"></i> {{ __('behavioral.personality.take_quiz') }}
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('behavioral.gamification') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-trophy"></i> {{ __('behavioral.gamification.achievements') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Row -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.triggers.title') }}</h6>
                </div>
                <div class="card-body">
                    @if($spendingTriggers->count() > 0)
                        @foreach($spendingTriggers->take(5) as $trigger)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <span class="font-weight-bold">{{ $trigger->trigger_type }}</span>
                                <div class="text-xs text-muted">{{ $trigger->description }}</div>
                            </div>
                            <span class="badge badge-warning">{{ $trigger->frequency }} times</span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">{{ __('behavioral.triggers.title') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.habits.title') }}</h6>
                </div>
                <div class="card-body">
                    @if($habits->count() > 0)
                        @foreach($habits->take(5) as $habit)
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <span class="font-weight-bold">{{ $habit->habit_name }}</span>
                                <div class="text-xs text-muted">{{ $habit->category }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-weight-bold">{{ $habit->current_streak }} days</div>
                                <div class="text-xs text-muted">Best: {{ $habit->best_streak }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted">{{ __('behavioral.habits.title') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($personality)
    <!-- Personality Insights Row -->
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('behavioral.personality.your_personality') }}: {{ $personality->personality_type }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>{{ __('behavioral.personality.personality_type') }}: {{ $personality->personality_type }}</h6>
                            <h6>{{ __('behavioral.personality.description') }}: {{ $personality->description }}</h6>
                            <h6>{{ __('behavioral.personality.strengths') }}: {{ $personality->strengths }}</h6>
                        </div>
                        <div class="col-md-6">
                            <h6>{{ __('behavioral.personality.recommendations') }}:</h6>
                            <ul>
                                @foreach($personality->recommendations as $recommendation)
                                <li>{{ $recommendation }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection
