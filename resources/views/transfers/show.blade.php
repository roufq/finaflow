@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Transfer</h1>
        <div>
            @if($transfer->status === 'pending')
            <a href="{{ route('transfers.edit', $transfer) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Transfer
            </a>
            @endif
            <a href="{{ route('transfers.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Transfer Status Card -->
    <div class="row mb-4">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-{{ $transfer->status_color }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $transfer->status_color }} text-uppercase mb-1">
                                Status Transfer</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge badge-{{ $transfer->status_color }} badge-lg">
                                    {{ $transfer->status_label }}
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfer Details -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Transfer</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Tanggal Transfer:</strong><br>
                            {{ $transfer->transfer_date->format('d F Y') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Nomor Referensi:</strong><br>
                            {{ $transfer->reference_number ?: '-' }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Dari Akun:</strong><br>
                            <a href="{{ route('accounts.show', $transfer->fromAccount) }}" class="text-decoration-none">
                                {{ $transfer->fromAccount->name }}
                            </a>
                            @if($transfer->fromAccount->account_number)
                            <br><small class="text-muted">{{ $transfer->fromAccount->account_number }}</small>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Ke Akun:</strong><br>
                            <a href="{{ route('accounts.show', $transfer->toAccount) }}" class="text-decoration-none">
                                {{ $transfer->toAccount->name }}
                            </a>
                            @if($transfer->toAccount->account_number)
                            <br><small class="text-muted">{{ $transfer->toAccount->account_number }}</small>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Jumlah Transfer:</strong><br>
                            <span class="h5 text-primary">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Biaya Transfer:</strong><br>
                            <span class="h6 text-warning">Rp {{ number_format($transfer->fee, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-md-4">
                            <strong>Total Debit:</strong><br>
                            <span class="h5 text-danger font-weight-bold">Rp {{ number_format($transfer->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Deskripsi:</strong><br>
                            {{ $transfer->description ?: '-' }}
                        </div>
                    </div>
                    @if($transfer->status === 'completed')
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Diproses Pada:</strong><br>
                            {{ $transfer->processed_at ? $transfer->processed_at->format('d F Y H:i') : '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Dibuat Pada:</strong><br>
                            {{ $transfer->created_at->format('d F Y H:i') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Transfer Flow Visualization -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Alur Transfer</h6>
                </div>
                <div class="card-body text-center">
                    <div class="transfer-flow">
                        <div class="account-box from-account">
                            <div class="account-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="account-name">{{ $transfer->fromAccount->name }}</div>
                            <div class="account-balance">Saldo: Rp {{ number_format($transfer->fromAccount->balance, 0, ',', '.') }}</div>
                        </div>

                        <div class="transfer-arrow">
                            <i class="fas fa-arrow-down fa-2x text-primary"></i>
                            <div class="transfer-amount">
                                Rp {{ number_format($transfer->amount, 0, ',', '.') }}
                                @if($transfer->fee > 0)
                                <br><small class="text-muted">+ Biaya Rp {{ number_format($transfer->fee, 0, ',', '.') }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="account-box to-account">
                            <div class="account-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="account-name">{{ $transfer->toAccount->name }}</div>
                            <div class="account-balance">Saldo: Rp {{ number_format($transfer->toAccount->balance, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($transfer->status === 'pending')
            <!-- Action Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('transfers.edit', $transfer) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Edit Transfer
                        </a>
                        <form action="{{ route('transfers.destroy', $transfer) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transfer ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Hapus Transfer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.transfer-flow {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 0;
}

.account-box {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: 15px;
    margin: 10px 0;
    min-width: 200px;
    text-align: center;
}

.account-icon {
    font-size: 24px;
    color: #6c757d;
    margin-bottom: 10px;
}

.account-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.account-balance {
    font-size: 12px;
    color: #6c757d;
}

.transfer-arrow {
    margin: 20px 0;
    text-align: center;
}

.transfer-amount {
    margin-top: 10px;
    font-weight: bold;
    color: #007bff;
}

.from-account {
    border-color: #dc3545;
}

.to-account {
    border-color: #28a745;
}
</style>
@endsection
