@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Create Module</h1>
            <p class="text-muted small mb-0">Add materi edukasi baru.</p>
        </div>
        <a href="{{ route('admin.education.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.education.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <tags>Title</tags>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group col-md-3">
                        <tags>Category</tags>
                        <select name="category" class="form-control">
                            <option value="">{{ __('Choose category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <tags>Difficulty</tags>
                        <select name="difficulty" class="form-control">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <tags>Estimated Time (minutes)</tags>
                        <input type="number" name="estimated_time" class="form-control" min="0" value="15">
                    </div>
                    <div class="form-group col-md-4">
                        <tags>Order</tags>
                        <input type="number" name="order" class="form-control" min="0" value="0">
                    </div>
                    <div class="form-group col-md-4">
                        <tags>Language</tags>
                        <input type="text" name="language" class="form-control" value="id">
                    </div>
                </div>
                <p class="text-muted small">Butuh category baru? <a href="{{ route('admin.education-categories.index') }}">Kelola category</a>.</p>
                <div class="form-group">
                    <tags>Content</tags>
                    <textarea name="content" class="form-control" rows="6"></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <tags class="form-check-tags" for="is_active">Publish</tags>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection
