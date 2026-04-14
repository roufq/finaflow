@extends('layouts.app')

@section('content')
<div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Category Report</h1>
            <div class="d-flex">
                <a href="{{ route('reports.categories.export', request()->except('page')) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
                </a>
            </div>
        </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form id="category-report-filters" method="GET" action="{{ route('reports.categories.index') }}" class="form-row align-items-end">
                <div class="form-group col-md-3 mb-3">
                    <label for="period">Periode</label>
                    <select name="period" id="period" class="form-control">
                        <option value="this_month" {{ ($filters['period'] ?? 'this_month') === 'this_month' ? 'selected' : '' }}>Bulan ini</option>
                        <option value="this_year" {{ ($filters['period'] ?? '') === 'this_year' ? 'selected' : '' }}>Tahun ini</option>
                        <option value="ytd" {{ ($filters['period'] ?? '') === 'ytd' ? 'selected' : '' }}>YTD</option>
                        <option value="last_12_months" {{ ($filters['period'] ?? '') === 'last_12_months' ? 'selected' : '' }}>12 Bulan</option>
                        <option value="custom" {{ ($filters['period'] ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>
                <div class="form-group col-md-3 mb-3">
                    <label for="start_date">Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date', $filters['start']->toDateString()) }}" class="form-control">
                </div>
                <div class="form-group col-md-3 mb-3">
                    <label for="end_date">Sampai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date', $filters['end']->toDateString()) }}" class="form-control">
                </div>
                <div class="form-group col-md-3 mb-3">
                    <label for="category_id">Kategori</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Semua</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (int) request('category_id') === $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3 mb-3">
                    <label for="account_id">Akun</label>
                    <select name="account_id" id="account_id" class="form-control">
                        <option value="">Semua</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ (int) request('account_id') === $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary mt-4">
                        <i class="fas fa-filter"></i> Terapkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ringkasan per Kategori ({{ $periodLabel }})</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center">Transaksi</th>
                                    <th class="text-right">Total Pengeluaran</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($summary as $row)
                                    <tr>
                                        <td>{{ $row['category_name'] }}</td>
                                        <td class="text-center">{{ number_format($row['transactions_count'], 0, ',', '.') }}</td>
                                        <td class="text-right">Rp {{ number_format($row['expense_total'], 0, ',', '.') }}</td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-sm btn-outline-primary js-detail" data-category-id="{{ $row['category_id'] }}" data-category-name="{{ $row['category_name'] }}" data-detail-url="{{ route('reports.categories.detail', $row['category_id']) }}">
                                                Detail
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada transaksi pada rentang ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow mb-4 h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Kategori (Stacked)</h6>
                </div>
                <div class="card-body">
                    <canvas id="categorySummaryChart" data-chart='@json($chartSummary)'></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">12 Bulan Terakhir (Stacked per Kategori)</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryMonthlyChart" data-chart='@json($monthlyStacks)'></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="categoryDetailModal" tabindex="-1" role="dialog" aria-labelledby="categoryDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryDetailModalLabel">Detail Kategori</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div class="small text-muted">Total Pengeluaran</div>
                        <div class="h5 mb-0" id="detailTotal">Rp 0</div>
                    </div>
                    <div>
                        <div class="small text-muted">Jumlah Transaksi</div>
                        <div class="h5 mb-0" id="detailCount">0</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Deskripsi</th>
                                <th>Akun</th>
                                <th class="text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody id="detailBody">
                            <tr>
                                <td colspan="4" class="text-center text-muted">Pilih kategori untuk melihat detail.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        const summaryCanvas = document.getElementById('categorySummaryChart');
        if (summaryCanvas) {
            const summaryData = JSON.parse(summaryCanvas.dataset.chart || '{"labels":[],"datasets":[]}');
            if (summaryData.labels.length > 0 && window.Chart) {
                new Chart(summaryCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: summaryData.labels,
                        datasets: summaryData.datasets
                    },
                    options: {
                        responsive: true,
                        scales: {
                            xAxes: [{ stacked: true }],
                            yAxes: [{ stacked: true, ticks: { beginAtZero: true } }]
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                });
            }
        }

        const monthlyCanvas = document.getElementById('categoryMonthlyChart');
        if (monthlyCanvas) {
            const monthlyData = JSON.parse(monthlyCanvas.dataset.chart || '{"labels":[],"datasets":[]}');
            if (monthlyData.labels.length > 0 && window.Chart) {
                new Chart(monthlyCanvas.getContext('2d'), {
                    type: 'bar',
                    data: monthlyData,
                    options: {
                        responsive: true,
                        scales: {
                            xAxes: [{ stacked: true }],
                            yAxes: [{ stacked: true, ticks: { beginAtZero: true } }]
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                });
            }
        }

        const formatRupiah = (value) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
        };

        const detailButtons = document.querySelectorAll('.js-detail');
        const detailBody = document.getElementById('detailBody');
        const detailTotal = document.getElementById('detailTotal');
        const detailCount = document.getElementById('detailCount');
        const modalEl = $('#categoryDetailModal');
        const filterForm = document.getElementById('category-report-filters');

        detailButtons.forEach((button) => {
            button.addEventListener('click', () => {
                if (!filterForm) {
                    return;
                }

                const formData = new FormData(filterForm);
                const url = new URL(button.dataset.detailUrl, window.location.origin);
                formData.forEach((value, key) => {
                    if (value) {
                        url.searchParams.set(key, value.toString());
                    }
                });

                detailBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Memuat data...</td></tr>';
                detailTotal.textContent = 'Rp 0';
                detailCount.textContent = '0';

                fetch(url.toString())
                    .then((response) => response.json())
                    .then((payload) => {
                        detailTotal.textContent = formatRupiah(payload.totals.amount || 0);
                        detailCount.textContent = payload.totals.transactions || 0;

                        if (!payload.transactions || payload.transactions.length === 0) {
                            detailBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Belum ada transaksi.</td></tr>';
                        } else {
                            detailBody.innerHTML = '';
                            payload.transactions.forEach((transaction) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td>${transaction.date || '-'}</td>
                                    <td>${transaction.description || '-'}</td>
                                    <td>${transaction.account || '-'}</td>
                                    <td class="text-right">${formatRupiah(transaction.amount || 0)}</td>
                                `;
                                detailBody.appendChild(row);
                            });
                        }

                        modalEl.modal('show');
                        document.getElementById('categoryDetailModalLabel').textContent = `Detail ${button.dataset.categoryName}`;
                    })
                    .catch(() => {
                        detailBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load details.</td></tr>';
                    });
            });
        });
    })();
</script>
@endsection
