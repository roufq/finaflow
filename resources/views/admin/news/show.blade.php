@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">{{ $news->title }}</h1>
            <p class="text-muted small mb-0">Detail berita</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-sm btn-primary">Edit</a>
            <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Source:</strong> {{ $news->source ?? '-' }}</div>
                <div class="col-md-4"><strong>Category:</strong> {{ $news->category ?? '-' }}</div>
                <div class="col-md-4"><strong>Published:</strong> {{ $news->published_at?->format('d M Y H:i') ?? '-' }}</div>
            </div>
            <div class="mb-3">
                <strong>URL:</strong>
                @if($news->url)
                    <a href="{{ $news->url }}" target="_blank" rel="noopener noreferrer">{{ $news->url }}</a>
                @else
                    <span>-</span>
                @endif
            </div>
            <div>
                <strong>Content:</strong>
                <div class="border rounded p-3 mt-2" style="white-space: pre-wrap;">{{ $news->content }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
