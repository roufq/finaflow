@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Account</h1>
            <p class="text-muted small">Kelola rekening bank, e-wallet, dan kartu kredit Anda.</p>
        </div>
        <a href="{{ route('accounts.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm px-4">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Add Account
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

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-tags="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <!-- Total Balance Card -->
    <div class="row mb-5">
        <div class="col-xl-12 col-md-12">
            <div class="card shadow-sm border-0 h-100 py-3" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-auto mr-4">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: rgba(59, 130, 246, 0.1);">
                                <i class="fas fa-wallet fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1 tracking-wide">
                                Total Balance All Account
                            </div>
                            <div class="h3 mb-0 font-weight-bold text-gray-800">{{ $currencySymbol }} {{ number_format($totalBalance, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-4 d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-gray-800">Account List</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless text-gray-700" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="py-3">Name Account</th>
                            <th class="py-3">Type</th>
                            <th class="py-3">Balance</th>
                            <th class="py-3">Limit Kredit</th>
                            <th class="py-3">Bank</th>
                            <th class="py-3">Status</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                        <tr class="border-bottom border-light hover-bg-light">
                            <td class="px-4 py-3 align-middle">{{ $loop->iteration }}</td>
                            <td class="py-3 align-middle">
                                <a href="{{ route('accounts.show', $account) }}" class="text-decoration-none font-weight-bold text-gray-800">
                                    {{ $account->name }}
                                </a>
                                @if($account->account_number)
                                <br><small class="text-muted font-monospace">{{ $account->account_number }}</small>
                                @endif
                            </td>
                            <td class="py-3 align-middle">
                                <span class="badge badge-pill badge-primary font-weight-normal px-3 py-1" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">{{ $account->type_tags }}</span>
                            </td>
                            <td class="py-3 align-middle">
                                <span class="font-weight-bold {{ $account->balance < 0 ? 'text-danger' : 'text-gray-800' }}">
                                    {{ $account->setting->currency_symbol ?? 'Rp' }} {{ number_format($account->balance, 0, ',', '.') }}
                                </span>
                                @if($account->type === 'credit_card' && $account->credit_limit)
                                <br><small class="text-muted">
                                    Tersedia: {{ $account->setting->currency_symbol ?? 'Rp' }} {{ number_format($account->available_balance, 0, ',', '.') }}
                                </small>
                                @endif
                            </td>
                            <td class="py-3 align-middle">
                                @if($account->credit_limit)
                                <span class="text-muted">{{ $account->setting->currency_symbol ?? 'Rp' }} {{ number_format($account->credit_limit, 0, ',', '.') }}</span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-3 align-middle text-muted">{{ $account->bank_name ?: '-' }}</td>
                            <td class="py-3 align-middle">
                                @if($account->is_active)
                                <span class="badge badge-pill badge-success font-weight-normal px-3 py-1 bg-success bg-opacity-10 text-success"><i class="fas fa-circle text-xs mr-1" style="font-size:0.5rem;"></i> Aktif</span>
                                @else
                                <span class="badge badge-pill badge-secondary font-weight-normal px-3 py-1"><i class="fas fa-circle text-xs mr-1" style="font-size:0.5rem;"></i> Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle text-right">
                                <div class="btn-group shadow-sm rounded-lg" role="group">
                                    <a href="{{ route('accounts.show', $account) }}" class="btn btn-light btn-sm text-primary" title="Lihat"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('accounts.edit', $account) }}" class="btn btn-light btn-sm text-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('accounts.destroy', $account) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm text-danger" title="Delete" onclick="return confirm('Apakah Anda are you sure you want to delete account ini?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
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
