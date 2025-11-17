@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit API Integration</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('api-integrations.update', $apiIntegration) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Provider</label>
                            <select name="provider" class="form-control">
                                @foreach(['credit_score','investment_data','news','weather'] as $provider)
                                    <option value="{{ $provider }}" @selected($apiIntegration->provider === $provider)>{{ ucfirst(str_replace('_',' ', $provider)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>API Key</label>
                            <input type="text" name="api_key" value="{{ old('api_key', $apiIntegration->api_key) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Notes / Settings</label>
                            <textarea name="settings[notes]" class="form-control" rows="3">{{ data_get($apiIntegration->settings, 'notes') }}</textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ $apiIntegration->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('api-integrations.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h6>Status</h6>
                    <p class="mb-1">Last Sync: {{ $apiIntegration->last_sync_at?->diffForHumans() ?? 'Never' }}</p>
                    <p class="mb-1">Rate Limit Remaining: {{ $apiIntegration->rate_limit_remaining ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
