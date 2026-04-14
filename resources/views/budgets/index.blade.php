@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Budgets</h1>
        <a href="{{ route('budgets.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Budget
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @forelse($budgets as $budget)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-{{ $budget->spent_percentage > 100 ? 'danger' : ($budget->spent_percentage > 80 ? 'warning' : 'success') }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                {{ $budget->name }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($budget->spent_amount, 0, ',', '.') }} / Rp {{ number_format($budget->total_budget, 0, ',', '.') }}
                            </div>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-{{ $budget->spent_percentage > 100 ? 'danger' : ($budget->spent_percentage > 80 ? 'warning' : 'success') }}" role="progressbar" style="width: {{ min($budget->spent_percentage, 100) }}%" aria-valuenow="{{ min($budget->spent_percentage, 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">{{ number_format($budget->spent_percentage, 1) }}% digunakan</small>
                            @if($budget->spent_percentage > 100)
                                <br><small class="text-danger">Over budget: Rp {{ number_format($budget->spent_amount - $budget->total_budget, 0, ',', '.') }}</small>
                            @else
                                <br><small class="text-muted">Sisa: Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}</small>
                            @endif
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calculator fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('budgets.show', $budget) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </div>
                        <div class="col">
                            <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-calculator fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada budgets</h5>
                    <p class="text-gray-500">Mulai buat budget pertama Anda untuk mengelola pengeluaran</p>
                    <a href="{{ route('budgets.create') }}" class="btn btn-primary">Buat Budget Pertama</a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
