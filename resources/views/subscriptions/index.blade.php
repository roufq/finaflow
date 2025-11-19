@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('subscriptions.title') }}</h1>
        <a href="{{ route('subscriptions.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{ __('subscriptions.buttons.add_subscription') }}
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('subscriptions.summary.total') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $subscriptions->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('subscriptions.summary.monthly_cost') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalMonthlyCost, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('subscriptions.summary.renewing_soon') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $renewingSoon->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ __('subscriptions.summary.potential_savings') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($potentialSavings, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-piggy-bank fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('subscriptions.table.heading') }}</h6>
        </div>
        <div class="card-body">
            @if($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('subscriptions.table.columns.name') }}</th>
                                <th>{{ __('subscriptions.table.columns.provider') }}</th>
                                <th>{{ __('subscriptions.table.columns.amount') }}</th>
                                <th>{{ __('subscriptions.table.columns.frequency') }}</th>
                                <th>{{ __('subscriptions.table.columns.next_billing') }}</th>
                                <th>{{ __('subscriptions.table.columns.status') }}</th>
                                <th>{{ __('subscriptions.table.columns.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $subscription->name }}</td>
                                <td>{{ $subscription->provider }}</td>
                                <td>Rp {{ number_format($subscription->amount, 0, ',', '.') }}</td>
                                <td>{{ ucfirst($subscription->frequency) }}</td>
                                <td>
                                    {{ $subscription->next_billing_date->format('d M Y') }}
                                    @if($subscription->next_billing_date->isPast())
                                        <span class="badge badge-danger">Overdue</span>
                                    @elseif($subscription->next_billing_date->diffInDays() <= 7)
                                        <span class="badge badge-warning">Soon</span>
                                    @endif
                                </td>
                                <td>
                                    @if($subscription->auto_renewal)
                                        <span class="badge badge-success">{{ __('subscriptions.badges.auto') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ __('subscriptions.badges.manual') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('subscriptions.edit', $subscription) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="deleteSubscription({{ $subscription->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-sync-alt fa-4x text-primary mb-3"></i>
                    <h4>{{ __('subscriptions.empty.title') }}</h4>
                    <p class="text-muted">{{ __('subscriptions.empty.description') }}</p>
                    <a href="{{ route('subscriptions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('subscriptions.empty.cta') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Optimization Suggestions -->
    @if($potentialSavings > 0)
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('subscriptions.tips.title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>{{ __('subscriptions.tips.bundle_title') }}</strong>
                        {{ __('subscriptions.tips.bundle_message', ['amount' => number_format($potentialSavings, 0, ',', '.')]) }}
                    </div>
                    <div class="alert alert-success">
                        <strong>{{ __('subscriptions.tips.smart_title') }}</strong>
                        {{ __('subscriptions.tips.smart_message') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
function deleteSubscription(id) {
    if (confirm(@json(__('subscriptions.messages.delete_confirm')))) {
        // Create a form to submit DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/subscriptions/${id}`;

        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
        }

        // Add method spoofing for DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
