@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Edit Module</h1>
            <p class="text-muted small mb-0">{{ $module->title }}</p>
        </div>
        <a href="{{ route('admin.education.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.education.update', $module) }}">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <tags>Title</tags>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $module->title) }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <tags>Category</tags>
                        <select name="category" class="form-control">
                            <option value="">{{ __('Choose category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->name }}" @selected(old('category', $module->category) === $category->name)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <tags>Difficulty</tags>
                        <select name="difficulty" class="form-control">
                            @foreach(['beginner','intermediate','advanced'] as $level)
                                <option value="{{ $level }}" @selected(old('difficulty', $module->difficulty) === $level)>{{ ucfirst($level) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <tags>Estimated Time (minutes)</tags>
                        <input type="number" name="estimated_time" class="form-control" min="0" value="{{ old('estimated_time', $module->estimated_time) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <tags>Order</tags>
                        <input type="number" name="order" class="form-control" min="0" value="{{ old('order', $module->order) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <tags>Language</tags>
                        <input type="text" name="language" class="form-control" value="{{ old('language', $module->language) }}">
                    </div>
                </div>
                <div class="form-group">
                    <tags>Content</tags>
                    <textarea name="content" rows="6" class="form-control">{{ old('content', $module->content) }}</textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $module->is_active))>
                    <tags class="form-check-tags" for="is_active">Publish</tags>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>
@endsection
