@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Edit Account</h1>
            <p class="text-sm font-medium text-slate-500">Modify the registration details for <span class="text-primary-600 font-bold">{{ $account->name }}</span>.</p>
        </div>
        <a href="{{ route('accounts.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Accounts
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
        <form action="{{ route('accounts.update', $account) }}" method="POST" class="space-y-8" x-data="{ accountType: '{{ old('type', $account->type) }}' }">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- Account Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Account Display Name *</label>
                    <input type="text" name="name" value="{{ old('name', $account->name) }}" placeholder="e.g. Personal Savings" required
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20 @error('name') ring-rose-500 @enderror">
                    @error('name') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                </div>

                <!-- Account Type -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Financial Classification *</label>
                    <div class="relative">
                        <select id="type" name="type" required x-model="accountType"
                                class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                            @foreach($accountTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('type', $account->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                    </div>
                </div>

                <!-- Currency Selection -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Currency Profile *</label>
                    <div class="relative">
                        <select id="setting_id" name="setting_id" required
                                class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                            @foreach($settings as $setting)
                                <option value="{{ $setting->id }}" data-symbol="{{ $setting->currency_symbol }}" {{ old('setting_id', $account->setting_id) == $setting->id ? 'selected' : '' }}>
                                    {{ $setting->tags }} ({{ $setting->currency_symbol }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                    </div>
                </div>

                <!-- Balance -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Current Ledger Balance *</label>
                    <div class="relative">
                        <span class="currency-symbol absolute left-5 top-1/2 -translate-y-1/2 text-sm font-extrabold text-slate-400">{{ $account->setting->currency_symbol ?? 'Rp' }}</span>
                        <input type="number" step="0.01" name="balance" value="{{ old('balance', $account->balance) }}" required
                               class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-extrabold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>

                <!-- Bank Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Banking Institution</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $account->bank_name) }}" placeholder="e.g. Morgan Stanley"
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                </div>

                <!-- Account Number -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Account Number</label>
                    <input type="text" name="account_number" value="{{ old('account_number', $account->account_number) }}" placeholder="XXXX-XXXX-XXXX"
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-mono text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                </div>

                <!-- Credit Limit (Dynamic) -->
                <div class="space-y-2 col-span-full md:col-span-1" x-show="accountType === 'credit_card'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Credit Limit Threshold</label>
                    <div class="relative">
                        <span class="currency-symbol absolute left-5 top-1/2 -translate-y-1/2 text-sm font-extrabold text-slate-400">{{ $account->setting->currency_symbol ?? 'Rp' }}</span>
                        <input type="number" step="0.01" id="credit_limit" name="credit_limit" value="{{ old('credit_limit', $account->credit_limit) }}"
                               class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-extrabold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>

                <!-- Opening Date -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Registration Date</label>
                    <input type="date" name="opening_date" value="{{ old('opening_date', $account->opening_date?->format('Y-m-d')) }}"
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>

            <!-- Notes -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Private Ledger Notes</label>
                <textarea name="notes" rows="3" placeholder="Context for this financial channel..."
                          class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-medium text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-300 focus:ring-2 focus:ring-primary-500/20">{{ old('notes', $account->notes) }}</textarea>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $account->is_active) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary-600"></div>
                    <span class="ml-3 text-sm font-bold text-slate-900">Synchronize & Monitor</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-slate-50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('accounts.index') }}" 
                   class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                    Cancel Changes
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center rounded-2xl bg-primary-600 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const settingSelect = document.getElementById('setting_id');
    const updateSymbol = () => {
        const symbol = settingSelect.options[settingSelect.selectedIndex]?.dataset?.symbol || 'Rp';
        document.querySelectorAll('.currency-symbol').forEach(el => el.textContent = symbol);
    };
    settingSelect.addEventListener('change', updateSymbol);
    updateSymbol();
</script>
@endpush
@endsection
