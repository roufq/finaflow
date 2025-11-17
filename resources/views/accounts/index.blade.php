@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Akun</h1>
        <a href="{{ route('accounts.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Akun
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Total Balance Card -->
    <div class="row mb-4">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Saldo Semua Akun</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalBalance, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wallet fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Akun</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Akun</th>
                            <th>Tipe</th>
                            <th>Saldo</th>
                            <th>Limit Kredit</th>
                            <th>Bank</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr>
                            <td>
                                <a href="{{ route('accounts.show', $account) }}" class="text-decoration-none">
                                    {{ $account->name }}
                                </a>
                                @if($account->account_number)
                                <br><small class="text-muted">{{ $account->account_number }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $account->type_label }}</span>
                            </td>
                            <td>
                                <span class="font-weight-bold {{ $account->balance < 0 ? 'text-danger' : 'text-success' }}">
                                    Rp {{ number_format($account->balance, 0, ',', '.') }}
                                </span>
                                @if($account->type === 'credit_card' && $account->credit_limit)
                                <br><small class="text-muted">
                                    Tersedia: Rp {{ number_format($account->available_balance, 0, ',', '.') }}
                                </small>
                                @endif
                            </td>
                            <td>
                                @if($account->credit_limit)
                                Rp {{ number_format($account->credit_limit, 0, ',', '.') }}
                                @else
                                -
                                @endif
                            </td>
                            <td>{{ $account->bank_name ?: '-' }}</td>
                            <td>
                                @if($account->is_active)
                                <span class="badge badge-success">Aktif</span>
                                @else
                                <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('accounts.show', $account) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('accounts.destroy', $account) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
