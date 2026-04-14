@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Transfers</h1>
        <a href="{{ route('transfers.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Transfers
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transfers List</h6>
        </div>
        <div class="card-body">
            @if($transfers->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Dari Account</th>
                            <th>Ke Account</th>
                            <th>Amount</th>
                            <th>Biaya</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $transfer->transfers_date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('accounts.show', $transfer->fromAccount) }}" class="text-decoration-none">
                                    {{ $transfer->fromAccount->name }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('accounts.show', $transfer->toAccount) }}" class="text-decoration-none">
                                    {{ $transfer->toAccount->name }}
                                </a>
                            </td>
                            <td>Rp {{ number_format($transfer->amount, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($transfer->fee, 0, ',', '.') }}</td>
                            <td class="font-weight-bold">Rp {{ number_format($transfer->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $transfer->status_color }}">
                                    {{ $transfer->status_tags }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('transfers.show', $transfer) }}" class="btn btn-info btn-sm">Lihat</a>
                                @if($transfer->status === 'pending')
                                <a href="{{ route('transfers.edit', $transfer) }}" class="btn btn-warning btn-sm">Edit</a>
                                @endif
                                @if(in_array($transfer->status, ['pending', 'failed']))
                                <form action="{{ route('transfers.destroy', $transfer) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda are you sure you want to delete transfers ini?')">Delete</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $transfers->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-exchange-alt fa-4x text-gray-300 mb-3"></i>
                <h5 class="text-gray-500">Belum ada transfers</h5>
                <p class="text-gray-400">Buat transfers pertama Anda untuk memindahkan dana antar account.</p>
                <a href="{{ route('transfers.create') }}" class="btn btn-primary">Buat Transfers Pertama</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
