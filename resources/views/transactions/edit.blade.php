@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Edit Transaction</h1>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 font-weight-bold text-primary">Pembaruan Transaction History</h6>
        </div>
        <div class="card-body px-4 pb-4">
            <form action="{{ route('transactions.update', $transaction) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-gray-700" for="account_id">Account Penyalur</label>
                            <select class="form-control form-control-modern" id="account_id" name="account_id" required>
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-gray-700" for="category_id">Category</label>
                            <select class="form-control form-control-modern" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }} ({{ ucfirst($category->type) }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-gray-700" for="transaction_date">Transaction Date</label>
                            <input type="date" class="form-control form-control-modern" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-gray-700" for="type">Type</label>
                            <select class="form-control form-control-modern" id="type" name="type" required>
                                <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Pemasukan</option>
                                <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="small font-weight-bold text-gray-700" for="amount">Nominal (<span id="currency-symbol">{{ $transaction->account?->setting->currency_symbol ?? 'Rp' }}</span>)</label>
                    <input type="number" step="0.01" class="form-control form-control-modern font-weight-bold" id="amount" name="amount" value="{{ old('amount', $transaction->amount) }}" required>
                </div>

                <div class="form-group mb-4">
                    <label class="small font-weight-bold text-gray-700" for="description">Description (Opsional)</label>
                    <textarea class="form-control form-control-modern" id="description" name="description" rows="3">{{ old('description', $transaction->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end" style="gap: 12px;">
                    <a href="{{ route('transactions.index') }}" class="btn btn-light px-4 font-weight-bold">Cancel</a>
                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm" style="background-color: #3b82f6;">Perbarui Transactions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-control-modern {
        border-radius: 10px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 0.6rem 1rem !important;
        background-color: #f8fafc !important;
    }
    .form-control-modern:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
        background-color: #fff !important;
    }
</style>

@push('scripts')
<script>
    const accountCurrencies = @json($accounts->mapWithKeys(fn($a) => [$a->id => $a->setting->currency_symbol ?? 'Rp']));
    
    document.getElementById('account_id').addEventListener('change', function() {
        const symbol = accountCurrencies[this.value] || 'Rp';
        document.getElementById('currency-symbol').textContent = symbol;
    });

    // Initial load
    window.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('account_id');
        if (select.value) {
            const symbol = accountCurrencies[select.value] || 'Rp';
            document.getElementById('currency-symbol').textContent = symbol;
        }
    });
</script>
@endpush
@endsection
