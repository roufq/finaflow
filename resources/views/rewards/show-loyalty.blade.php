@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $loyaltyProgram->program_name }}</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Program Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Program Type:</strong> {{ ucfirst($loyaltyProgram->program_type) }}</p>
                            <p><strong>Current Tier:</strong> {{ ucfirst($loyaltyProgram->tier_level ?? 'Bronze') }}</p>
                            <p><strong>Points Balance:</strong> {{ number_format($loyaltyProgram->points_balance, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-md-6">
                            @if($loyaltyProgram->membership_number)
                                <p><strong>Membership Number:</strong> {{ $loyaltyProgram->membership_number }}</p>
                            @endif
                            @if($loyaltyProgram->expiry_date)
                                <p><strong>Expires:</strong> {{ $loyaltyProgram->expiry_date->format('M d, Y') }}</p>
                                <p><strong>Days Until Expiry:</strong>
                                    @if($loyaltyProgram->getDaysUntilExpiry() > 0)
                                        <span class="text-success">{{ $loyaltyProgram->getDaysUntilExpiry() }} days</span>
                                    @else
                                        <span class="text-danger">Expired</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tier Benefits -->
            @if($loyaltyProgram->benefits)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Tier Benefits</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($loyaltyProgram->benefits as $benefit)
                            <li class="list-group-item">{{ $benefit }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Redemption History -->
            @if($loyaltyProgram->redemption_history)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Redemption History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Points Redeemed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_reverse($loyaltyProgram->redemption_history) as $redemption)
                                    <tr>
                                        <td>{{ $redemption['date'] }}</td>
                                        <td>{{ $redemption['description'] ?? 'N/A' }}</td>
                                        <td>{{ number_format($redemption['points_redeemed'], 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rewards.redeem-loyalty', $loyaltyProgram) }}" class="mb-3">
                        @csrf
                        <div class="form-group">
                            <label for="points">Redeem Points</label>
                            <input type="number" class="form-control" id="points" name="points" placeholder="Points to redeem" min="1" max="{{ $loyaltyProgram->points_balance }}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="What are you redeeming for?">
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">Redeem Points</button>
                    </form>

                    <a href="{{ route('rewards.edit-loyalty', $loyaltyProgram) }}" class="btn btn-primary btn-block mb-2">Edit Program</a>
                    <form method="POST" action="{{ route('rewards.destroy-loyalty', $loyaltyProgram) }}" onsubmit="return confirm('Are you sure you want to delete this loyalty program?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">Delete Program</button>
                    </form>
                </div>
            </div>

            <!-- Tier Progress -->
            @if($loyaltyProgram->getPointsToNextTier())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tier Progress</h6>
                </div>
                <div class="card-body">
                    <p class="small">Points to next tier: {{ number_format($loyaltyProgram->getPointsToNextTier(), 0, ',', '.') }}</p>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($loyaltyProgram->points_balance / ($loyaltyProgram->points_balance + $loyaltyProgram->getPointsToNextTier())) * 100 }}%"></div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Earning Rate -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Earning Rate</h6>
                </div>
                <div class="card-body">
                    <p class="small">You earn {{ $loyaltyProgram->getEarningRate() }} points per Rp 1,000 spent</p>
                    <p class="text-muted small">Based on {{ ucfirst($loyaltyProgram->program_type) }} program type</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
