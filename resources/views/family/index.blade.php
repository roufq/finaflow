@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('family.dashboard_title') }}</h1>
        <div>
            <a href="{{ route('family.members.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('family.buttons.add_member') }}
            </a>
            <a href="{{ route('family.shared-expenses.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('family.buttons.add_expense') }}
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Family Members Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('family.overview.members') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $familyMembers->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shared Expenses Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('family.overview.shared_expenses') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sharedExpenses->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Family Goals Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('family.overview.goals') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $familyGoals->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullseye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('family.overview.events') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $upcomingEvents->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-gift fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Family Members Overview -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Family Members</h6>
                    <a href="{{ route('family.members') }}" class="btn btn-primary btn-sm">View All</a>
                </div>
                <div class="card-body">
                    @if($familyMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('family.members.name') }}</th>
                                        <th>{{ __('family.members.relationship') }}</th>
                                        <th>{{ __('family.members.allowance') }}</th>
                                        <th>{{ __('family.members.balance') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($familyMembers as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td>{{ $member->relationship }}</td>
                                        <td>Rp {{ number_format($member->monthly_allowance, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($member->current_balance, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">{{ __('family.empty_states.members.description') }}</p>
                            <a href="{{ route('family.members.create') }}" class="btn btn-primary">{{ __('family.empty_states.members.cta') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Shared Expenses -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">{{ __('family.shared_expenses.title') }}</h6>
                    <a href="{{ route('family.shared-expenses') }}" class="btn btn-success btn-sm">{{ __('family.buttons.view_all') }}</a>
                </div>
                <div class="card-body">
                    @if($sharedExpenses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{ __('family.shared_expenses.name') }}</th>
                                        <th>{{ __('family.shared_expenses.amount') }}</th>
                                        <th>{{ __('family.shared_expenses.date') }}</th>
                                        <th>{{ __('family.shared_expenses.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sharedExpenses as $expense)
                                    <tr>
                                        <td>{{ $expense->expense_name }}</td>
                                        <td>Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</td>
                                        <td>{{ $expense->expense_date->format('d/m/Y') }}</td>
                                        <td>
                                            @if($expense->is_settled)
                                                <span class="badge badge-success">{{ __('family.statuses.settled') }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ __('family.statuses.pending') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-receipt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">{{ __('family.empty_states.expenses.description') }}</p>
                            <a href="{{ route('family.shared-expenses.create') }}" class="btn btn-success">{{ __('family.empty_states.expenses.cta') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <!-- Content Row -->
    <div class="row">

        <!-- Family Goals -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info">{{ __('family.goals.title') }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('family.goals') }}" class="btn btn-outline-info btn-sm text-info border-info">
                            {{ __('family.buttons.view_all') }}
                        </a>
                        <a href="{{ route('family.goals.create') }}" class="btn btn-info btn-sm">{{ __('family.buttons.add_goal') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($familyGoals->count() > 0)
                        @foreach($familyGoals as $goal)
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold">{{ $goal->goal_name }}</span>
                                <span class="text-muted">{{ $goal->progress_percentage }}% Complete</span>
                            </div>
                            <div class="progress mb-2">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ $goal->progress_percentage }}%"
                                    aria-valuenow="{{ $goal->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">
                                Rp {{ number_format($goal->current_amount, 0, ',', '.') }} of Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                            </small>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bullseye fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">{{ __('family.empty_states.goals.description') }}</p>
                            <a href="{{ route('family.goals.create') }}" class="btn btn-info">{{ __('family.empty_states.goals.cta') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Gift Events -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-warning">{{ __('family.events.title') }}</h6>
                    <div class="btn-group">
                        <a href="{{ route('family.gift-events') }}" class="btn btn-light btn-sm text-warning border-warning">
                            {{ __('family.buttons.view_all') }}
                        </a>
                        <a href="{{ route('family.gift-events.create') }}" class="btn btn-warning btn-sm">{{ __('family.buttons.add_event') }}</a>
                    </div>
                </div>
                <div class="card-body">
                    @if($upcomingEvents->count() > 0)
                        @foreach($upcomingEvents as $event)
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <i class="fas fa-gift fa-2x text-warning"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold">{{ $event->event_name }}</div>
                                <div class="text-muted small">{{ $event->event_date->format('d M Y') }}</div>
                                <div class="text-muted small">{{ __('family.events.budget') }}: Rp {{ number_format($event->budget_amount, 0, ',', '.') }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-alt fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">{{ __('family.empty_states.events.description') }}</p>
                            <a href="{{ route('family.gift-events.create') }}" class="btn btn-warning">{{ __('family.empty_states.events.cta') }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
