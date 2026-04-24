@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Execute Transfer</h1>
            <p class="text-sm font-medium text-slate-500">Initiate a secure internal capital shift between your registered accounts.</p>
        </div>
        <a href="{{ route('transfers.index') }}" class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Form & Summary Grid -->
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Main Form Area -->
        <div class="lg:col-span-8">
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <form action="{{ route('transfers.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <!-- Corridor Selection -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="from_account_id" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Origin Account <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="from_account_id" name="from_account_id" required
                                        class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('from_account_id') ring-rose-500 @enderror">
                                    <option value="">Select Origin...</option>
                                    @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('from_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }} (Available: Rp {{ number_format($account->available_balance, 0, ',', '.') }})
                                    </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                            @error('from_account_id') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="to_account_id" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Target Account <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="to_account_id" name="to_account_id" required
                                        class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('to_account_id') ring-rose-500 @enderror">
                                    <option value="">Select Destination...</option>
                                    @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                            @error('to_account_id') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Financial Details -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="amount" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Capital Transfer Volume <span class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                                    <span class="text-xs font-black text-slate-400 group-focus-within:text-primary-500 transition-colors">Rp</span>
                                </div>
                                <input type="number" step="0.01" min="0.01" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" required
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('amount') ring-rose-500 @enderror">
                            </div>
                            @error('amount') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="fee" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Friction Costs (Fees)</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                                    <span class="text-xs font-black text-slate-400 group-focus-within:text-rose-500 transition-colors">Rp</span>
                                </div>
                                <input type="number" step="0.01" min="0" id="fee" name="fee" value="{{ old('fee', 0) }}" placeholder="0.00"
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>
                    </div>

                    <!-- Temporal & Metadata -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="transfer_date" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Execution Log Date <span class="text-rose-500">*</span></label>
                            <input type="date" id="transfer_date" name="transfer_date" value="{{ old('transfer_date', date('Y-m-d')) }}" required
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                        </div>

                        <div class="space-y-2">
                            <label for="reference_number" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Audit Reference ID (Bank/System)</label>
                            <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number') }}" placeholder="e.g. TRX-99827..."
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Operational Context (Optional)</label>
                        <textarea id="description" name="description" rows="3" placeholder="Contextual notes for this capital movement..."
                                  class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-medium text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-300 focus:bg-white focus:ring-4 focus:ring-primary-500/10">{{ old('description') }}</textarea>
                    </div>

                    <!-- Form Actions -->
                    <div class="pt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end border-t border-slate-50">
                        <a href="{{ route('transfers.index') }}" class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                            Discard
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                            <i class="fas fa-paper-plane mr-2 text-primary-400"></i>
                            Initiate Transfer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Real-time Manifest Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <div id="transfers_summary" class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10 hidden">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8">Liquidity Manifest</h3>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 ring-1 ring-white/10 text-rose-500">
                            <i class="fas fa-sign-out-alt"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[8px] font-bold text-slate-500 uppercase">Origin</p>
                            <p id="summary_from" class="truncate text-xs font-black text-white">-</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/5 ring-1 ring-white/10 text-emerald-500">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[8px] font-bold text-slate-500 uppercase">Destination</p>
                            <p id="summary_to" class="truncate text-xs font-black text-white">-</p>
                        </div>
                    </div>

                    <div class="h-px bg-white/10 my-6"></div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-bold text-slate-400 uppercase">Principal</span>
                            <span id="summary_amount" class="text-xs font-black text-white">Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-bold text-slate-400 uppercase">Friction</span>
                            <span id="summary_fee" class="text-xs font-bold text-rose-400">Rp 0</span>
                        </div>
                        <div class="pt-3 border-t border-white/5 flex items-center justify-between">
                            <span class="text-[10px] font-black text-primary-400 uppercase tracking-widest">Total Debit</span>
                            <span id="summary_total" class="text-lg font-black text-white tracking-tighter">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Operational Security Notice -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100 flex flex-col items-center text-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-900 shadow-sm border border-slate-100">
                    <i class="fas fa-shield-halved text-xl"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-1">Atomic Execution</h4>
                    <p class="text-[10px] font-medium text-slate-400 leading-relaxed">
                        Internal transfers are executed as atomic ledger operations. Credits and debits are processed simultaneously to ensure zero-point discrepancy.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromAccountSelect = document.getElementById('from_account_id');
    const toAccountSelect = document.getElementById('to_account_id');
    const amountInput = document.getElementById('amount');
    const feeInput = document.getElementById('fee');
    const summaryContainer = document.getElementById('transfers_summary');

    function updateSummary() {
        const fromAccountId = fromAccountSelect.value;
        const toAccountId = toAccountSelect.value;
        const amount = parseFloat(amountInput.value) || 0;
        const fee = parseFloat(feeInput.value) || 0;

        if (fromAccountId && toAccountId && amount > 0) {
            const fromOption = fromAccountSelect.options[fromAccountSelect.selectedIndex];
            const toOption = toAccountSelect.options[toAccountSelect.selectedIndex];

            document.getElementById('summary_from').textContent = fromOption.text.split(' (Available:')[0];
            document.getElementById('summary_to').textContent = toOption.text;
            document.getElementById('summary_amount').textContent = 'Rp ' + amount.toLocaleString('id-ID');
            document.getElementById('summary_fee').textContent = 'Rp ' + fee.toLocaleString('id-ID');
            document.getElementById('summary_total').textContent = 'Rp ' + (amount + fee).toLocaleString('id-ID');

            summaryContainer.classList.remove('hidden');
        } else {
            summaryContainer.classList.add('hidden');
        }
    }

    fromAccountSelect.addEventListener('change', updateSummary);
    toAccountSelect.addEventListener('change', updateSummary);
    amountInput.addEventListener('input', updateSummary);
    feeInput.addEventListener('input', updateSummary);

    // Initial update
    updateSummary();
});
</script>
@endpush
@endsection
