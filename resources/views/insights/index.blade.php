@extends('layouts.app')

@section('title', __('insights.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('insights.title') }}</h3>
                    <div class="card-tools">
                        <form id="generate-insights-form" method="POST" action="{{ route('insights.generate') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" id="generate-insights-btn">
                                <i class="fas fa-sync-alt"></i> {{ __('insights.generate_insights') }}
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <!-- Recommendations -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-lightbulb text-warning"></i> {{ __('insights.recommendations.title') }}
                                    </h5>
                                    <div class="card-tools">
                                        <a href="{{ route('insights.recommendations') }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('common.view_all') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @forelse($recommendations as $recommendation)
                                        <div class="alert alert-{{ $recommendation->priority >= 3 ? 'warning' : 'info' }} mb-2">
                                            <strong>{{ __('insights.recommendations.priority_' . $recommendation->priority) }}</strong>
                                            <p class="mb-1">{{ $recommendation->content }}</p>
                                            <small class="text-muted">{{ $recommendation->created_at->diffForHumans() }}</small>
                                        </div>
                                    @empty
                                        <p class="text-muted">{{ __('insights.no_recommendations') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Anomalies -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-exclamation-triangle text-danger"></i> {{ __('insights.anomalies.title') }}
                                    </h5>
                                    <div class="card-tools">
                                        <a href="{{ route('insights.anomalies') }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('common.view_all') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @forelse($anomalies as $anomaly)
                                        <div class="alert alert-{{ $anomaly->severity >= 3 ? 'danger' : ($anomaly->severity >= 2 ? 'warning' : 'info') }} mb-2">
                                            <strong>{{ $anomaly->severity_tags }}</strong>
                                            <p class="mb-1">{{ $anomaly->description }}</p>
                                            <small class="text-muted">{{ $anomaly->detected_at->diffForHumans() }}</small>
                                        </div>
                                    @empty
                                        <p class="text-muted">{{ __('insights.no_anomalies') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Predictions -->
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fas fa-chart-line text-success"></i> {{ __('insights.predictions.title') }}
                                    </h5>
                                    <div class="card-tools">
                                        <a href="{{ route('insights.predictions') }}" class="btn btn-sm btn-outline-primary">
                                            {{ __('common.view_all') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @forelse($predictions as $prediction)
                                        <div class="alert alert-success mb-2">
                                            <strong>{{ $prediction->category }}</strong>
                                            <p class="mb-1">
                                                {{ __('insights.predictions.expected_amount', [
                                                    'amount' => number_format($prediction->predicted_amount, 0),
                                                    'period' => $prediction->period_tags
                                                ]) }}
                                            </p>
                                            <small class="text-muted">
                                                {{ __('insights.predictions.confidence') }}: {{ $prediction->confidence_percentage }}%
                                            </small>
                                        </div>
                                    @empty
                                        <p class="text-muted">{{ __('insights.no_predictions') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('generate-insights-form');
    const button = document.getElementById('generate-insights-btn');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
        button.disabled = true;

        // Create FormData from the form
        const formData = new FormData(form);

        // Make AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.headers.get('content-type')?.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, treat as success and reload
                window.location.reload();
                return;
            }
        })
        .then(data => {
            if (data && data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <strong>Success!</strong> ${data.message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                `;

                const cardBody = document.querySelector('.card-body');
                cardBody.insertBefore(alertDiv, cardBody.firstChild);

                // Reload the page after 2 seconds to show updated insights
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else if (data && !data.success) {
                throw new Error(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <strong>Error!</strong> Failed to generate insights: ${error.message}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            `;

            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertDiv, cardBody.firstChild);
        })
        .finally(() => {
            // Restore button state
            button.innerHTML = originalText;
            button.disabled = false;
        });
    });
});
</script>
@endsection
