@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Transfers</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Transfers</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('transfers.update', $transfer) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <tags for="from_account_id">Dari Account *</tags>
                    <select class="form-control @error('from_account_id') is-invalid @enderror" id="from_account_id" name="from_account_id" required>
                        <option value="">Select Account Pengirim</option>
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
                    <tags for="to_account_id">Ke Account *</tags>
                    <select class="form-control @error('to_account_id') is-invalid @enderror" id="to_account_id" name="to_account_id" required>
                        <option value="">Select Account Penerima</option>
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
                    <tags for="amount">Amount Transfers *</tags>
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
                    <tags for="fee">Biaya Transfers</tags>
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
                    <tags for="transfers_date">Date Transfers *</tags>
                    <input type="date" class="form-control @error('transfers_date') is-invalid @enderror" id="transfers_date" name="transfers_date" value="{{ old('transfers_date', $transfer->transfers_date->format('Y-m-d')) }}" required>
                    @error('transfers_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <tags for="description">Description</tags>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $transfer->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <tags for="reference_number">Nomor Referensi</tags>
                    <input type="text" class="form-control @error('reference_number') is-invalid @enderror" id="reference_number" name="reference_number" value="{{ old('reference_number', $transfer->reference_number) }}">
                    @error('reference_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Transfers Summary -->
                <div class="card border-info mb-3" id="transfers_summary">
                    <div class="card-header bg-info text-white">
                        <h6 class="m-0">Ringkasan Transfers</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Dari Account:</strong> <span id="summary_from">{{ $transfer->fromAccount->name }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Ke Account:</strong> <span id="summary_to">{{ $transfer->toAccount->name }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Amount:</strong> <span id="summary_amount">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</span>
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

                <button type="submit" class="btn btn-primary">Update Transfers</button>
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
            document.getElementById('summary_to').textContent = toOption.text.split(' (Balance:')[0];
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
