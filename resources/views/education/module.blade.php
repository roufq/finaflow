@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ $module->title }}</h1>
            <p class="text-muted mb-0 small">
                {{ ucfirst($module->category) }} &middot; {{ ucfirst($module->difficulty) }} &middot; {{ $module->estimated_time }} min &middot; {{ strtoupper($module->language) }}
            </p>
        </div>
        <a href="{{ route('education.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('navigation.financial_education') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @error('progress')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('education.module.overview') }}</h6>
                </div>
                <div class="card-body">
                    @if(!empty($module->tag_list))
                        <div class="mb-3">
                            @foreach($module->tag_list as $tag)
                                <span class="badge badge-secondary mr-1 mb-1">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif

                    {!! nl2br(e($module->content)) !!}

                    @if(!empty($module->objective_list))
                        <hr>
                        <h6 class="text-uppercase text-xs text-muted">{{ __('education.module.objectives') }}</h6>
                        <ul class="mb-0">
                            @foreach($module->objective_list as $objective)
                                <li>{{ $objective }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($module->resource_list))
                        <hr>
                        <h6 class="text-uppercase text-xs text-muted">{{ __('education.module.resources') }}</h6>
                        <ul class="mb-0">
                            @foreach($module->resource_list as $resource)
                                @php
                                    $label = is_array($resource) ? ($resource['tags'] ?? ($resource['url'] ?? __('education.module.resources'))) : $resource;
                                    $url = is_array($resource) ? ($resource['url'] ?? null) : null;
                                @endphp
                                <li>
                                    @if($url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener">{{ $label }}</a>
                                    @else
                                        {{ $label }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('education.module.progress') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-1">{{ __('education.module.progress') }}</p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong class="h4 mb-0">{{ $path->progress }}%</strong>
                        @if($path->is_completed)
                            <span class="badge badge-success">{{ __('education.metrics.completed') }}</span>
                        @endif
                    </div>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $path->progress }}%"></div>
                    </div>
                    <form method="POST" action="{{ route('education.module.progress', $module) }}">
                        @csrf
                        <div class="form-group">
                            <label for="progress-range" class="text-xs text-muted">{{ __('education.module.cta') }}</label>
                            <input type="range" class="custom-range" id="progress-range" name="progress" min="0" max="100" value="{{ $path->progress }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">{{ __('education.module.cta') }}</button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('education.module.related') }}</h6>
                </div>
                <div class="card-body">
                    @forelse($relatedModules as $related)
                        <div class="mb-3">
                            <a href="{{ route('education.module', $related) }}" class="font-weight-bold d-block">{{ $related->title }}</a>
                            <small class="text-muted">{{ ucfirst($related->difficulty) }} &middot; {{ $related->estimated_time }} min</small>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <p class="text-muted mb-0">{{ __('education.recommendations.empty') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
