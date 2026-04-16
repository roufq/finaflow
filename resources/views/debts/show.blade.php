@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $debt->name }}</h1>
        <div>
            <a href="{{ route('debts.edit', $debt) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm text-white-50"></i> Edit
            </a>
            <a href="{{ route('debts.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Debt Progress Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Progress Pelunasan</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="progress-circle {{ $debt->status == 'paid_off' ? 'paid-off' : ($debt->status == 'defaulted' ? 'defaulted' : '') }}" data-progress="{{ $debt->payoff_progress }}">
                            <span class="progress-text">{{ number_format($debt->payoff_progress, 1) }}%</span>
                        </div>
                        <h4 class="mt-3">Rp {{ number_format($debt->current_balance, 0, ',', '.') }}</h4>
                        <p class="text-muted">dari Rp {{ number_format($debt->original_amount, 0, ',', '.') }}</p>
                        <p class="text-success">Sudah paid: Rp {{ number_format($debt->original_amount - $debt->current_balance, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Debt Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Debt Details</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Type:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $debt->type_tags }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Pemberi Pinjaman:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $debt->lender }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Suku Bunga:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $debt->interest_rate }}%
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Pembayaran Minimum:</strong>
                        </div>
                        <div class="col-sm-6">
                            Rp {{ number_format($debt->minimum_payment, 0, ',', '.') }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Jatuh Tempo:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $debt->due_date->format('d M Y') }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Strategi Pelunasan:</strong>
                        </div>
                        <div class="col-sm-6">
                            {{ $debt->payoff_strategy_tags }}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="badge badge-{{ $debt->status_color }}">{{ $debt->status_tags }}</span>
                        </div>
                    </div>
                    @if($debt->description)
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <strong>Description:</strong>
                            <p class="mt-2">{{ $debt->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Payment History & Actions -->
        <div class="col-xl-8 col-lg-7">
            <!-- Payment History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">History Pembayaran</h6>
                </div>
                <div class="card-body">
                    @if($debt->payment_history && count($debt->payment_history) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Balance Setelah</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(array_reverse($debt->payment_history) as $payment)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($payment['date'])->format('d M Y') }}</td>
                                            <td>Rp {{ number_format($payment['amount'], 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($payment['balance_after'], 0, ',', '.') }}</td>
                                            <td>{{ $payment['notes'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-history fa-3x mb-3"></i>
                            <p>Belum ada history pembayaran</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Action Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <button class="btn btn-success btn-block" data-toggle="modal" data-target="#addPaymentModal">
                                <i class="fas fa-plus"></i> Bayar Debts
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-info btn-block" data-toggle="modal" data-target="#calculateModal">
                                <i class="fas fa-calculator"></i> Hitung Cicilan
                            </button>
                        </div>
                        <div class="col-md-4">
                            <form action="{{ route('debts.destroy', $debt) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda are you sure you want to delete debts ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> Delete Debts
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Debt Calculator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kalkulator Debts</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Estimasi Waktu Pelunasan</h6>
                            <p class="text-muted">Dengan pembayaran minimum:</p>
                            <h5 class="text-primary">{{ $debt->estimated_payoff_months }} month</h5>
                            <small class="text-muted">Total pembayaran: Rp {{ number_format($debt->estimated_total_payment, 0, ',', '.') }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Total Bunga</h6>
                            <p class="text-muted">Dengan pembayaran minimum:</p>
                            <h5 class="text-warning">Rp {{ number_format($debt->estimated_total_interest, 0, ',', '.') }}</h5>
                            <small class="text-muted">{{ number_format($debt->estimated_interest_percentage, 1) }}% dari total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">Add Pembayaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addPaymentForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="payment_amount">Amount Pembayaran (Rp)</label>
                        <input type="number" class="form-control" id="payment_amount" name="payment_amount" min="0" max="{{ $debt->current_balance }}" required>
                        <small class="form-text text-muted">Maksimal: Rp {{ number_format($debt->current_balance, 0, ',', '.') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="payment_date">Date Pembayaran</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Calculate Modal -->
<div class="modal fade" id="calculateModal" tabindex="-1" role="dialog" aria-labelledby="calculateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="calculateModalLabel">Kalkulator Pelunasan Debts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-tags="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Simulasi Pembayaran Lebih</h6>
                        <div class="form-group">
                            <label for="extra_payment">Pembayaran Tambahan per Month (Rp)</label>
                            <input type="number" class="form-control" id="extra_payment" min="0">
                        </div>
                        <button class="btn btn-primary btn-block" id="calculateBtn">Hitung</button>
                    </div>
                    <div class="col-md-6">
                        <div id="calculationResult" style="display: none;">
                            <h6>Hasil Perhitungan</h6>
                            <p>Waktu pelunasan: <span id="newPayoffTime"></span></p>
                            <p>Total pembayaran: <span id="newTotalPayment"></span></p>
                            <p>Hemat bunga: <span id="savedInterest"></span></p>
                        </div>
                    </div>
                </div>
            </div>
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
    background: conic-gradient(#f6c23e 0% var(--progress), #e9ecef var(--progress) 100%);
}

.progress-circle.paid-off {
    background: conic-gradient(#1cc88a 0% var(--progress), #e9ecef var(--progress) 100%);
}

.progress-circle.defaulted {
    background: conic-gradient(#e74a3b 0% var(--progress), #e9ecef var(--progress) 100%);
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
    color: #f6c23e;
}

.progress-circle.paid-off .progress-text {
    color: #1cc88a;
}

.progress-circle.defaulted .progress-text {
    color: #e74a3b;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressCircles = document.querySelectorAll('.progress-circle');
    progressCircles.forEach(circle => {
        const progress = circle.dataset.progress;
        circle.style.setProperty('--progress', progress + '%');
    });

    // Handle add payment form
    document.getElementById('addPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(`{{ route('debts.addPayment', $debt) }}`, {
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

    // Handle calculation
    document.getElementById('calculateBtn').addEventListener('click', function() {
        const extraPayment = parseFloat(document.getElementById('extra_payment').value) || 0;
        const resultDiv = document.getElementById('calculationResult');

        // Simple calculation (would be more complex in real implementation)
        const monthlyPayment = {{ $debt->minimum_payment }} + extraPayment;
        const remainingBalance = {{ $debt->current_balance }};
        const interestRate = {{ $debt->interest_rate }} / 100 / 12;

        // Estimate months to payoff
        let months = 0;
        let balance = remainingBalance;
        let totalPayment = 0;

        while (balance > 0 && months < 600) { // Max 50 years
            const interest = balance * interestRate;
            const principal = Math.min(monthlyPayment - interest, balance);
            balance -= principal;
            totalPayment += monthlyPayment;
            months++;
        }

        const originalTotal = {{ $debt->estimated_total_payment }};
        const savedInterest = originalTotal - totalPayment;

        document.getElementById('newPayoffTime').textContent = months + ' month';
        document.getElementById('newTotalPayment').textContent = 'Rp ' + totalPayment.toLocaleString('id-ID');
        document.getElementById('savedInterest').textContent = 'Rp ' + savedInterest.toLocaleString('id-ID');

        resultDiv.style.display = 'block';
    });
});
</script>
@endsection
