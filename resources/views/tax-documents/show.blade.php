@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tax Document Details</h1>
        <a href="{{ route('tax-documents.edit', $document) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Edit</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Document Information</h6>
        </div>
        <div class="card-body">
            <p><strong>Title:</strong> {{ $document->title }}</p>
            <p><strong>Year:</strong> {{ $document->year }}</p>
            <p><strong>Category:</strong> {{ $document->category ?? '-' }}</p>
            <p><strong>Uploaded At:</strong> {{ $document->created_at->format('Y-m-d H:i') }}</p>
            <p><strong>Notes:</strong><br>{{ $document->notes ?: '-' }}</p>
            <p>
                <a href="{{ Storage::disk('public')->url($document->file_path) }}" target="_blank" class="btn btn-success btn-sm">View / Download File</a>
            </p>
            <a href="{{ route('tax-documents.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection

