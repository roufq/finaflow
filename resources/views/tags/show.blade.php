@extends('layouts.app')

@section('title', 'Detail Tag: ' . $tag->name)

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                Tag: <span class="badge" style="background-color: {{ $tag->color }}; color: white;">{{ $tag->name }}</span>
            </h1>
            @if($tag->description)
                <p class="mb-0 text-muted">{{ $tag->description }}</p>
            @endif
        </div>
        <div>
            <a href="{{ route('tags.edit', $tag) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit Tag
            </a>
            <a href="{{ route('tags.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm ml-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $transactions->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
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
                                Total Pemasukan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($transactions->where('type', 'income')->sum('amount'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Total Pengeluaran
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($transactions->where('type', 'expense')->sum('amount'), 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Recent Transactions
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @if($transactions->count() > 0)
                                    {{ $transactions->first()->transaction_date->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transactions with this Tag</h6>
        </div>
        <div class="card-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Category</th>
                                <th>Account</th>
                                <th>Description</th>
                                <th>Jumlah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaction->transaction_date->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                            {{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->category->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->account->name ?? 'N/A' }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td class="text-{{ $transaction->type === 'income' ? 'success' : 'danger' }}">
                                        <strong>
                                            {{ $transaction->type === 'income' ? '+' : '-' }}
                                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                        </strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('transactions.show', $transaction) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('transactions.edit', $transaction) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada transaksi</h5>
                    <p class="text-gray-400">Tag ini belum digunakan pada transaksi manapun.</p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus fa-sm text-white-50"></i> Buat Transaksi Baru
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
