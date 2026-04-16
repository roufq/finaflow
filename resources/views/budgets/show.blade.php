@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $budget->name }}</h1>
        <div>
            <a href="{{ route('budgets.edit', $budget) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="{{ route('budgets.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Budget Progress Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Progress Budget</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="progress-circle {{ $budget->spent_percentage > 100 ? 'over-budget' : '' }}" data-progress="{{ min($budget->spent_percentage, 100) }}">
                            <span class="progress-text">{{ number_format($budget->spent_percentage, 1) }}%</span>
                        </div>
                        <h4 class="mt-3">Rp {{ number_format($budget->spent_amount, 0, ',', '.') }}</h4>
                        <p class="text-muted">dari Rp {{ number_format($budget->total_budget, 0, ',', '.') }}</p>
                        @if($budget->spent_percentage > 100)
                            <p class="text-danger">Over budget: Rp {{ number_format($budget->spent_amount - $budget->total_budget, 0, ',', '.') }}</p>
                        @else
                            <p class="text-success">Remaining: Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Budget Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Budget</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Type:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $budget->type_tags }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Periode:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $budget->period_tags }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Date Mulai:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $budget->start_date->format('d M Y') }}
                        </div>
                    </div>
                    @if($budget->end_date)
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Date Berakhir:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $budget->end_date->format('d M Y') }}
                        </div>
                    </div>
                    @endif
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="badge badge-{{ $budget->status_color }}">{{ $budget->status_tags }}</span>
                        </div>
                    </div>
                    @if($budget->description)
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <strong>Description:</strong>
                            <p class="mt-2">{{ $budget->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Category Allocations & Actions -->
        <div class="col-xl-8 col-lg-7">
            <!-- Category Allocations -->
            @if($budget->category_allocations && count($budget->category_allocations) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alokasi Category</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($budget->category_allocations as $categoryId => $allocatedAmount)
                            @php
                                $category = \App\Models\Category::find($categoryId);
                                if (!$category) continue;
                                // Calculate spent amount for this category (simplified - would need actual transaction data)
                                $spentForCategory = 0; // Placeholder
                                $percentage = $allocatedAmount > 0 ? min(($spentForCategory / $allocatedAmount) * 100, 100) : 0;
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="card border-left-info">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $category->name }}</h6>
                                        <p class="card-text">
                                            Rp {{ number_format($spentForCategory, 0, ',', '.') }} / Rp {{ number_format($allocatedAmount, 0, ',', '.') }}
                                        </p>
                                        <div class="progress">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">{{ number_format($percentage, 1) }}% digunakan</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Action Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-success btn-block" data-toggle="modal" data-target="#updateSpentModal">
                                <i class="fas fa-plus"></i> Update Expense
                            </button>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda are you sure you want to delete budget ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Delete Budget
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Spent Amount Modal -->
<div class="modal fade" id="updateSpentModal" tabindex="-1" role="dialog" aria-labelledby="updateSpentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateSpentModalLabel">Update Amount Terpakai</h5>
                <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updateSpentForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="spent_amount">Amount Terpakai Baru (Rp)</label>
                        <input type="number" class="form-control" id="spent_amount" name="spent_amount" value="{{ $budget->spent_amount }}" min="0" required>
                        <small class="form-text text-muted">Masukkan amount total yang sudah terpakai</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    background: conic-gradient(#4e73df 0% var(--progress), #e9ecef var(--progress) 100%);
}

.progress-circle.over-budget {
    background: conic-gradient(#e74a3b 0% var(--progress), #e9ecef var(--progress) 100%);
}

.progress-circle::before {
    content: '';
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;
    border-radius: 50%;
    background: white;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 18px;
    font-weight: bold;
    color: #4e73df;
}

.progress-circle.over-budget .progress-text {
    color: #e74a3b;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressCircles = document.querySelectorAll('.progress-circle');
    progressCircles.forEach(circle => {
        const progress = circle.dataset.progress;
        circle.style.setProperty('--progress', progress + '%');
    });

    // Handle update spent form
    document.getElementById('updateSpentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(`{{ route('budgets.updateSpent', $budget) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>
@endsection
