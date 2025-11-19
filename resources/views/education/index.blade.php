@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('education.title') }}</h1>
            <p class="text-muted mb-0 small">{{ __('education.subtitle') }}</p>
        </div>
        <a href="{{ route('education.news') }}" class="btn btn-sm btn-info shadow-sm">
            <i class="fas fa-newspaper fa-sm text-white-50"></i> {{ __('education.news.view_all') }}
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('education.metrics.completed') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $progress['completed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('education.metrics.in_progress') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $progress['in_progress'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
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
                                {{ __('education.metrics.available') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $progress['total_modules'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-layer-group fa-2x text-gray-300"></i>
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
                                {{ __('education.metrics.completion_rate') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $progress['completion_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('education.filters.title') }}</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('education.index') }}">
                <div class="form-row">
                    <div class="form-group col-md-3">
                        <label class="text-xs text-muted text-uppercase">{{ __('education.filters.category') }}</label>
                        <select name="category" class="form-control">
                            <option value="">{{ __('education.filters.all') }}</option>
                            @foreach($filterOptions['categories'] ?? [] as $category)
                                <option value="{{ $category }}" {{ ($filters['category'] ?? null) === $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label class="text-xs text-muted text-uppercase">{{ __('education.filters.difficulty') }}</label>
                        <select name="difficulty" class="form-control">
                            <option value="">{{ __('education.filters.all') }}</option>
                            @foreach($filterOptions['difficulties'] ?? [] as $difficulty)
                                <option value="{{ $difficulty }}" {{ ($filters['difficulty'] ?? null) === $difficulty ? 'selected' : '' }}>
                                    {{ ucfirst($difficulty) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="text-xs text-muted text-uppercase">{{ __('education.filters.language') }}</label>
                        <select name="language" class="form-control">
                            <option value="">{{ __('education.filters.all') }}</option>
                            @foreach($filterOptions['languages'] ?? [] as $language)
                                <option value="{{ $language }}" {{ ($filters['language'] ?? null) === $language ? 'selected' : '' }}>
                                    {{ strtoupper($language) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="text-xs text-muted text-uppercase">{{ __('education.filters.tag') }}</label>
                        <select name="tag" class="form-control">
                            <option value="">{{ __('education.filters.all') }}</option>
                            @foreach($filterOptions['tags'] ?? [] as $tag)
                                <option value="{{ $tag }}" {{ ($filters['tag'] ?? null) === $tag ? 'selected' : '' }}>
                                    {{ ucfirst($tag) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-2">
                        <label class="text-xs text-muted text-uppercase">{{ __('education.filters.search') }}</label>
                        <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control" placeholder="{{ __('education.filters.search_placeholder') }}">
                    </div>
                </div>
                @if($newsTag)
                    <input type="hidden" name="news_tag" value="{{ $newsTag }}">
                @endif
                <div class="d-flex align-items-center">
                    <button type="submit" class="btn btn-primary mr-3">{{ __('education.filters.apply') }}</button>
                    <a href="{{ route('education.index') }}" class="btn btn-outline-secondary btn-sm">{{ __('education.filters.reset') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('education.recommendations.title') }}</h6>
                </div>
                <div class="card-body">
                    @if($recommendations['next_module'])
                        <div class="mb-4">
                            <h5 class="font-weight-bold">{{ __('education.recommendations.next_module') }}</h5>
                            <p class="mb-1">{{ $recommendations['next_module']->title }}</p>
                            <small class="text-muted">{{ __('education.modules.category') }}: {{ ucfirst($recommendations['next_module']->category) }}</small>
                            <div class="mt-3">
                                <a href="{{ route('education.module', $recommendations['next_module']) }}" class="btn btn-sm btn-primary">{{ __('education.modules.start') }}</a>
                            </div>
                        </div>
                    @endif
                    <div>
                        <h6 class="font-weight-bold">{{ __('education.recommendations.focus_modules') }}</h6>
                        @forelse($recommendations['focus_modules'] as $module)
                            <div class="border rounded p-2 mb-2">
                                <div class="font-weight-bold">{{ $module->title }}</div>
                                <small class="text-muted">{{ ucfirst($module->category) }} · {{ ucfirst($module->difficulty) }}</small>
                            </div>
                        @empty
                            <p class="text-muted mb-0">{{ __('education.recommendations.empty') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('education.news.title') }}</h6>
                </div>
                <div class="card-body">
                    @if(!empty($newsTags))
                        <form method="GET" action="{{ route('education.index') }}" class="form-inline mb-3">
                            <label class="text-xs text-muted mr-2 mb-0">{{ __('education.news.filter_tag') }}</label>
                            <select name="news_tag" class="form-control form-control-sm mr-2">
                                <option value="">{{ __('education.filters.all') }}</option>
                                @foreach($newsTags as $tag)
                                    <option value="{{ $tag }}" {{ ($newsTag ?? null) === $tag ? 'selected' : '' }}>{{ ucfirst($tag) }}</option>
                                @endforeach
                            </select>
                            @foreach($filters as $key => $value)
                                @if($value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <button type="submit" class="btn btn-sm btn-outline-secondary mr-2">{{ __('education.filters.apply') }}</button>
                            <a href="{{ route('education.index') }}" class="btn btn-sm btn-link">{{ __('education.filters.reset') }}</a>
                        </form>
                    @endif
                    @forelse($news as $article)
                        <div class="mb-3">
                            <a href="{{ $article->url ?? '#' }}" target="_blank" class="font-weight-bold d-block">{{ $article->title }}</a>
                            <small class="text-muted">{{ $article->source ?? __('education.news.source') }} &middot; {{ optional($article->published_at)->diffForHumans() }}</small>
                            <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 80) }}</p>
                            @if(is_array($article->tags) && count($article->tags))
                                <div>
                                    @foreach($article->tags as $tag)
                                        <span class="badge badge-light border text-muted mr-1 mb-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @empty
                        <p class="text-muted mb-0">{{ __('education.news.empty') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('education.community.title') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ __('education.community.subtitle') }}</p>
                    @foreach($communityHighlights as $story)
                        <div class="border rounded p-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $story['name'] }}</strong>
                                <span class="badge badge-light text-uppercase">Peer</span>
                            </div>
                            @if(!empty($story['title']))
                                <p class="text-xs text-muted mb-1">{{ $story['title'] }}</p>
                            @endif
                            <p class="mb-1 text-gray-800">{{ $story['achievement'] }}</p>
                            <p class="text-muted small mb-0">{{ $story['tip'] }}</p>
                        </div>
                    @endforeach
                    <hr>
                    <h6 class="font-weight-bold text-primary mb-3">{{ __('education.community.share_title') }}</h6>
                    <form method="POST" action="{{ route('education.community-stories.store') }}">
                        @csrf
                        <div class="form-group">
                            <label class="text-xs text-muted text-uppercase">{{ __('education.community.fields.display_name') }}</label>
                            <input type="text" name="display_name" class="form-control" value="{{ old('display_name') }}" placeholder="{{ __('education.community.fields.display_name_placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label class="text-xs text-muted text-uppercase">{{ __('education.community.fields.title') }}</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>
                        <div class="form-group">
                            <label class="text-xs text-muted text-uppercase">{{ __('education.community.fields.achievement') }}</label>
                            <textarea name="achievement" rows="3" class="form-control" required>{{ old('achievement') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label class="text-xs text-muted text-uppercase">{{ __('education.community.fields.tip') }}</label>
                            <textarea name="tip" rows="2" class="form-control">{{ old('tip') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary btn-block">{{ __('education.community.submit') }}</button>
                        <p class="text-muted small mt-2 mb-0">{{ __('education.community.moderation_note') }}</p>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('education.modules.title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($modules as $module)
                        @php($path = $learningPaths->get($module->id))
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-left-info">
                                <div class="card-body d-flex flex-column">
                                    <div class="mb-2">
                                        <h5 class="font-weight-bold">{{ $module->title }}</h5>
                                        <small class="text-muted">{{ __('education.modules.category') }}: {{ ucfirst($module->category) }}</small>
                                        <div class="mt-1">
                                            <span class="badge badge-light border text-uppercase text-muted">{{ __('education.modules.language') }}: {{ $module->language_label }}</span>
                                        </div>
                                    </div>
                                    <div class="text-xs text-muted mb-2">
                                        <span>{{ __('education.modules.difficulty') }}: {{ ucfirst($module->difficulty) }}</span><br>
                                        <span>{{ __('education.modules.estimated_time') }}: {{ $module->estimated_time }} min</span>
                                    </div>
                                    @if(!empty($module->tag_list))
                                        <div class="mb-2">
                                            @foreach($module->tag_list as $tag)
                                                <span class="badge badge-secondary mr-1 mb-1">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(!empty($module->objective_list))
                                        <ul class="text-xs text-muted pl-3 mb-3">
                                            @foreach(array_slice($module->objective_list, 0, 3) as $objective)
                                                <li>{{ $objective }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <small>{{ __('education.module.progress') }}</small>
                                            <small>{{ $path->progress ?? 0 }}%</small>
                                        </div>
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $path->progress ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <a href="{{ route('education.module', $module) }}" class="btn btn-sm btn-outline-primary btn-block">
                                            {{ $path && $path->progress > 0 ? __('education.modules.continue') : __('education.modules.start') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <p class="text-muted mb-0">{{ __('education.recommendations.empty') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
