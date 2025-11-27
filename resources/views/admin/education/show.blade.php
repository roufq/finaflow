@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">{{ $module->title }}</h1>
            <p class="text-muted small mb-0">Detail module</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('admin.education.edit', $module) }}" class="btn btn-sm btn-primary">Edit</a>
            <a href="{{ route('admin.education.index') }}" class="btn btn-sm btn-secondary">Back</a>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4"><strong>Category:</strong> {{ $module->category ?? '-' }}</div>
                <div class="col-md-4"><strong>Difficulty:</strong> {{ ucfirst($module->difficulty ?? 'beginner') }}</div>
                <div class="col-md-4"><strong>Status:</strong> {{ $module->is_active ? 'Published' : 'Draft' }}</div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4"><strong>Estimated Time:</strong> {{ $module->estimated_time ?? '-' }} minutes</div>
                <div class="col-md-4"><strong>Order:</strong> {{ $module->order }}</div>
                <div class="col-md-4"><strong>Language:</strong> {{ strtoupper($module->language ?? 'ID') }}</div>
            </div>
            <div>
                <strong>Content:</strong>
                <div class="border rounded p-3 mt-2" style="white-space: pre-wrap;">{{ $module->content }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
