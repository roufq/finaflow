@extends('layouts.app')

@section('title', 'Tags')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tags</h1>
        <a href="{{ route('tags.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Tag
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

    <div class="row">
        @forelse($tags as $tag)
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    <span class="badge" style="background-color: {{ $tag->color }}; color: white;">
                                        {{ $tag->name }}
                                    </span>
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $tag->getTransactionsCount() }} transaksi
                                </div>
                                @if($tag->description)
                                    <div class="text-xs text-muted mt-1">
                                        {{ Str::limit($tag->description, 50) }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-auto">
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink{{ $tag->id }}"
                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                         aria-labelledby="dropdownMenuLink{{ $tag->id }}">
                                        <a class="dropdown-item" href="{{ route('tags.show', $tag) }}">
                                            <i class="fas fa-eye fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Lihat Detail
                                        </a>
                                        <a class="dropdown-item" href="{{ route('tags.edit', $tag) }}">
                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus tag ini?')">
                                                <i class="fas fa-trash fa-sm fa-fw mr-2"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-500">Belum ada tag</h5>
                        <p class="text-gray-400 mb-4">Buat tag pertama Anda untuk mengorganisir transaksi dengan lebih baik.</p>
                        <a href="{{ route('tags.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Tag Pertama
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
