@extends('layouts.app')

@section('title', __('insights.anomalies.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('insights.anomalies.title') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('insights.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('common.back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($anomalies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('insights.anomalies.type') }}</th>
                                        <th>{{ __('insights.anomalies.description') }}</th>
                                        <th>{{ __('insights.anomalies.severity') }}</th>
                                        <th>{{ __('insights.anomalies.status') }}</th>
                                        <th>{{ __('insights.anomalies.detected_at') }}</th>
                                        <th>{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anomalies as $anomaly)
                                        <tr>
                                            <td>
                                                <span class="badge badge-secondary">{{ ucfirst(str_replace('_', ' ', $anomaly->anomaly_type)) }}</span>
                                            </td>
                                            <td>{{ $anomaly->description }}</td>
                                            <td>
                                                <span class="badge badge-{{ $anomaly->severity >= 3 ? 'danger' : ($anomaly->severity >= 2 ? 'warning' : 'info') }}">
                                                    {{ $anomaly->severity_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($anomaly->is_resolved)
                                                    <span class="badge badge-success">{{ __('insights.anomalies.resolved') }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ __('insights.anomalies.unresolved') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $anomaly->detected_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                @if(!$anomaly->is_resolved)
                                                    <form action="{{ route('insights.anomalies.resolve', $anomaly) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-check"></i> {{ __('insights.anomalies.mark_resolved') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $anomalies->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                            <h4>{{ __('insights.no_anomalies') }}</h4>
                            <p class="text-muted">{{ __('insights.generate_anomalies_hint') }}</p>
                            <form method="POST" action="{{ route('insights.generate') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-sync-alt"></i> {{ __('insights.generate_insights') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
