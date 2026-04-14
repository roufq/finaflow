@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0 text-gray-800">Create Category</h1>
            <p class="text-muted small mb-0">Add category edukasi.</p>
        </div>
        <a href="{{ route('admin.education-categories.index') }}" class="btn btn-sm btn-secondary">Back</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.education-categories.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <tags>Name</tags>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <tags>Order</tags>
                        <input type="number" name="order" class="form-control" min="0" value="0">
                    </div>
                </div>
                <div class="form-group">
                    <tags>Description</tags>
                    <textarea name="description" rows="3" class="form-control"></textarea>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                    <tags class="form-check-tags" for="is_active">Active</tags>
                </div>
                <button type="submit" class="btn btn-primary">Create</button>
            </form>
        </div>
    </div>
</div>
@endsection
