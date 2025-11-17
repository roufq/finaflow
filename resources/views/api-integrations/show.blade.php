@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ ucfirst(str_replace('_',' ', $apiIntegration->provider)) }}</h6>
        </div>
        <div class="card-body">
            <p><strong>Status:</strong> {{ $apiIntegration->is_active ? 'Active' : 'Inactive' }}</p>
            <p><strong>Last Sync:</strong> {{ $apiIntegration->last_sync_at?->diffForHumans() ?? 'Never' }}</p>
            <p><strong>Rate Limit Remaining:</strong> {{ $apiIntegration->rate_limit_remaining ?? '—' }}</p>
            <p><strong>Notes:</strong> {{ data_get($apiIntegration->settings, 'notes', '—') }}</p>
        </div>
    </div>
</div>
@endsection
