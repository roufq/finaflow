@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Rewards Optimization</h1>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Optimization Suggestions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Smart Card Recommendations</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Best for Groceries
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $optimization['best_for_groceries'] ? $optimization['best_for_groceries']->card_type : 'No card available' }}
                                            </div>
                                            @if($optimization['best_for_groceries'])
                                                <div class="text-xs text-muted">
                                                    {{ $optimization['best_for_groceries']->reward_type }} rewards
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
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
                                                Best for Travel
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $optimization['best_for_travel'] ? $optimization['best_for_travel']->card_type : 'No card available' }}
                                            </div>
                                            @if($optimization['best_for_travel'])
                                                <div class="text-xs text-muted">
                                                    {{ $optimization['best_for_travel']->reward_type }} rewards
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-plane fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Best for Online Shopping
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                {{ $optimization['best_for_online'] ? $optimization['best_for_online']->card_type : 'No card available' }}
                                            </div>
                                            @if($optimization['best_for_online'])
                                                <div class="text-xs text-muted">
                                                    {{ $optimization['best_for_online']->reward_type }} rewards
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-bag fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reward Cards Overview -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Your Reward Cards</h6>
                </div>
                <div class="card-body">
                    @if($rewards->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Card Type</th>
                                        <th>Reward Type</th>
                                        <th>Points Earned</th>
                                        <th>Points Available</th>
                                        <th>Cashback</th>
                                        <th>Expires</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rewards as $reward)
                                        <tr>
                                            <td>{{ $reward->card_type }}</td>
                                            <td>{{ ucfirst($reward->reward_type) }}</td>
                                            <td>{{ number_format($reward->points_earned, 0, ',', '.') }}</td>
                                            <td>{{ number_format($reward->getAvailablePoints(), 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($reward->cashback_amount, 0, ',', '.') }}</td>
                                            <td>
                                                @if($reward->expiry_date)
                                                    {{ $reward->expiry_date->format('M d, Y') }}
                                                    @if($reward->isExpired())
                                                        <span class="badge badge-danger">Expired</span>
                                                    @elseif($reward->getDaysUntilExpiry() <= 30)
                                                        <span class="badge badge-warning">Soon</span>
                                                    @endif
                                                @else
                                                    Never
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('rewards.show-reward', $reward) }}" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('rewards.edit-reward', $reward) }}" class="btn btn-sm btn-warning">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">No reward cards added yet.</p>
                            <a href="{{ route('rewards.create') }}" class="btn btn-primary">Add Your First Reward Card</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Optimization Tips -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Optimization Tips</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>💳 Card Selection Tips:</h6>
                            <ul class="small">
                                <li>Use Visa/Mastercard for online purchases (wider acceptance)</li>
                                <li>Use American Express for travel and dining</li>
                                <li>Check for foreign transaction fees when traveling</li>
                                <li>Compare cashback rates for different spending categories</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>🎯 Reward Maximization:</h6>
                            <ul class="small">
                                <li>Pay your balance in full each month to avoid interest</li>
                                <li>Redeem points before they expire</li>
                                <li>Look for sign-up bonuses for new cards</li>
                                <li>Track spending to maximize rewards in bonus categories</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
