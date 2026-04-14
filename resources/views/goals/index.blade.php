@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Tujuan Finansial</h1>
            <p class="text-muted small">Kelola target keuangan Anda untuk masa depan yang lebih baik.</p>
        </div>
        <a href="{{ route('goals.create') }}" class="d-none d-sm-inline-block btn btn-primary shadow-sm px-4">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Tujuan Baru
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
        @forelse($goals as $goal)
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100 position-relative overflow-hidden group">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: rgba(59, 130, 246, 0.1);">
                            <i class="fas fa-bullseye fa-lg text-primary"></i>
                        </div>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle text-muted" href="#" role="button" id="dropdownMenuLink{{ $goal->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{ $goal->id }}">
                                <div class="dropdown-header">Aksi:</div>
                                <a class="dropdown-item" href="{{ route('goals.edit', $goal) }}">Edit</a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('goals.destroy', $goal) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit" onclick="return confirm('Hapus tujuan?')">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('goals.show', $goal) }}" class="text-decoration-none">
                            <h5 class="font-weight-bold text-gray-800 mb-1 text-truncate" title="{{ $goal->name }}">{{ $goal->name }}</h5>
                        </a>
                        
                        <div class="mt-3">
                            <div class="text-xs font-weight-bold text-muted text-uppercase mb-1">Terkumpul</div>
                            <div class="h5 mb-0 font-weight-bold text-primary">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</div>
                            <div class="text-xs text-muted mt-1 text-truncate">Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</div>
                        </div>

                        <div class="progress mt-3" style="height: 8px; border-radius: 4px; background-color: #f1f5f9;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $goal->progress_percentage }}%; background-color: #3b82f6;" aria-valuenow="{{ $goal->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="font-weight-bold" style="color: #64748b;">{{ $goal->progress_percentage }}% selesai</small>
                            @if($goal->progress_percentage >= 100)
                                <small class="text-success font-weight-bold"><i class="fas fa-check-circle"></i></small>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4 text-center">
                    <a href="{{ route('goals.show', $goal) }}" class="btn btn-light btn-sm btn-block text-primary font-weight-bold rounded-lg" style="background-color: rgba(59, 130, 246, 0.05);">Top-up & Detail</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-bullseye fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">Belum ada goals</h5>
                    <p class="text-gray-500">Mulai buat goal finansial pertama Anda</p>
                    <a href="{{ route('goals.create') }}" class="btn btn-primary">Buat Goal Pertama</a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
