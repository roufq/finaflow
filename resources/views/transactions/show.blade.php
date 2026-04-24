@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('transactions.index') }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 transition-all hover:bg-slate-50 hover:text-slate-600">
                <i class="fas fa-chevron-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Transaction Details</h1>
                <p class="text-xs font-medium text-slate-500">Record ID: #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transactions.edit', $transaction) }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-amber-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-amber-50">
                <i class="fas fa-edit mr-2 text-[10px]"></i> Edit
            </a>
            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" class="inline" onsubmit="return confirm('Permanently remove this record?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white text-rose-500 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-rose-50">
                    <i class="fas fa-trash text-xs"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Receipt Card -->
    <div class="relative overflow-hidden rounded-3xl bg-white shadow-premium ring-1 ring-slate-100">
        <!-- Top Accent Bar -->
        <div class="h-2 w-full {{ $transaction->type == 'income' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
        
        <div class="p-8 md:p-12">
            <!-- Amount Section -->
            <div class="text-center space-y-2 mb-12">
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-extrabold uppercase {{ $transaction->type === 'income' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    <i class="fas {{ $transaction->type === 'income' ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ $transaction->type }}
                </span>
                <h2 class="text-5xl font-extrabold tracking-tighter text-slate-900">
                    {{ $transaction->type == 'expense' ? '-' : '+' }}{{ $transaction->account?->setting->currency_symbol ?? 'Rp' }} {{ number_format($transaction->amount, 0, ',', '.') }}
                </h2>
                <p class="text-sm font-bold text-slate-400 capitalize">{{ $transaction->category->name }}</p>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8 border-t border-slate-50 pt-10">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Date of Record</p>
                    <p class="text-base font-bold text-slate-900">{{ $transaction->transaction_date->format('l, M d, Y') }}</p>
                </div>
                
                <div class="space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Source Account</p>
                    <div class="flex items-center gap-2">
                        <div class="h-2 w-2 rounded-full bg-primary-500"></div>
                        <p class="text-base font-bold text-slate-900">{{ $transaction->account->name ?? 'System Ledger' }}</p>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-1">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Transaction Notes</p>
                    <div class="rounded-2xl bg-slate-50 p-5 ring-1 ring-slate-100">
                        <p class="text-sm font-medium text-slate-600 leading-relaxed italic">
                            {{ $transaction->description ?: 'No additional notes provided for this transaction.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="mt-12 pt-8 border-t border-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-50 text-slate-400 ring-1 ring-slate-100">
                        <i class="fas fa-fingerprint"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase leading-none">Security Key</p>
                        <p class="text-[10px] font-mono text-slate-300 mt-1">{{ hash('sha256', $transaction->id . $transaction->created_at) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase">
                    <i class="fas fa-clock"></i>
                    Created {{ $transaction->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Help Box -->
    <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft ring-1 ring-white/10 flex items-center gap-6">
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-primary-400">
            <i class="fas fa-info-circle text-xl"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold">Audit History</h4>
            <p class="text-xs text-slate-400 mt-1">This transaction is immutable once verified by the system. Any changes are logged in the activity trail for compliance.</p>
        </div>
    </div>
</div>
@endsection
