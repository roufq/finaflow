@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Loyalty Program</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rewards.update-loyalty', $loyaltyProgram) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <tags for="program_name">Program Name</tags>
                            <input type="text" class="form-control @error('program_name') is-invalid @enderror" id="program_name" name="program_name" value="{{ old('program_name', $loyaltyProgram->program_name) }}" required>
                            @error('program_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="program_type">Program Type</tags>
                            <select class="form-control @error('program_type') is-invalid @enderror" id="program_type" name="program_type" required>
                                <option value="airline" {{ old('program_type', $loyaltyProgram->program_type) == 'airline' ? 'selected' : '' }}>Airline (Miles)</option>
                                <option value="hotel" {{ old('program_type', $loyaltyProgram->program_type) == 'hotel' ? 'selected' : '' }}>Hotel (Points)</option>
                                <option value="credit_card" {{ old('program_type', $loyaltyProgram->program_type) == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="retail" {{ old('program_type', $loyaltyProgram->program_type) == 'retail' ? 'selected' : '' }}>Retail/Store</option>
                            </select>
                            @error('program_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="tier_level">Current Tier Level</tags>
                            <select class="form-control @error('tier_level') is-invalid @enderror" id="tier_level" name="tier_level">
                                <option value="bronze" {{ old('tier_level', $loyaltyProgram->tier_level) == 'bronze' ? 'selected' : '' }}>Bronze</option>
                                <option value="silver" {{ old('tier_level', $loyaltyProgram->tier_level) == 'silver' ? 'selected' : '' }}>Silver</option>
                                <option value="gold" {{ old('tier_level', $loyaltyProgram->tier_level) == 'gold' ? 'selected' : '' }}>Gold</option>
                                <option value="platinum" {{ old('tier_level', $loyaltyProgram->tier_level) == 'platinum' ? 'selected' : '' }}>Platinum</option>
                                <option value="diamond" {{ old('tier_level', $loyaltyProgram->tier_level) == 'diamond' ? 'selected' : '' }}>Diamond</option>
                            </select>
                            @error('tier_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="membership_number">Membership Number (Optional)</tags>
                            <input type="text" class="form-control @error('membership_number') is-invalid @enderror" id="membership_number" name="membership_number" value="{{ old('membership_number', $loyaltyProgram->membership_number) }}">
                            @error('membership_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="expiry_date">Membership Expiry Date (Optional)</tags>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $loyaltyProgram->expiry_date ? $loyaltyProgram->expiry_date->format('Y-m-d') : '') }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-warning">Update Loyalty Program</button>
                        <a href="{{ route('rewards.show-loyalty', $loyaltyProgram) }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Info</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6>Current Points:</h6>
                            <p class="text-primary font-weight-bold">{{ number_format($loyaltyProgram->points_balance, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Earning Rate:</h6>
                            <p class="text-info">{{ $loyaltyProgram->getEarningRate() }} points per Rp 1,000</p>
                        </div>
                    </div>

                    @if($loyaltyProgram->getPointsToNextTier())
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Points to Next Tier:</h6>
                            <p class="text-success">{{ number_format($loyaltyProgram->getPointsToNextTier(), 0, ',', '.') }}</p>
                        </div>
                    </div>
                    @endif

                    @if($loyaltyProgram->isExpired())
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <small>This membership has expired!</small>
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
                    <a href="{{ route('rewards.show-loyalty', $loyaltyProgram) }}" class="btn btn-info btn-block mb-2">View Details</a>
                    <form method="POST" action="{{ route('rewards.destroy-loyalty', $loyaltyProgram) }}" onsubmit="return confirm('Are you sure you want to delete this loyalty program?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">Delete Program</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
