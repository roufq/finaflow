@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Detail Akses Pengguna</h1>
            <p class="text-muted mb-0">Atur hak akses sidebar untuk <strong>{{ $user->name }}</strong> ({{ $user->email }}).</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-link">Back</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="mb-3">
                <div><strong>Nama:</strong> {{ $user->name }}</div>
                <div><strong>Email:</strong> {{ $user->email }}</div>
                <div><strong>Peran:</strong> {{ $user->roles->pluck('name')->implode(', ') ?: 'Tidak ada' }}</div>
                <div><strong>Status:</strong>
                    <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
            </div>

            @if($user->hasRole('admin'))
                <div class="alert alert-info mb-3">
                    Hak akses admin tidak dapat diubah di halaman ini. Admin selalu mendapatkan semua fitur.
                </div>
            @else
                @php
                    $selectedPlan = old('plan', $activePlan !== 'custom' ? $activePlan : null);
                @endphp
                <form method="POST" action="{{ route('admin.users.permissions', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Hak Akses Sidebar (SaaS Plan)</h6>
                        <p class="text-muted small mb-2">Pilih paket akses sesuai tier di SAAS.md. Paket akan otomatis mengatur izin pengguna.</p>
                        @if($activePlan === 'custom')
                            <div class="alert alert-warning small">
                                Hak akses pengguna ini belum sesuai paket manapun. Pilih salah satu paket untuk menyesuaikan sesuai SaaS tier.
                            </div>
                        @endif
                        <div class="row">
                            @foreach($plans as $planKey => $plan)
                                <div class="col-lg-4 col-md-6 mb-3">
                                    <div class="card h-100 {{ $selectedPlan === $planKey ? 'border-primary' : '' }}">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h5 class="card-title mb-1">{{ $plan['label'] }}</h5>
                                                    <p class="card-subtitle mb-0 text-muted small">{{ $plan['description'] }}</p>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="plan" id="plan-{{ $planKey }}" value="{{ $planKey }}" {{ $selectedPlan === $planKey ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="plan-{{ $planKey }}">Pilih</label>
                                                </div>
                                            </div>
                                            <ul class="mb-0 small text-muted">
                                                @foreach($plan['features'] as $feature)
                                                    <li>{{ $feature }}</li>
                                                @endforeach
                                            </ul>
                                            @if($activePlan === $planKey)
                                                <span class="badge badge-primary align-self-start mt-2">Sedang dipakai</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-muted small mb-0">Perubahan paket akan memperbarui izin sidebar sesuai daftar fitur pada tier yang dipilih.</p>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Access Package</button>
                </form>
            @endif

            <hr class="my-4">

            <h6 class="font-weight-bold">Status Pengguna</h6>
            <p class="text-muted small mb-2">Nonaktifkan pengguna dan berikan pesan yang akan tampil sebagai peringatan ketika mereka login.</p>
            <form method="POST" action="{{ route('admin.users.status', $user) }}">
                @csrf
                @method('PUT')
                <div class="form-check mb-3">
                    <input class="form-check-input" type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" name="is_active" id="status-active-{{ $user->id }}" value="1" {{ $user->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="status-active-{{ $user->id }}">Aktif</label>
                </div>
                <div class="form-group">
                    <label for="deactivation_message">Pesan untuk pengguna (jika dinonaktifkan)</label>
                    <textarea class="form-control" id="deactivation_message" name="deactivation_message" rows="3" placeholder="Contoh: Akun Anda dinonaktifkan sementara. Hubungi admin untuk informasi lebih lanjut.">{{ old('deactivation_message', $user->deactivation_message) }}</textarea>
                </div>
                <button type="submit" class="btn btn-outline-primary">Save Status</button>
            </form>
        </div>
    </div>
</div>
@endsection
