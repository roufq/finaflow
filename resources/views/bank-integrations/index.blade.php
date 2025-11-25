@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Integrasi Bank</h2>
                <a href="{{ route('bank-integrations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Integrasi
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if($integrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Bank</th>
                                        <th>Akun</th>
                                        <th>Tipe</th>
                                        <th>Status</th>
                                        <th>Saldo</th>
                                        <th>Terakhir Sync</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($integrations as $integration)
                                        <tr>
                                            <td>
                                                <strong>{{ $integration->bank_name }}</strong>
                                                @if($integration->account_number)
                                                    <br><small class="text-muted">{{ $integration->account_number }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($integration->account)
                                                    {{ $integration->account->name }}
                                                    <br><small class="text-muted">{{ $integration->account->account_number ?? 'No Account Number' }}</small>
                                                @else
                                                    <span class="text-danger">Akun tidak ditemukan</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($integration->integration_type) }}</span>
                                                <br><small class="text-muted">{{ ucfirst($integration->account_type) }}</small>
                                            </td>
                                            <td>
                                                @if($integration->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-warning">Non-aktif</span>
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
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('bank-integrations.show', $integration) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('bank-integrations.edit', $integration) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if($integration->is_active)
                                                        <button type="button" class="btn btn-sm btn-outline-success sync-btn" data-id="{{ $integration->id }}" title="Sync Sekarang">
                                                            <i class="fas fa-sync-alt"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bank fa-3x text-muted mb-3"></i>
                            <h5>Belum ada integrasi bank</h5>
                            <p class="text-muted">Tambahkan integrasi bank untuk mengimpor transaksi secara otomatis.</p>
                            <a href="{{ route('bank-integrations.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Integrasi Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
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
            const integrationId = this.getAttribute('data-id');
            syncIntegration(integrationId);
        });
    });

    function syncIntegration(integrationId) {
        const modal = new bootstrap.Modal(document.getElementById('syncModal'));
        const progressDiv = document.getElementById('sync-progress');
        const resultDiv = document.getElementById('sync-result');

        // Show modal and progress
        progressDiv.classList.remove('d-none');
        resultDiv.innerHTML = '';
        modal.show();

        // Make sync request
        fetch(`/bank-integrations/${integrationId}/sync`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            progressDiv.classList.add('d-none');

            if (data.success) {
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
                        <p>${data.message}</p>
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
                </div>
            `;
            console.error('Sync error:', error);
        });
    }
});
</script>
@endsection
