@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ $automation->name }}</h6>
            <span class="badge badge-{{ $automation->is_active ? 'success' : 'secondary' }}">{{ $automation->is_active ? 'Active' : 'Inactive' }}</span>
        </div>
        <div class="card-body">
            <p><strong>Type:</strong> {{ ucfirst($automation->type) }}</p>
            <p><strong>Description:</strong> {{ $automation->description ?? '—' }}</p>
            <p><strong>Last Run:</strong> {{ $automation->last_run_at?->diffForHumans() ?? 'Never' }}</p>
            <p><strong>Run Count:</strong> {{ $automation->run_count }}</p>

            <div class="row">
                <div class="col-md-6">
                    <h6>Conditions</h6>
                    <pre class="bg-light p-3">{{ json_encode($automation->conditions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
                <div class="col-md-6">
                    <h6>Actions</h6>
                    <pre class="bg-light p-3">{{ json_encode($automation->actions, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
