@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-0">Detail Akses Pengguna</h1>
            <p class="text-muted mb-0">Atur hak akses sidebar untuk <strong>{{ $user->name }}</strong> ({{ $user->email }}).</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn btn-link">Kembali</a>
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
                <form method="POST" action="{{ route('admin.users.permissions', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <h6 class="font-weight-bold">Hak Akses Sidebar</h6>
                        <p class="text-muted small mb-2">Aktifkan fitur yang perlu untuk membatasi akses pengguna.</p>
                        <ul class="text-muted small mb-3">
                            <li>Access Dashboard: halaman Dashboard.</li>
                            <li>Manage Transactions: menu Transaksi & Tag.</li>
                            <li>Manage Accounts: menu Akun & Transfer.</li>
                            <li>Manage Budgets: menu Anggaran & Utang.</li>
                            <li>Manage Goals: menu Goals.</li>
                            <li>View Reports: Reporting, Analytics, Investments, Assets, Net Worth, Tax Documents.</li>
                            <li>Manage Automations: Automations & Integrations.</li>
                            <li>Manage Family: Subscriptions & Rewards/Loyalty.</li>
                            <li>Manage Behavioral: Behavioral Insights, Education, Coaching.</li>
                        </ul>
                        <div class="row">
                            @foreach($availablePermissions as $permission)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="permissions[]" id="perm-{{ Str::slug($permission) }}" value="{{ $permission }}"
                                            {{ in_array($permission, $userPermissions, true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="perm-{{ Str::slug($permission) }}">
                                            {{ ucwords(str_replace('_', ' ', $permission)) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Hak Akses</button>
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
                <button type="submit" class="btn btn-outline-primary">Simpan Status</button>
            </form>
        </div>
    </div>
</div>
@endsection
