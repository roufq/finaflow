@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $bankIntegration->bank_name }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Account Details</h6>
                    <p class="mb-1"><strong>Account Number:</strong> {{ $bankIntegration->account_number ?? '—' }}</p>
                    <p class="mb-1"><strong>Account Type:</strong> {{ ucfirst($bankIntegration->account_type) }}</p>
                    <p class="mb-1"><strong>Integration:</strong> {{ strtoupper($bankIntegration->integration_type) }}</p>
                    <p class="mb-1"><strong>Status:</strong> {{ $bankIntegration->is_active ? 'Active' : 'Inactive' }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Sync</h6>
                    <p class="mb-1">Last Sync: {{ $bankIntegration->last_sync_at?->diffForHumans() ?? 'Never' }}</p>
                    <p class="mb-1">Current Balance: Rp {{ number_format($bankIntegration->current_balance ?? 0, 0, ',', '.') }}</p>
                    <p class="mb-1">Available Balance: Rp {{ number_format($bankIntegration->available_balance ?? 0, 0, ',', '.') }}</p>
                    <p class="mb-1">Last Balance Sync: {{ $bankIntegration->last_balance_sync_at?->diffForHumans() ?? 'Never' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
