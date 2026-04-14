@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $account->name }}</h1>
        <div>
            <a href="{{ route('accounts.edit', $account) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Account
            </a>
            <a href="{{ route('accounts.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-tags="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Account Details Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Balance Saat Ini</div>
                            <div class="h5 mb-0 font-weight-bold {{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                                Rp {{ number_format($account->balance, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($account->type === 'credit_card')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Limit Kredit</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($account->credit_limit, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Balance Tersedia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($account->available_balance, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Status</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($account->is_active)
                                <span class="badge badge-success">Aktif</span>
                                @else
                                <span class="badge badge-secondary">No Aktif</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-info-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="row">
        <div class="col-lg-8">
            <!-- Account Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Account</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Name Account:</strong><br>
                            {{ $account->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Type Account:</strong><br>
                            <span class="badge badge-info">{{ $account->type_tags }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nomor Account:</strong><br>
                            {{ $account->account_number ?: '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Name Bank:</strong><br>
                            {{ $account->bank_name ?: '-' }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Date Pembukaan:</strong><br>
                            {{ $account->opening_date ? $account->opening_date->format('d M Y') : '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Notes:</strong><br>
                            {{ $account->notes ?: '-' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transactions Recent</h6>
                </div>
                <div class="card-body">
                    @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Jenis</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                    <td>{{ $transaction->category->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="font-weight-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </td>
                                    <td>{{ Str::limit($transaction->description, 30) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('transactions.index') }}?account_id={{ $account->id }}" class="btn btn-primary btn-sm">Lihat All Transactions</a>
                    </div>
                    @else
                    <p class="text-center text-muted">Belum ada transactions untuk account ini.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Transfers History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Transfers Recent</h6>
                </div>
                <div class="card-body">
                    @if($transfersFrom->count() > 0 || $transfersTo->count() > 0)
                    <div class="timeline">
                        @foreach($transfersFrom as $transfer)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Transfers Logout</h6>
                                <p class="text-muted">{{ $transfer->transfers_date->format('d/m/Y') }}</p>
                                <p>Rp {{ number_format($transfer->amount, 0, ',', '.') }} ke {{ $transfer->toAccount->name }}</p>
                            </div>
                        </div>
                        @endforeach

                        @foreach($transfersTo as $transfer)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Transfers Masuk</h6>
                                <p class="text-muted">{{ $transfer->transfers_date->format('d/m/Y') }}</p>
                                <p>Rp {{ number_format($transfer->amount, 0, ',', '.') }} dari {{ $transfer->fromAccount->name }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('transfers.index') }}?account_id={{ $account->id }}" class="btn btn-primary btn-sm">Lihat All Transfers</a>
                    </div>
                    @else
                    <p class="text-center text-muted">Belum ada transfers untuk account ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 5px;
}

.timeline-title {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: bold;
}
</style>
@endsection
