@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Transfer</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Transfer</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('transfers.update', $transfer) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="from_account_id">From Account *</label>
                    <select class="form-control @error('from_account_id') is-invalid @enderror" id="from_account_id" name="from_account_id" required>
                        <option value="">Select Sender Account</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ old('from_account_id', $transfer->from_account_id) == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} (Balance: Rp {{ number_format($account->available_balance, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>
                    @error('from_account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="to_account_id">To Account *</label>
                    <select class="form-control @error('to_account_id') is-invalid @enderror" id="to_account_id" name="to_account_id" required>
                        <option value="">Select Receiver Account</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ old('to_account_id', $transfer->to_account_id) == $account->id ? 'selected' : '' }}>
                            {{ $account->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('to_account_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="amount">Jumlah Transfer *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $transfer->amount) }}" required>
                    </div>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="fee">Biaya Transfer</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="number" step="0.01" min="0" class="form-control @error('fee') is-invalid @enderror" id="fee" name="fee" value="{{ old('fee', $transfer->fee) }}">
                    </div>
                    @error('fee')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="transfer_date">Transfer Date *</label>
                    <input type="date" class="form-control @error('transfer_date') is-invalid @enderror" id="transfer_date" name="transfer_date" value="{{ old('transfer_date', $transfer->transfer_date->format('Y-m-d')) }}" required>
                    @error('transfer_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $transfer->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="reference_number">Nomor Referensi</label>
                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number', $transfer->reference_number) }}">
                    @error('reference_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Transfer Summary -->
                <div class="card border-info mb-3" id="transfer_summary">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0">Ringkasan Transfer</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Dari Akun:</strong> <span id="summary_from">{{ $transfer->fromAccount->name }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Ke Akun:</strong> <span id="summary_to">{{ $transfer->toAccount->name }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Jumlah:</strong> <span id="summary_amount">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Biaya:</strong> <span id="summary_fee">Rp {{ number_format($transfer->fee, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <strong class="text-primary">Total Debit:</strong> <span id="summary_total" class="text-primary font-weight-bold">Rp {{ number_format($transfer->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Transfer</button>
                <a href="{{ route('transfers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromAccountSelect = document.getElementById('from_account_id');
    const toAccountSelect = document.getElementById('to_account_id');
    const amountInput = document.getElementById('amount');
    const feeInput = document.getElementById('fee');

    function updateSummary() {
        const fromAccountId = fromAccountSelect.value;
        const toAccountId = toAccountSelect.value;
        const amount = parseFloat(amountInput.value) || 0;
        const fee = parseFloat(feeInput.value) || 0;

        if (fromAccountId && toAccountId && amount > 0) {
            const fromOption = fromAccountSelect.options[fromAccountSelect.selectedIndex];
            const toOption = toAccountSelect.options[toAccountSelect.selectedIndex];

            document.getElementById('summary_from').textContent = fromOption.text.split(' (Balance:')[0];
            document.getElementById('summary_to').textContent = toOption.text.split(' (Saldo:')[0];
            document.getElementById('summary_amount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('summary_fee').textContent = 'Rp ' + fee.toLocaleString('id-ID');
            document.getElementById('summary_total').textContent = 'Rp ' + (amount + fee).toLocaleString('id-ID');
        }
    }

    fromAccountSelect.addEventListener('change', updateSummary);
    toAccountSelect.addEventListener('change', updateSummary);
    amountInput.addEventListener('input', updateSummary);
    feeInput.addEventListener('input', updateSummary);
});
</script>
@endsection
