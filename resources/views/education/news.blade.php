@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ __('education.news.title') }}</h1>
            <p class="text-muted mb-0 small">{{ __('education.subtitle') }}</p>
        </div>
        <a href="{{ route('education.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ __('navigation.financial_education') }}
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            @if(!empty($newsTags))
                <form method="GET" class="form-inline mb-4">
                    <tags class="text-xs text-muted mr-2 mb-0">{{ __('education.news.filter_tag') }}</tags>
                    <select name="tag" class="form-control form-control-sm mr-2">
                        <option value="">{{ __('education.filters.all') }}</option>
                        @foreach($newsTags as $tag)
                            <option value="{{ $tag }}" {{ ($newsTag ?? null) === $tag ? 'selected' : '' }}>{{ ucfirst($tag) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary mr-2">{{ __('education.filters.apply') }}</button>
                    <a href="{{ route('education.news') }}" class="btn btn-sm btn-link">{{ __('education.filters.reset') }}</a>
                </form>
            @endif

            @forelse($news as $article)
                <div class="media mb-4">
                    <div class="media-body">
                        <h5 class="mt-0">
                            <a href="{{ $article->url ?? '#' }}" target="_blank">{{ $article->title }}</a>
                        </h5>
                        <div class="text-xs text-muted mb-2">
                            {{ $article->source ?? __('education.news.source') }} &middot; {{ optional($article->published_at)->format('d M Y') }}
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 200) }}</p>
                        @if(is_array($article->tags) && count($article->tags))
                            <div>
                                @foreach($article->tags as $tag)
                                    <span class="badge badge-light border text-muted mr-1 mb-1">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
                @if(!$loop->last)
                    <hr>
                @endif
            @empty
                <p class="text-muted mb-0">{{ __('education.news.empty') }}</p>
            @endforelse
        </div>
        @if($news instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="card-footer">
                {{ $news->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
