@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">API Integrations</h1>
        <a href="{{ route('api-integrations.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Add Integration
        </a>
    </div>

    <div class="row">
        @forelse($integrations as $integration)
            <div class="col-lg-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <h5 class="font-weight-bold text-primary">{{ ucfirst(str_replace('_', ' ', $integration->provider)) }}</h5>
                            <span class="badge badge-{{ $integration->is_active ? 'success' : 'secondary' }}">{{ $integration->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                        <p class="text-muted mb-2">Last Sync: {{ $integration->last_sync_at?->diffForHumans() ?? 'Never' }}</p>
                        <p class="text-muted mb-0">Rate Limit Remaining: {{ $integration->rate_limit_remaining ?? '—' }}</p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <div>
                            <button class="btn btn-sm btn-outline-primary" data-sync="{{ route('api-integrations.sync', $integration) }}">
                                <i class="fas fa-sync"></i>
                            </button>
                            <a href="{{ route('api-integrations.edit', $integration) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                        <form method="POST" action="{{ route('api-integrations.destroy', $integration) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete integration?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-plug fa-3x text-gray-300 mb-3"></i>
                        <h4>No integrations yet</h4>
                        <p class="text-muted">Connect to data providers for credit scores, investment data, news, and weather insights.</p>
                        <a href="{{ route('api-integrations.create') }}" class="btn btn-primary">Add Integration</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
document.querySelectorAll('[data-sync]').forEach(btn => {
    btn.addEventListener('click', async () => {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        try {
            const response = await fetch(btn.dataset.sync, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            });
            const data = await response.json();
            alert(data.message || 'Synced');
            window.location.reload();
        } catch (error) {
            alert('Sync failed');
        }
    });
});
</script>
@endsection
