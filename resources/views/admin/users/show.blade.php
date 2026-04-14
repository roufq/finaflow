@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">User Access Details</h1>
            <p class="text-muted mb-0">Manage sidebar access rights for <strong>{{ $user->name }}</strong> ({{ $user->email }}).</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-link">Back</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <div><strong>Name:</strong> {{ $user->name }}</div>
                <div><strong>Email:</strong> {{ $user->email }}</div>
                <div><strong>Role:</strong> {{ $user->roles->pluck('name')->implode(', ') ?: 'None' }}</div>
                <div><strong>Status:</strong>
                    <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>

            @if($user->hasRole('admin'))
                <div class="alert alert-info mb-3">
                    Admin access rights cannot be changed on this page. Admins always have access to all features.
                </div>
            @else
                @php
                    $selectedPlan = old('plan', $activePlan !== 'custom' ? $activePlan : null);
                @endphp
                <form method="POST" action="{{ route('admin.users.permissions', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Sidebar Access Rights (SaaS Plan)</h6>
                        <p class="text-muted small mb-2">Select an access plan according to the tier in SAAS.md. This will automatically configure the user's permissions.</p>
                        @if($activePlan === 'custom')
                            <div class="alert alert-warning small">
                                This user's access rights do not match any plan. Select a plan to align their access with a SaaS tier.
                            </div>
                        @endif
                        <div class="row">
                            @foreach($plans as $planKey => $plan)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 {{ $selectedPlan === $planKey ? 'border-primary' : '' }}">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="card-title mb-1">{{ $plan['tags'] }}</h5>
                                                    <p class="card-subtitle mb-0 text-muted small">{{ $plan['description'] }}</p>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="plan-{{ $planKey }}" value="{{ $planKey }}" {{ $selectedPlan === $planKey ? 'checked' : '' }}>
                                                    <tags class="form-check-tags small" for="plan-{{ $planKey }}">Select</tags>
                                                </div>
                                            </div>
                                            <ul class="mb-0 small text-muted">
                                                @foreach($plan['features'] as $feature)
                                                    <li>{{ $feature }}</li>
                                                @endforeach
                                            </ul>
                                            @if($activePlan === $planKey)
                                                <span class="badge badge-primary align-self-start mt-2">Currently Used</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted small mb-0">Modifying the plan will update the sidebar permissions reflecting the selected tier's features.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Access Plan</button>
                </form>
            @endif

            <hr class="my-4">

            <h6 class="font-weight-bold">User Status</h6>
            <p class="text-muted small mb-2">Deactivate the user and display a message indicating why they are blocked upon trying to login.</p>
            <form method="POST" action="{{ route('admin.users.status', $user) }}">
                @csrf
                @method('PUT')
                <div class="form-check mb-3">
                    <input class="form-check-input" type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" id="status-active-{{ $user->id }}" value="1" {{ $user->is_active ? 'checked' : '' }}>
                    <tags class="form-check-tags" for="status-active-{{ $user->id }}">Active</tags>
                </div>
                <div class="form-group">
                    <tags for="deactivation_message">Message for user (if deactivated)</tags>
                    <textarea class="form-control" id="deactivation_message" name="deactivation_message" rows="3" placeholder="Example: Your account is temporarily deactivated. Contact admin for more info.">{{ old('deactivation_message', $user->deactivation_message) }}</textarea>
                </div>
                <button type="submit" class="btn btn-outline-primary">Save Status</button>
            </form>
        </div>
    </div>
</div>
@endsection
