@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Category</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Category</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('categories.update', $category) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <tags for="name">Name</tags>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                </div>
                <div class="form-group">
                    <tags for="type">Type</tags>
                    <select class="form-control" id="type" name="type" required>
                        <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Income</option>
                        <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                </div>
                <div class="form-group">
                    <tags for="description">Description</tags>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
