@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">New Transaction</h1>
            <p class="text-sm font-medium text-slate-500">Record a new financial movement in your ledger.</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
        <form action="{{ route('transactions.store') }}" method="POST" class="space-y-8">
            @csrf
            
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- Account Selection -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Source Account</label>
                    <div class="relative">
                        <select id="account_id" name="account_id" required
                                class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                            <option value="">Select an account...</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ $account->setting->currency_symbol ?? 'Rp' }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                <!-- Category Selection -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Classification</label>
                    <div class="relative">
                        <select id="category_id" name="category_id" required
                                class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                            <option value="">Select a category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ ucfirst($category->type) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>

                <!-- Date Selection -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Transaction Date</label>
                    <input type="date" id="transaction_date" name="transaction_date" value="{{ old('transaction_date', date('Y-m-d')) }}" required
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                </div>

                <!-- Type Selection -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Flow Type</label>
                    <div class="grid grid-cols-2 gap-3 p-1 rounded-2xl bg-slate-50 ring-1 ring-slate-200">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="income" class="peer sr-only" {{ old('type') == 'income' ? 'checked' : '' }}>
                            <div class="flex items-center justify-center rounded-xl py-2.5 text-xs font-bold text-slate-400 transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm">
                                <i class="fas fa-arrow-up mr-2 text-[10px]"></i> Income
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="type" value="expense" class="peer sr-only" {{ old('type', 'expense') == 'expense' ? 'checked' : '' }}>
                            <div class="flex items-center justify-center rounded-xl py-2.5 text-xs font-bold text-slate-400 transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm">
                                <i class="fas fa-arrow-down mr-2 text-[10px]"></i> Expense
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Amount Input -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Transaction Amount</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-6">
                        <span id="currency-symbol" class="text-lg font-extrabold text-slate-400 transition-colors group-focus-within:text-primary-500">Rp</span>
                    </div>
                    <input type="number" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" required
                           class="w-full rounded-2xl border-none bg-slate-50 pl-16 pr-5 py-5 text-2xl font-extrabold text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-200 focus:ring-4 focus:ring-primary-500/10">
                </div>
                <p class="text-[10px] text-slate-400 font-medium ml-1">Ensure the amount is precise for accurate financial reporting.</p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Reference Notes (Optional)</label>
                <textarea id="description" name="description" rows="3" placeholder="What is the context of this transaction?"
                          class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-medium text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-300 focus:ring-2 focus:ring-primary-500/20">{{ old('description') }}</textarea>
            </div>

            <!-- Actions -->
            <div class="pt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('transactions.index') }}" 
                   class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                    Process Transaction
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const accountCurrencies = @json($accounts->mapWithKeys(fn($a) => [$a->id => $a->setting->currency_symbol ?? 'Rp']));
    const currencyEl = document.getElementById('currency-symbol');
    const accountSelect = document.getElementById('account_id');

    const updateSymbol = (id) => {
        const symbol = accountCurrencies[id] || 'Rp';
        currencyEl.textContent = symbol;
    };

    accountSelect.addEventListener('change', (e) => updateSymbol(e.target.value));
    
    if (accountSelect.value) {
        updateSymbol(accountSelect.value);
    }
</script>
@endpush
@endsection
