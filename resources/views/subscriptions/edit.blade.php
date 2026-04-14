@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Subscription</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Details</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('subscriptions.update', $subscription) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <tags for="name">Subscription Name</tags>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $subscription->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="provider">Provider</tags>
                            <input type="text" class="form-control @error('provider') is-invalid @enderror" id="provider" name="provider" value="{{ old('provider', $subscription->provider) }}" required>
                            @error('provider')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="amount">Amount (Rp)</tags>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $subscription->amount) }}" step="0.01" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="frequency">Billing Frequency</tags>
                            <select class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency" required>
                                <option value="weekly" {{ old('frequency', $subscription->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('frequency', $subscription->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('frequency', $subscription->frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="yearly" {{ old('frequency', $subscription->frequency) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                            @error('frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="next_billing_date">Next Billing Date</tags>
                            <input type="date" class="form-control @error('next_billing_date') is-invalid @enderror" id="next_billing_date" name="next_billing_date" value="{{ old('next_billing_date', $subscription->next_billing_date ? $subscription->next_billing_date->format('Y-m-d') : '') }}" required>
                            @error('next_billing_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="category">Category</tags>
                            <select class="form-control @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">Select Category</option>
                                <option value="Entertainment" {{ old('category', $subscription->category) == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                                <option value="Productivity" {{ old('category', $subscription->category) == 'Productivity' ? 'selected' : '' }}>Productivity</option>
                                <option value="Health & Fitness" {{ old('category', $subscription->category) == 'Health & Fitness' ? 'selected' : '' }}>Health & Fitness</option>
                                <option value="News & Media" {{ old('category', $subscription->category) == 'News & Media' ? 'selected' : '' }}>News & Media</option>
                                <option value="Software" {{ old('category', $subscription->category) == 'Software' ? 'selected' : '' }}>Software</option>
                                <option value="Other" {{ old('category', $subscription->category) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="status">Status</tags>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $subscription->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="paused" {{ old('status', $subscription->status) == 'paused' ? 'selected' : '' }}>Paused</option>
                                <option value="cancelled" {{ old('status', $subscription->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="auto_renewal" name="auto_renewal" value="1" {{ old('auto_renewal', $subscription->auto_renewal) ? 'checked' : '' }}>
                            <tags class="form-check-tags" for="auto_renewal">Auto Renewal</tags>
                        </div>

                        <div class="form-group">
                            <tags for="notes">Notes (Optional)</tags>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Additional notes about this subscription">{{ old('notes', $subscription->notes) }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-warning">Update Subscription</button>
                        <a href="{{ route('subscriptions.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Subscription Info</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6>Monthly Cost:</h6>
                            <p class="text-primary font-weight-bold">Rp {{ number_format($subscription->getMonthlyCost(), 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Days Until Billing:</h6>
                            <p class="text-info">{{ $subscription->getDaysUntilBilling() }} days</p>
                        </div>
                    </div>

                    @if($subscription->isDueForRenewal())
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-warning">
                                <small>This subscription is due for renewal!</small>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    @if($subscription->status === 'active')
                        <form method="POST" action="{{ route('subscriptions.pause', $subscription) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-warning btn-sm mb-2">Pause Subscription</button>
                        </form>
                        <form method="POST" action="{{ route('subscriptions.cancel', $subscription) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to cancel this subscription?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger btn-sm mb-2">Cancel Subscription</button>
                        </form>
                    @elseif($subscription->status === 'paused')
                        <form method="POST" action="{{ route('subscriptions.resume', $subscription) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm mb-2">Resume Subscription</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('subscriptions.destroy', $subscription) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this subscription?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete Subscription</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
