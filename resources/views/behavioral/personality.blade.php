@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Your Financial Personality</h1>
    </div>

    @if($personality)
        <div class="row">
            <div class="col-lg-8">
                <!-- Personality Results -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Assessment Results</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="text-primary mb-3">{{ ucfirst($personality->personality_type) }}</h4>
                                <p><strong>Risk Tolerance:</strong> {{ $personality->risk_tolerance }}/10</p>
                                <p><strong>Spending Style:</strong> {{ ucfirst($personality->spending_style) }}</p>
                                <p><strong>Saving Habits:</strong> {{ ucfirst(str_replace('_', ' ', $personality->saving_habits)) }}</p>
                                <p><strong>Assessment Date:</strong> {{ $personality->assessment_date->format('M d, Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    @if($personality->personality_type == 'spender')
                                        <i class="fas fa-shopping-cart fa-4x text-success mb-3"></i>
                                        <p class="text-muted">You enjoy spending and experiences</p>
                                    @elseif($personality->personality_type == 'saver')
                                        <i class="fas fa-piggy-bank fa-4x text-info mb-3"></i>
                                        <p class="text-muted">You prioritize security and stability</p>
                                    @elseif($personality->personality_type == 'investor')
                                        <i class="fas fa-chart-line fa-4x text-warning mb-3"></i>
                                        <p class="text-muted">You focus on growth and returns</p>
                                    @elseif($personality->personality_type == 'avoider')
                                        <i class="fas fa-shield-alt fa-4x text-secondary mb-3"></i>
                                        <p class="text-muted">You prefer to avoid financial decisions</p>
                                    @else
                                        <i class="fas fa-balance-scale fa-4x text-primary mb-3"></i>
                                        <p class="text-muted">You maintain healthy financial balance</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Personalized Recommendations</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            @foreach($personality->recommendations as $recommendation)
                                <li class="list-group-item">
                                    <i class="fas fa-lightbulb text-warning mr-2"></i>
                                    {{ $recommendation }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Risk Tolerance Gauge -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Risk Tolerance</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ ($personality->risk_tolerance / 10) * 100 }}%" aria-valuenow="{{ $personality->risk_tolerance }}" aria-valuemin="0" aria-valuemax="10"></div>
                            </div>
                            <p class="small text-muted">
                                @if($personality->risk_tolerance <= 3)
                                    Conservative - Prefers safe investments
                                @elseif($personality->risk_tolerance <= 7)
                                    Moderate - Balanced risk and return
                                @else
                                    Aggressive - Willing to take higher risks
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Strengths & Weaknesses -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Your Profile</h6>
                    </div>
                    <div class="card-body">
                        @if($personality->personality_type == 'spender')
                            <h6>Strengths:</h6>
                            <ul class="small mb-3">
                                <li>Enjoys life and experiences</li>
                                <li>Generous with others</li>
                                <li>Optimistic about future</li>
                            </ul>
                            <h6>Areas for Growth:</h6>
                            <ul class="small">
                                <li>Building emergency savings</li>
                                <li>Creating spending boundaries</li>
                                <li>Long-term financial planning</li>
                            </ul>
                        @elseif($personality->personality_type == 'saver')
                            <h6>Strengths:</h6>
                            <ul class="small mb-3">
                                <li>Financial security focused</li>
                                <li>Disciplined with money</li>
                                <li>Prepared for emergencies</li>
                            </ul>
                            <h6>Areas for Growth:</h6>
                            <ul class="small">
                                <li>Enjoying life experiences</li>
                                <li>Considering investment opportunities</li>
                                <li>Balancing saving with spending</li>
                            </ul>
                        @elseif($personality->personality_type == 'investor')
                            <h6>Strengths:</h6>
                            <ul class="small mb-3">
                                <li>Long-term thinking</li>
                                <li>Knowledge of markets</li>
                                <li>Goal-oriented</li>
                            </ul>
                            <h6>Areas for Growth:</h6>
                            <ul class="small">
                                <li>Diversification strategies</li>
                                <li>Risk management</li>
                                <li>Emergency fund building</li>
                            </ul>
                        @elseif($personality->personality_type == 'avoider')
                            <h6>Strengths:</h6>
                            <ul class="small mb-3">
                                <li>Simple lifestyle</li>
                                <li>Avoids debt</li>
                                <li>Practical approach</li>
                            </ul>
                            <h6>Areas for Growth:</h6>
                            <ul class="small">
                                <li>Financial education</li>
                                <li>Goal setting</li>
                                <li>Automated systems</li>
                            </ul>
                        @else
                            <h6>Strengths:</h6>
                            <ul class="small mb-3">
                                <li>Well-balanced approach</li>
                                <li>Flexible with changes</li>
                                <li>Comprehensive planning</li>
                            </ul>
                            <h6>Areas for Growth:</h6>
                            <ul class="small">
                                <li>Advanced investment strategies</li>
                                <li>Tax optimization</li>
                                <li>Estate planning</li>
                            </ul>
                        @endif
                    </div>
                </div>

                <!-- Retake Quiz -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('behavioral.take-quiz') }}" class="btn btn-primary btn-block mb-2">Retake Quiz</a>
                        <a href="{{ route('behavioral.index') }}" class="btn btn-secondary btn-block">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- No Assessment Yet -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-user-circle fa-4x text-gray-300 mb-4"></i>
                        <h4 class="text-gray-500">No Personality Assessment Yet</h4>
                        <p class="text-gray-600 mb-4">Take our financial personality quiz to understand your money habits and get personalized recommendations.</p>
                        <a href="{{ route('behavioral.take-quiz') }}" class="btn btn-primary btn-lg">Take Personality Quiz</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
