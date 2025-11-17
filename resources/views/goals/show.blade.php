@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $goal->name }}</h1>
        <div>
            <a href="{{ route('goals.edit', $goal) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="{{ route('goals.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Goal Progress Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Progress Goal</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="progress-circle" data-progress="{{ $goal->progress_percentage }}">
                            <span class="progress-text">{{ $goal->progress_percentage }}%</span>
                        </div>
                        <h4 class="mt-3">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</h4>
                        <p class="text-muted">dari Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                        <p class="text-muted">Sisa: Rp {{ number_format($goal->target_amount - $goal->current_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Goal Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Goal</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Kategori:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $goal->getCategoryLabel() }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Tipe:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $goal->type_label }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Target Tanggal:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $goal->target_date->format('d M Y') }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="badge badge-{{ $goal->status_color }}">{{ $goal->status_label }}</span>
                        </div>
                    </div>
                    @if($goal->description)
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <strong>Deskripsi:</strong>
                            <p class="mt-2">{{ $goal->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progress History & Actions -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Progress</h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p>Riwayat progress akan ditampilkan di sini</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-success btn-block" data-toggle="modal" data-target="#addProgressModal">
                                <i class="fas fa-plus"></i> Tambah Progress
                            </button>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus goal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Hapus Goal
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Progress Modal -->
<div class="modal fade" id="addProgressModal" tabindex="-1" role="dialog" aria-labelledby="addProgressModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProgressModalLabel">Tambah Progress</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addProgressForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="progress_amount">Jumlah Progress (Rp)</label>
                        <input type="number" class="form-control" id="progress_amount" name="current_amount" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.progress-circle {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto;
    border-radius: 50%;
    background: conic-gradient(#4e73df 0% var(--progress), #e9ecef var(--progress) 100%);
}

.progress-circle::before {
    content: '';
    position: absolute;
    top: 10px;
    left: 10px;
    right: 10px;
    bottom: 10px;
    border-radius: 50%;
    background: white;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 18px;
    font-weight: bold;
    color: #4e73df;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressCircles = document.querySelectorAll('.progress-circle');
    progressCircles.forEach(circle => {
        const progress = circle.dataset.progress;
        circle.style.setProperty('--progress', progress + '%');
    });

    // Handle add progress form
    document.getElementById('addProgressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(`{{ route('goals.updateProgress', $goal) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>
@endsection
