@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Financial Gamification</h1>
    </div>

    @if($gamification)
        <div class="row">
            <!-- Current Stats -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Your Progress</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border-left-primary shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Total Points
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ number_format($gamification->points, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-star fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-left-success shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Current Level
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ $gamification->level }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-trophy fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-left-info shadow h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Badges Earned
                                                </div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ count($gamification->badges ?? []) }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-medal fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Achievements -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Achievements</h6>
                    </div>
                    <div class="card-body">
                        @if($gamification->achievements && count($gamification->achievements) > 0)
                            <div class="row">
                                @foreach($gamification->achievements as $achievement)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-left-warning">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $achievement['name'] ?? 'Achievement' }}</h6>
                                                <p class="card-text small">{{ $achievement['description'] ?? '' }}</p>
                                                <small class="text-muted">{{ $achievement['earned_date'] ?? '' }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-center text-muted py-4">No achievements yet. Keep using the app to earn badges!</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Level Progress -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Level Progress</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h4>Level {{ $gamification->level }}</h4>
                            <div class="progress mb-3">
                                <div class="progress-bar" role="progressbar" style="width: {{ $gamification->getLevelProgress() }}%"></div>
                            </div>
                            <p class="small text-muted">
                                {{ $gamification->getPointsToNextLevel() }} points to next level
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                    </div>
                    <div class="card-body">
                        @if($gamification->activity_log && count($gamification->activity_log) > 0)
                            <ul class="list-group list-group-flush small">
                                @foreach(array_slice($gamification->activity_log, 0, 5) as $activity)
                                    <li class="list-group-item px-0">
                                        <i class="fas fa-plus text-success mr-2"></i>
                                        {{ $activity['description'] ?? 'Points earned' }}
                                        <br>
                                        <small class="text-muted">{{ $activity['date'] ?? '' }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-center text-muted small py-3">No recent activity</p>
                        @endif
                    </div>
                </div>

                <!-- How to Earn Points -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">How to Earn Points</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small">
                            <li><strong>+10 points:</strong> Add a new transaction</li>
                            <li><strong>+25 points:</strong> Create a budget</li>
                            <li><strong>+50 points:</strong> Set up a savings goal</li>
                            <li><strong>+100 points:</strong> Complete a habit streak</li>
                            <li><strong>+200 points:</strong> Pay off debt</li>
                            <li><strong>+500 points:</strong> Reach savings milestone</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Initialize Gamification -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-gamepad fa-4x text-gray-300 mb-4"></i>
                        <h4 class="text-gray-500">Gamification Not Initialized</h4>
                        <p class="text-gray-600 mb-4">Start earning points and unlocking achievements by using the financial tracker features.</p>
                        <form method="POST" action="{{ route('behavioral.initialize-gamification') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">Initialize Gamification</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
