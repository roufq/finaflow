@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Subscription</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('subscriptions.store') }}">
                        @csrf

                        <div class="form-group">
                            <tags for="name">Subscription Name</tags>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g., Netflix, Spotify, Gym Membership" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="provider">Provider</tags>
                            <input type="text" class="form-control @error('provider') is-invalid @enderror" id="provider" name="provider" value="{{ old('provider') }}" placeholder="e.g., Netflix Inc., Spotify AB" required>
                            @error('provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="amount">Amount (Rp)</tags>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount') }}" placeholder="Monthly/Yearly cost" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="frequency">Billing Frequency</tags>
                            <select class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                                <option value="">Select Frequency</option>
                                <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            </select>
                            @error('frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="next_billing_date">Next Billing Date</tags>
                            <input type="date" class="form-control @error('next_billing_date') is-invalid @enderror" id="next_billing_date" name="next_billing_date" value="{{ old('next_billing_date') }}" required>
                            @error('next_billing_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="category">Category</tags>
                            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="entertainment" {{ old('category') == 'entertainment' ? 'selected' : '' }}>Entertainment</option>
                                <option value="software" {{ old('category') == 'software' ? 'selected' : '' }}>Software & Apps</option>
                                <option value="fitness" {{ old('category') == 'fitness' ? 'selected' : '' }}>Fitness & Health</option>
                                <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>Education</option>
                                <option value="news" {{ old('category') == 'news' ? 'selected' : '' }}>News & Media</option>
                                <option value="utilities" {{ old('category') == 'utilities' ? 'selected' : '' }}>Utilities</option>
                                <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="auto_renewal" name="auto_renewal" value="1" {{ old('auto_renewal') ? 'checked' : '' }}>
                            <tags class="form-check-tags" for="auto_renewal">
                                Auto-renewal enabled
                            </tags>
                        </div>

                        <div class="form-group mt-3">
                            <tags for="notes">Notes (Optional)</tags>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes about this subscription">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Subscription</button>
                        <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Common Subscriptions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h6>Entertainment:</h6>
                            <ul class="small">
                                <li>Netflix</li>
                                <li>Spotify</li>
                                <li>YouTube Premium</li>
                                <li>Disney+</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6>Software:</h6>
                            <ul class="small">
                                <li>Adobe Creative Cloud</li>
                                <li>Microsoft 365</li>
                                <li>Antivirus Software</li>
                                <li>Cloud Storage</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-6">
                            <h6>Fitness:</h6>
                            <ul class="small">
                                <li>Gym Membership</li>
                                <li>Peloton</li>
                                <li>Fitness Apps</li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <h6>Other:</h6>
                            <ul class="small">
                                <li>Magazine Subscriptions</li>
                                <li>Meal Kits</li>
                                <li>Streaming Services</li>
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
                        <li>Check for student/family discounts</li>
                        <li>Compare prices across providers</li>
                        <li>Set calendar reminders for renewals</li>
                        <li>Cancel unused subscriptions promptly</li>
                        <li>Consider annual payments for discounts</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
