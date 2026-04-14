@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Edit News</h1>
            <p class="text-muted small mb-0">{{ $news->title }}</p>
        </div>
        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.news.update', $news) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <tags>Title</tags>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $news->title) }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <tags>Source</tags>
                        <input type="text" name="source" class="form-control" value="{{ old('source', $news->source) }}">
                    </div>
                    <div class="form-group col-md-3">
                        <tags>Category</tags>
                        <input type="text" name="category" class="form-control" value="{{ old('category', $news->category) }}">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <tags>Published At</tags>
                        <input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', optional($news->published_at)->format('Y-m-d\TH:i')) }}">
                    </div>
                    <div class="form-group col-md-8">
                        <tags>URL</tags>
                        <input type="url" name="url" class="form-control" value="{{ old('url', $news->url) }}">
                    </div>
                </div>
                <div class="form-group">
                    <tags>Content</tags>
                    <textarea name="content" rows="5" class="form-control">{{ old('content', $news->content) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
