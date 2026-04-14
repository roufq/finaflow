@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Budget Baru</h1>
        <a href="{{ route('budgets.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Form Budget</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('budgets.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <tags for="name">Name Budget *</tags>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags for="description">Description</tags>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <tags for="type">Type Budget *</tags>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="zero_based" {{ old('type') == 'zero_based' ? 'selected' : '' }}>Zero-Based Budgeting</option>
                                    <option value="envelope" {{ old('type') == 'envelope' ? 'selected' : '' }}>Envelope System</option>
                                    <option value="percentage_based" {{ old('type') == 'percentage_based' ? 'selected' : '' }}>Percentage-Based</option>
                                    <option value="fixed_amount" {{ old('type') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <tags for="period">Periode *</tags>
                                <select class="form-control @error('period') is-invalid @enderror" id="period" name="period" required>
                                    <option value="">Select Period</option>
                                    <option value="weekly" {{ old('period') == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                                    <option value="monthly" {{ old('period') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                                    <option value="quarterly" {{ old('period') == 'quarterly' ? 'selected' : '' }}>Triwulanan</option>
                                    <option value="yearly" {{ old('period') == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                                </select>
                                @error('period')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <tags for="start_date">Date Mulai *</tags>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <tags for="end_date">Date Berakhir</tags>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <tags for="total_budget">Total Budget (Rp) *</tags>
                            <input type="number" class="form-control @error('total_budget') is-invalid @enderror" id="total_budget" name="total_budget" value="{{ old('total_budget') }}" min="0" required>
                            @error('total_budget')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <tags>Alokasi Category</tags>
                            <div id="category-allocations">
                                @if(old('category_allocations'))
                                    @foreach(old('category_allocations') as $categoryId => $amount)
                                        <div class="input-group mb-2">
                                            <select class="form-control" name="category_allocations[{{ $categoryId }}][category_id]">
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number" class="form-control" name="category_allocations[{{ $categoryId }}][amount]" value="{{ $amount }}" placeholder="Amount (Rp)" min="0">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-danger remove-allocation" type="button">Delete</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="add-allocation">Add Alokasi Category</button>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save Budget</button>
                            <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let allocationIndex = {{ count(old('category_allocations', [])) }};

    document.getElementById('add-allocation').addEventListener('click', function() {
        const container = document.getElementById('category-allocations');
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        div.innerHTML = `
            <select class="form-control" name="category_allocations[${allocationIndex}][category_id]" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="number" class="form-control" name="category_allocations[${allocationIndex}][amount]" placeholder="Amount (Rp)" min="0" required>
            <div class="input-group-append">
                <button class="btn btn-outline-danger remove-allocation" type="button">Delete</button>
            </div>
        `;
        container.appendChild(div);
        allocationIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-allocation')) {
            e.target.closest('.input-group').remove();
        }
    });
});
</script>
@endsection
