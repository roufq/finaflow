@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Reward Card</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Card Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rewards.store') }}">
                        @csrf

                        <div class="form-group">
                            <label for="card_type">Card Type</label>
                            <select class="form-control @error('card_type') is-invalid @enderror" id="card_type" name="card_type" required>
                                <option value="">Select Card Type</option>
                                <option value="Visa" {{ old('card_type') == 'Visa' ? 'selected' : '' }}>Visa</option>
                                <option value="Mastercard" {{ old('card_type') == 'Mastercard' ? 'selected' : '' }}>Mastercard</option>
                                <option value="American Express" {{ old('card_type') == 'American Express' ? 'selected' : '' }}>American Express</option>
                                <option value="JCB" {{ old('card_type') == 'JCB' ? 'selected' : '' }}>JCB</option>
                                <option value="Other" {{ old('card_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('card_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="reward_type">Reward Type</label>
                            <select class="form-control @error('reward_type') is-invalid @enderror" id="reward_type" name="reward_type" required>
                                <option value="">Select Reward Type</option>
                                <option value="Cashback" {{ old('reward_type') == 'Cashback' ? 'selected' : '' }}>Cashback</option>
                                <option value="Points" {{ old('reward_type') == 'Points' ? 'selected' : '' }}>Points</option>
                                <option value="Miles" {{ old('reward_type') == 'Miles' ? 'selected' : '' }}>Miles</option>
                                <option value="Coins" {{ old('reward_type') == 'Coins' ? 'selected' : '' }}>Coins</option>
                                <option value="Other" {{ old('reward_type') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('reward_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="points_earned">Points Earned</label>
                            <input type="number" class="form-control @error('points_earned') is-invalid @enderror" id="points_earned" name="points_earned" value="{{ old('points_earned', 0) }}" required>
                            @error('points_earned')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="points_redeemed">Points Redeemed</label>
                            <input type="number" class="form-control @error('points_redeemed') is-invalid @enderror" id="points_redeemed" name="points_redeemed" value="{{ old('points_redeemed', 0) }}" required>
                            @error('points_redeemed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="cashback_amount">Cashback Amount (Rp)</label>
                            <input type="number" class="form-control @error('cashback_amount') is-invalid @enderror" id="cashback_amount" name="cashback_amount" value="{{ old('cashback_amount', 0) }}" step="0.01" required>
                            @error('cashback_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expiry_date">Points Expiry Date (Optional)</label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="notes">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Card benefits, bonus categories, etc.">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warning">Save Card</button>
                        <a href="{{ route('rewards.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Popular Reward Cards</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6>Cashback Cards:</h6>
                            <ul class="small mb-3">
                                <li>General Purpose (1-2% everywhere)</li>
                                <li>Category Specialists (5%+ in specific categories)</li>
                                <li>Rotating Categories (monthly bonus categories)</li>
                                <li>Foreign Transaction Fee-Free</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Points Cards:</h6>
                            <ul class="small mb-3">
                                <li>Travel Rewards (airlines, hotels)</li>
                                <li>Transferable Points (to multiple programs)</li>
                                <li>Flexible Redemption (cash, travel, merchandise)</li>
                                <li>Business Cards (higher earning potential)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li>Track your spending to maximize rewards</li>
                        <li>Pay your balance in full each month</li>
                        <li>Check for sign-up bonuses</li>
                        <li>Use cards with no foreign transaction fees when traveling</li>
                        <li>Redeem points before they expire</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
