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
            @forelse($news as $article)
                <div class="media mb-4">
                    <div class="media-body">
                        <h5 class="mt-0">
                            <a href="{{ $article->url ?? '#' }}" target="_blank">{{ $article->title }}</a>
                        </h5>
                        <div class="text-xs text-muted mb-2">
                            {{ $article->source ?? __('education.news.source') }} · {{ optional($article->published_at)->format('d M Y') }}
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($article->content), 200) }}</p>
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
