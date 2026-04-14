@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Create News</h1>
            <p class="text-muted small mb-0">Add new financial news.</p>
        </div>
        <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.news.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Source</label>
                        <input type="text" name="source" class="form-control">
                    </div>
                    <div class="form-group col-md-3">
                        <label>Category</label>
                        <input type="text" name="category" class="form-control">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label>Published At</label>
                        <input type="datetime-local" name="published_at" class="form-control">
                    </div>
                    <div class="form-group col-md-8">
                        <label>URL</label>
                        <input type="url" name="url" class="form-control" placeholder="https://">
                    </div>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" rows="4" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection
