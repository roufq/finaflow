@extends('layouts.app')

@section('title', __('insights.predictions.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('insights.predictions.title') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('insights.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('common.back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($predictions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('insights.predictions.category') }}</th>
                                        <th>{{ __('insights.predictions.predicted_amount') }}</th>
                                        <th>{{ __('insights.predictions.confidence') }}</th>
                                        <th>{{ __('insights.predictions.period') }}</th>
                                        <th>{{ __('insights.predictions.prediction_date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($predictions as $prediction)
                                        <tr>
                                            <td>
                                                <span class="badge badge-primary">{{ $prediction->category }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($prediction->predicted_amount, 0) }}</strong>
                                            </td>
                                            <td>
                                                <div class="progress" style="width: 100px;">
                                                    <div class="progress-bar bg-{{ $prediction->confidence >= 0.8 ? 'success' : ($prediction->confidence >= 0.6 ? 'warning' : 'danger') }}"
                                                         role="progressbar"
                                                         style="width: {{ $prediction->confidence_percentage }}%"
                                                         aria-valuenow="{{ $prediction->confidence_percentage }}"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                        {{ $prediction->confidence_percentage }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $prediction->period_tags }}</span>
                                            </td>
                                            <td>{{ $prediction->prediction_date->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center">
                            {{ $predictions->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h4>{{ __('insights.no_predictions') }}</h4>
                            <p class="text-muted">{{ __('insights.generate_predictions_hint') }}</p>
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
