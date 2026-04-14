@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Hutang</h1>
        <a href="{{ route('debts.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Hutang Baru
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
        @forelse($debts as $debt)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-{{ $debt->status == 'active' ? 'warning' : ($debt->status == 'paid_off' ? 'success' : 'danger') }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                {{ $debt->name }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($debt->current_balance, 0, ',', '.') }}
                            </div>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-{{ $debt->status == 'active' ? 'warning' : ($debt->status == 'paid_off' ? 'success' : 'danger') }}" role="progressbar" style="width: {{ $debt->payoff_progress }}%" aria-valuenow="{{ $debt->payoff_progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <small class="text-muted">{{ number_format($debt->payoff_progress, 1) }}% lunas</small>
                            <br><small class="text-muted">Lender: {{ $debt->lender }}</small>
                            <br><small class="text-muted">Bunga: {{ $debt->interest_rate }}%</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="row">
                        <div class="col">
                            <a href="{{ route('debts.show', $debt) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                        </div>
                        <div class="col">
                            <a href="{{ route('debts.edit', $debt) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-credit-card fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada hutang</h5>
                    <p class="text-gray-500">Mulai catat hutang pertama Anda untuk melacak pembayaran</p>
                    <a href="{{ route('debts.create') }}" class="btn btn-primary">Tambah Hutang Pertama</a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Debt Summary -->
    @if($debts->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan Hutang</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ $debts->where('status', 'active')->count() }}</h4>
                                <small class="text-muted">Hutang Aktif</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ $debts->where('status', 'paid_off')->count() }}</h4>
                                <small class="text-muted">Sudah Lunas</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-danger">{{ $debts->where('status', 'defaulted')->count() }}</h4>
                                <small class="text-muted">Macet</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4>Rp {{ number_format($debts->where('status', 'active')->sum('current_balance'), 0, ',', '.') }}</h4>
                                <small class="text-muted">Total Hutang Aktif</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
