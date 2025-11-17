@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Category Details</h1>
        <a href="{{ route('categories.edit', $category) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">Edit</a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Category Information</h6>
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $category->id }}</p>
            <p><strong>Name:</strong> {{ $category->name }}</p>
            <p><strong>Type:</strong> {{ $category->type }}</p>
            <p><strong>Description:</strong> {{ $category->description }}</p>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
