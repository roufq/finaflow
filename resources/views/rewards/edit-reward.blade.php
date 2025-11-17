@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Reward Card</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Card Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rewards.update-reward', $reward) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="card_type">Card Type</label>
                            <select class="form-control @error('card_type') is-invalid @enderror" id="card_type" name="card_type" required>
                                <option value="Visa" {{ old('card_type', $reward->card_type) == 'Visa' ? 'selected' : '' }}>Visa</option>
                                <option value="Mastercard" {{ old('card_type', $reward->card_type) == 'Mastercard' ? 'selected' : '' }}>Mastercard</option>
                                <option value="American Express" {{ old('card_type', $reward->card_type) == 'American Express' ? 'selected' : '' }}>American Express</option>
                                <option value="JCB" {{ old('card_type', $reward->card_type) == 'JCB' ? 'selected' : '' }}>JCB</option>
                                <option value="Other" {{ old('card_type', $reward->card_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('card_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reward_type">Reward Type</label>
                            <select class="form-control @error('reward_type') is-invalid @enderror" id="reward_type" name="reward_type" required>
                                <option value="Cashback" {{ old('reward_type', $reward->reward_type) == 'Cashback' ? 'selected' : '' }}>Cashback</option>
                                <option value="Points" {{ old('reward_type', $reward->reward_type) == 'Points' ? 'selected' : '' }}>Points</option>
                                <option value="Miles" {{ old('reward_type', $reward->reward_type) == 'Miles' ? 'selected' : '' }}>Miles</option>
                                <option value="Coins" {{ old('reward_type', $reward->reward_type) == 'Coins' ? 'selected' : '' }}>Coins</option>
                                <option value="Other" {{ old('reward_type', $reward->reward_type) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('reward_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="points_earned">Points Earned</label>
                            <input type="number" class="form-control @error('points_earned') is-invalid @enderror" id="points_earned" name="points_earned" value="{{ old('points_earned', $reward->points_earned) }}" required>
                            @error('points_earned')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="points_redeemed">Points Redeemed</label>
                            <input type="number" class="form-control @error('points_redeemed') is-invalid @enderror" id="points_redeemed" name="points_redeemed" value="{{ old('points_redeemed', $reward->points_redeemed) }}" required>
                            @error('points_redeemed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="cashback_amount">Cashback Amount (Rp)</label>
                            <input type="number" class="form-control @error('cashback_amount') is-invalid @enderror" id="cashback_amount" name="cashback_amount" value="{{ old('cashback_amount', $reward->cashback_amount) }}" step="0.01" required>
                            @error('cashback_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expiry_date">Points Expiry Date (Optional)</label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $reward->expiry_date ? $reward->expiry_date->format('Y-m-d') : '') }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $reward->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="expired" {{ old('status', $reward->status) == 'expired' ? 'selected' : '' }}>Expired</option>
                                <option value="redeemed" {{ old('status', $reward->status) == 'redeemed' ? 'selected' : '' }}>Redeemed</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-warning">Update Card</button>
                        <a href="{{ route('rewards.show-reward', $reward) }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Card Info</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6>Available Points:</h6>
                            <p class="text-primary font-weight-bold">{{ number_format($reward->getAvailablePoints(), 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Cashback Rate:</h6>
                            <p class="text-info">{{ $reward->getCashbackRate($reward->card_type) * 100 }}%</p>
                        </div>
                    </div>

                    @if($reward->expiry_date)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Days Until Expiry:</h6>
                            <p class="{{ $reward->isExpired() ? 'text-danger' : 'text-success' }}">
                                @if($reward->getDaysUntilExpiry() !== null)
                                    {{ $reward->getDaysUntilExpiry() > 0 ? $reward->getDaysUntilExpiry() . ' days' : 'Expired' }}
                                @else
                                    Never
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($reward->isExpired())
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <small>This card has expired points!</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('rewards.show-reward', $reward) }}" class="btn btn-info btn-block mb-2">View Details</a>
                    <form method="POST" action="{{ route('rewards.destroy-reward', $reward) }}" onsubmit="return confirm('Are you sure you want to delete this reward card?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">Delete Card</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
