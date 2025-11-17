@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Tax Document</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Tax Document</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('tax-documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $document->title) }}" required>
                </div>
                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" class="form-control" id="year" name="year" min="2000" max="{{ now()->year + 1 }}" value="{{ old('year', $document->year) }}" required>
                </div>
                <div class="form-group">
                    <label for="category">Category (optional)</label>
                    <select class="form-control" id="category" name="category">
                        <option value="">Pilih kategori</option>
                        <option value="income_proof" {{ old('category', $document->category) === 'income_proof' ? 'selected' : '' }}>Income Proof</option>
                        <option value="expense_receipt" {{ old('category', $document->category) === 'expense_receipt' ? 'selected' : '' }}>Expense Receipt</option>
                        <option value="tax_form" {{ old('category', $document->category) === 'tax_form' ? 'selected' : '' }}>Tax Form</option>
                        <option value="other" {{ old('category', $document->category) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes">Notes (optional)</label>
                    <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $document->notes) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Current File</label><br>
                    <a href="{{ Storage::disk('public')->url($document->file_path) }}" target="_blank" class="btn btn-sm btn-success">View / Download</a>
                </div>
                <div class="form-group">
                    <label for="document">Replace File (optional)</label>
                    <input type="file" class="form-control-file" id="document" name="document">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('tax-documents.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

