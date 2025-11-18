@extends('layouts.app')

@section('title', __('insights.recommendations.title'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('insights.recommendations.title') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('insights.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> {{ __('common.back') }}
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recommendations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('insights.recommendations.type') }}</th>
                                        <th>{{ __('insights.recommendations.content') }}</th>
                                        <th>{{ __('insights.recommendations.priority') }}</th>
                                        <th>{{ __('insights.recommendations.status') }}</th>
                                        <th>{{ __('common.created_at') }}</th>
                                        <th>{{ __('common.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recommendations as $recommendation)
                                        <tr>
                                            <td>
                                                <span class="badge badge-info">{{ ucfirst($recommendation->type) }}</span>
                                            </td>
                                            <td>{{ $recommendation->content }}</td>
                                            <td>
                                                <span class="badge badge-{{ $recommendation->priority >= 3 ? 'danger' : ($recommendation->priority >= 2 ? 'warning' : 'success') }}">
                                                    {{ __('insights.recommendations.priority_' . $recommendation->priority) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($recommendation->is_read)
                                                    <span class="badge badge-success">{{ __('insights.recommendations.read') }}</span>
                                                @else
                                                    <span class="badge badge-warning">{{ __('insights.recommendations.unread') }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $recommendation->created_at->format('M d, Y') }}</td>
                                            <td>
                                                @if(!$recommendation->is_read)
                                                    <form action="{{ route('insights.recommendations.read', $recommendation) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-check"></i> {{ __('insights.recommendations.mark_as_read') }}
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
                            {{ $recommendations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-lightbulb fa-3x text-muted mb-3"></i>
                            <h4>{{ __('insights.no_recommendations') }}</h4>
                            <p class="text-muted">{{ __('insights.generate_recommendations_hint') }}</p>
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
