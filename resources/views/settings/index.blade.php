@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 font-weight-bold">Pengaturan Keuangan</h1>
            <p class="text-muted small">Kelola mata uang, profil risiko, dan konfigurasi lainnya.</p>
        </div>
        <a href="{{ route('settings.create') }}" class="btn btn-primary shadow-sm px-4">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah Pengaturan
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

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-4">
            <h6 class="m-0 font-weight-bold text-gray-800">Daftar Konfigurasi (Multi-Currency)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless text-gray-700" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light text-muted">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="py-3">Profil / Label</th>
                            <th class="py-3 text-center">Simbol</th>
                            <th class="py-3 text-right">Kurs (ke Base)</th>
                            <th class="py-3 text-center">Bulan Mulai</th>
                            <th class="py-3 text-center">Credit Score</th>
                            <th class="py-3">Profil Risiko</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($settings as $setting)
                        <tr class="border-bottom border-light hover-bg-light {{ $setting->is_default ? 'bg-soft-primary' : '' }}">
                            <td class="px-4 py-3 align-middle">{{ $loop->iteration }}</td>
                            <td class="py-3 align-middle">
                                <span class="font-weight-bold">{{ $setting->label ?? 'Default' }}</span>
                                @if($setting->is_default)
                                    <span class="badge badge-pill badge-primary ml-1" style="background-color: rgba(59, 130, 246, 0.1); color: #3b82f6;">Utama</span>
                                @endif
                            </td>
                            <td class="py-3 align-middle text-center">
                                <span class="badge badge-light px-3 py-1">{{ $setting->currency_symbol }}</span>
                            </td>
                            <td class="py-3 align-middle text-right font-weight-bold">
                                {{ number_format($setting->exchange_rate, floor($setting->exchange_rate) == $setting->exchange_rate ? 0 : 4) }}
                            </td>
                            <td class="py-3 align-middle text-center">{{ $setting->start_month }}</td>
                            <td class="py-3 align-middle text-center">{{ $setting->credit_score ?? '-' }}</td>
                            <td class="py-3 align-middle">
                                @if($setting->risk_profile)
                                    <span class="badge badge-pill @if($setting->risk_profile == 'aggressive') badge-danger @elseif($setting->risk_profile == 'balanced') badge-warning @else badge-success @endif px-3 py-1" style="opacity: 0.8;">
                                        {{ ucfirst($setting->risk_profile) }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 align-middle text-right">
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle btn btn-light btn-sm rounded-circle" href="#" role="button" id="dropdownMenuLink{{ $setting->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in border-0" aria-labelledby="dropdownMenuLink{{ $setting->id }}">
                                        <a class="dropdown-item" href="{{ route('settings.show', $setting) }}">Lihat Detail</a>
                                        <a class="dropdown-item" href="{{ route('settings.edit', $setting) }}">Edit</a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('settings.destroy', $setting) }}" method="POST" onsubmit="return confirm('Hapus pengaturan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Hapus</button>
                                        </form>
                                    </div>
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

<style>
    .bg-soft-primary { background-color: rgba(59, 130, 246, 0.03); }
    .hover-bg-light:hover { background-color: #f8fafc; }
</style>
@endsection
