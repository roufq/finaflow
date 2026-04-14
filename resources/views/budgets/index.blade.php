@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Anggaran Keuangan</h1>
            <p class="text-muted small">Kelola batas expense Anda agar tetap dalam kendali.</p>
        </div>
        <a href="{{ route('budgets.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm px-4">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add Anggaran Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-tags="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @forelse($budgets as $budget)
        @php
            $bgIcon = $budget->spent_percentage > 100 ? 'rgba(239, 68, 68, 0.1)' : ($budget->spent_percentage > 80 ? 'rgba(234, 179, 8, 0.1)' : 'rgba(59, 130, 246, 0.1)');
            $colorIcon = $budget->spent_percentage > 100 ? '#ef4444' : ($budget->spent_percentage > 80 ? '#eab308' : '#3b82f6');
        @endphp
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100 position-relative overflow-hidden group">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: {{ $bgIcon }};">
                            <i class="fas fa-calculator fa-lg" style="color: {{ $colorIcon }};"></i>
                        </div>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle text-muted" href="#" role="button" id="dropdownMenuLink{{ $budget->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{ $budget->id }}">
                                <div class="dropdown-header">Action:</div>
                                <a class="dropdown-item" href="{{ route('budgets.edit', $budget) }}">Edit</a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('budgets.destroy', $budget) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit" onclick="return confirm('Delete anggaran ini?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('budgets.show', $budget) }}" class="text-decoration-none">
                            <h5 class="font-weight-bold text-gray-800 mb-1 text-truncate" title="{{ $budget->name }}">{{ $budget->name }}</h5>
                        </a>
                        
                        <div class="mt-3">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Terpakai</div>
                            <div class="h5 mb-0 font-weight-bold {{ $budget->spent_percentage > 100 ? 'text-danger' : 'text-gray-800' }}">{{ $currencySymbol }} {{ number_format($budget->spent_amount, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1 text-truncate">Batas: {{ $currencySymbol }} {{ number_format($budget->total_budget, 0, ',', '.') }}</div>
                        </div>

                        <div class="progress mt-3" style="height: 8px; border-radius: 4px; background-color: #f1f5f9;">
                            <div class="progress-bar bg-{{ $budget->spent_percentage > 100 ? 'danger' : ($budget->spent_percentage > 80 ? 'warning' : 'primary') }}" role="progressbar" style="width: {{ min($budget->spent_percentage, 100) }}%" aria-valuenow="{{ min($budget->spent_percentage, 100) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="font-weight-bold text-{{ $budget->spent_percentage > 100 ? 'danger' : ($budget->spent_percentage > 80 ? 'warning' : 'muted') }}">{{ number_format($budget->spent_percentage, 1) }}% digunakan</small>
                            @if($budget->spent_percentage > 100)
                                <small class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Over: {{ $currencySymbol }} {{ number_format($budget->spent_amount - $budget->total_budget, 0, ',', '.') }}</small>
                            @else
                                <small class="text-muted font-weight-bold">Remaining: {{ $currencySymbol }} {{ number_format($budget->remaining_amount, 0, ',', '.') }}</small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4 text-center">
                    <a href="{{ route('budgets.show', $budget) }}" class="btn btn-light btn-sm btn-block text-gray-600 font-weight-bold rounded-lg" style="background-color: #f8fafc;">Transaction Details</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-calculator fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada budgets</h5>
                    <p class="text-gray-500">Mulai buat budget pertama Anda untuk mengelola expense</p>
                    <a href="{{ route('budgets.create') }}" class="btn btn-primary">Buat Budget Pertama</a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
