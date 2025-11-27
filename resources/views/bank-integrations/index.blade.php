@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Integrasi Bank</h1>
        <a href="{{ route('bank-integrations.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Integrasi
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

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Integrasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $integrations->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-university fa-2x text-gray-300"></i>
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
                                Integrasi Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $integrations->where('is_active', true)->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                                Total Saldo</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($integrations->where('is_active', true)->sum('current_balance'), 0, ',', '.') }}
                            </div>
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
            <h6 class="m-0 font-weight-bold text-primary">Daftar Integrasi Bank</h6>
        </div>
        <div class="card-body">
            @if($integrations->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Bank</th>
                            <th>Akun</th>
                            <th>Tipe Akun</th>
                            <th>Metode Integrasi</th>
                            <th>Status</th>
                            <th>Saldo</th>
                            <th>Terakhir Sync</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($integrations as $integration)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $integration->bank_name }}</strong>
                                @if($integration->account_number)
                                <br><small class="text-muted">{{ $integration->account_number }}</small>
                                @endif
                            </td>
                            <td>{{ $integration->account->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst(str_replace('_', ' ', $integration->account_type)) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ ucfirst($integration->integration_type) }}</span>
                            </td>
                            <td>
                                @if($integration->is_active)
                                <span class="badge badge-success">Aktif</span>
                                @else
                                <span class="badge badge-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                            <td>
                                @if($integration->current_balance !== null)
                                <strong>Rp {{ number_format($integration->current_balance, 0, ',', '.') }}</strong>
                                @if($integration->available_balance)
                                <br><small class="text-muted">Tersedia: Rp {{ number_format($integration->available_balance, 0, ',', '.') }}</small>
                                @endif
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($integration->last_sync_at)
                                <small>{{ $integration->last_sync_at->diffForHumans() }}</small>
                                @else
                                <span class="text-muted">Belum pernah</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('bank-integrations.show', $integration) }}" class="btn btn-info btn-sm">Lihat</a>
                                <a href="{{ route('bank-integrations.edit', $integration) }}" class="btn btn-warning btn-sm">Edit</a>
                                @if($integration->is_active)
                                <button
                                    type="button"
                                    class="btn btn-success btn-sm sync-btn"
                                    data-sync-url="{{ route('bank-integrations.sync', $integration) }}"
                                    title="Sync Data"
                                >
                                    <i class="fas fa-sync"></i>
                                </button>
                                @endif
                                <form action="{{ route('bank-integrations.destroy', $integration) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus integrasi ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center">
                <i class="fas fa-university fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-500">Belum ada integrasi bank</h5>
                <p class="text-gray-500">Tambahkan integrasi bank untuk mengimpor transaksi secara otomatis.</p>
                <a href="{{ route('bank-integrations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Buat Integrasi Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Sync Modal -->
<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sinkronisasi Bank</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="sync-progress" class="d-none">
                    <div class="progress mb-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                    </div>
                    <p class="text-center">Menyinkronkan data...</p>
                </div>
                <div id="sync-result"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const syncButtons = document.querySelectorAll('.sync-btn');

    syncButtons.forEach(button => {
        button.addEventListener('click', function() {
            const syncUrl = this.getAttribute('data-sync-url');
            syncIntegration(syncUrl);
        });
    });

    function syncIntegration(syncUrl) {
        const modal = new bootstrap.Modal(document.getElementById('syncModal'));
        const progressDiv = document.getElementById('sync-progress');
        const resultDiv = document.getElementById('sync-result');

        // Show modal and progress
        progressDiv.classList.remove('d-none');
        resultDiv.innerHTML = '';
        modal.show();

        // Make sync request
        fetch(syncUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                throw new Error('Invalid response');
            }

            progressDiv.classList.add('d-none');

            if (response.ok && data.success) {
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        <h6>Sinkronisasi Berhasil!</h6>
                        <p>${data.message}</p>
                        ${data.data && data.data.transactions_imported ? `<p>Transaksi diimpor: ${data.data.transactions_imported}</p>` : ''}
                    </div>
                `;
                // Reload page after 2 seconds to show updated data
                setTimeout(() => location.reload(), 2000);
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6>Sinkronisasi Gagal</h6>
                        <p>${data.message || 'Terjadi kesalahan saat menyinkronkan.'}</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            progressDiv.classList.add('d-none');
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6>Kesalahan Jaringan</h6>
                    <p>Terjadi kesalahan saat menyinkronkan. Silakan coba lagi.</p>
                    <small class="text-muted">${error.message || ''}</small>
                </div>
            `;
            console.error('Sync error:', error);
        });
    }
});
</script>
@endsection
