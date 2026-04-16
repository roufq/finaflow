@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add Loyalty Program</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Program Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rewards.store-loyalty') }}">
                        @csrf

                        <div class="form-group">
                            <label for="program_name">Program Name</label>
                            <input type="text" class="form-control @error('program_name') is-invalid @enderror" id="program_name" name="program_name" value="{{ old('program_name') }}" placeholder="e.g., GarudaMiles, Marriott Bonvoy, Starbucks Rewards" required>
                            @error('program_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="program_type">Program Type</label>
                            <select class="form-control @error('program_type') is-invalid @enderror" id="program_type" name="program_type" required>
                                <option value="">Select Program Type</option>
                                <option value="airline" {{ old('program_type') == 'airline' ? 'selected' : '' }}>Airline (Miles)</option>
                                <option value="hotel" {{ old('program_type') == 'hotel' ? 'selected' : '' }}>Hotel (Points)</option>
                                <option value="credit_card" {{ old('program_type') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="retail" {{ old('program_type') == 'retail' ? 'selected' : '' }}>Retail/Store</option>
                            </select>
                            @error('program_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="membership_number">Membership Number (Optional)</label>
                            <input type="text" class="form-control @error('membership_number') is-invalid @enderror" id="membership_number" name="membership_number" value="{{ old('membership_number') }}" placeholder="Your membership/account number">
                            @error('membership_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="expiry_date">Membership Expiry Date (Optional)</label>
                            <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}">
                            @error('expiry_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success">Save Loyalty Program</button>
                        <a href="{{ route('rewards.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Popular Programs</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6>Airline Programs:</h6>
                            <ul class="small mb-3">
                                <li>GarudaMiles (Garuda Indonesia)</li>
                                <li>Lion Air Miles</li>
                                <li>AirAsia BIG</li>
                                <li>Citilink Miles</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Hotel Programs:</h6>
                            <ul class="small mb-3">
                                <li>Marriott Bonvoy</li>
                                <li>Hilton Honors</li>
                                <li>IHG Rewards Club</li>
                                <li>Accor Live Limitless</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <h6>Retail Programs:</h6>
                            <ul class="small mb-3">
                                <li>Starbucks Rewards</li>
                                <li>Indomaret Card</li>
                                <li>Alfamart Point</li>
                                <li>Bank Mega Rewards</li>
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
                        <li>Check for sign-up bonuses when joining new programs</li>
                        <li>Use points before they expire</li>
                        <li>Combine programs for maximum benefits</li>
                        <li>Track your points balance regularly</li>
                        <li>Look for transfers partnerships between programs</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
