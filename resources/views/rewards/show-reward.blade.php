@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $reward->card_type }} Card</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Card Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Card Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Card Type:</strong> {{ $reward->card_type }}</p>
                            <p><strong>Reward Type:</strong> {{ ucfirst($reward->reward_type) }}</p>
                            <p><strong>Status:</strong>
                                <span class="badge badge-{{ $reward->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($reward->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Points Earned:</strong> {{ number_format($reward->points_earned, 0, ',', '.') }}</p>
                            <p><strong>Points Redeemed:</strong> {{ number_format($reward->points_redeemed, 0, ',', '.') }}</p>
                            <p><strong>Available Points:</strong> {{ number_format($reward->getAvailablePoints(), 0, ',', '.') }}</p>
                            <p><strong>Cashback Amount:</strong> Rp {{ number_format($reward->cashback_amount, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    @if($reward->expiry_date)
                    <div class="row mt-3">
                        <div class="col-12">
                            <p><strong>Points Expiry:</strong> {{ $reward->expiry_date->format('M d, Y') }}</p>
                            <p><strong>Days Until Expiry:</strong>
                                @if($reward->getDaysUntilExpiry() !== null)
                                    @if($reward->getDaysUntilExpiry() > 0)
                                        <span class="text-success">{{ $reward->getDaysUntilExpiry() }} days</span>
                                    @else
                                        <span class="text-danger">Expired</span>
                                    @endif
                                @else
                                    Never expires
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Transaction History -->
            @if($reward->transaction_history)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transaction History</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(array_reverse($reward->transaction_history) as $transaction)
                                    <tr>
                                        <td>{{ $transaction['date'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $transaction['type'] == 'earned' ? 'success' : 'warning' }}">
                                                {{ ucfirst($transaction['type']) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($transaction['type'] == 'cashback')
                                                Rp {{ number_format($transaction['amount'], 0, ',', '.') }}
                                            @else
                                                {{ number_format($transaction['amount'], 0, ',', '.') }} points
                                            @endif
                                        </td>
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
                    @if($reward->reward_type == 'points' && $reward->getAvailablePoints() > 0)
                    <form method="POST" action="{{ route('rewards.redeem-reward', $reward) }}" class="mb-3">
                        @csrf
                        <div class="form-group">
                            <label for="points">Redeem Points</label>
                            <input type="number" class="form-control" id="points" name="points" placeholder="Points to redeem" min="1" max="{{ $reward->getAvailablePoints() }}" required>
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">Redeem Points</button>
                    </form>
                    @endif

                    <a href="{{ route('rewards.edit-reward', $reward) }}" class="btn btn-primary btn-block mb-2">Edit Card</a>
                    <form method="POST" action="{{ route('rewards.destroy-reward', $reward) }}" onsubmit="return confirm('Are you sure you want to delete this reward card?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">Delete Card</button>
                    </form>
                </div>
            </div>

            <!-- Reward Calculator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Reward Calculator</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="calc_amount">Transaction Amount (Rp)</label>
                        <input type="number" class="form-control" id="calc_amount" placeholder="Enter amount">
                    </div>
                    <button type="button" class="btn btn-info btn-block" onclick="calculateRewards()">Calculate Rewards</button>
                    <div id="calc_result" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Potential Rewards:</strong><br>
                            <span id="points_reward"></span><br>
                            <span id="cashback_reward"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Benefits -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Card Benefits</h6>
                </div>
                <div class="card-body">
                    <ul class="small">
                        @if($reward->card_type == 'Visa')
                            <li>Widely accepted worldwide</li>
                            <li>Good for online shopping</li>
                            <li>Contactless payments</li>
                        @elseif($reward->card_type == 'Mastercard')
                            <li>Global acceptance</li>
                            <li>Strong rewards programs</li>
                            <li>Travel benefits</li>
                        @elseif($reward->card_type == 'American Express')
                            <li>Premium travel rewards</li>
                            <li>Dining and entertainment perks</li>
                            <li>Excellent customer service</li>
                        @else
                            <li>Check specific card benefits</li>
                            <li>Contact issuer for details</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function calculateRewards() {
    const amount = document.getElementById('calc_amount').value;
    if (!amount || amount <= 0) {
        alert('Please enter a valid amount');
        return;
    }

    const rewards = @json($reward->calculatePotentialReward(parseFloat(amount)));

    document.getElementById('calc_result').style.display = 'block';

    if (rewards.points) {
        document.getElementById('points_reward').textContent = `Points: ${rewards.points.toLocaleString()}`;
    } else {
        document.getElementById('points_reward').textContent = '';
    }

    if (rewards.cashback) {
        document.getElementById('cashback_reward').textContent = `Cashback: Rp ${rewards.cashback.toLocaleString()}`;
    } else {
        document.getElementById('cashback_reward').textContent = '';
    }
}
</script>
@endsection
