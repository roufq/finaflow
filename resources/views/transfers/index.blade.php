@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Inter-Account Transfers</h1>
            <p class="text-sm font-medium text-slate-500">Monitor and execute internal capital shifts across your financial ecosystem</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('transfers.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Execute Transfer
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="relative overflow-hidden rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Global Transfer Metrics (Static visualization for context) -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-primary-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Liquidity Moved</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($transfers->sum('total_amount'), 0, ',', '.') }}</h3>
            <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold tracking-tight">Across all successful shifts</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-amber-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Aggregate Fees</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">Rp {{ number_format($transfers->sum('fee'), 0, ',', '.') }}</h3>
            <p class="text-[9px] text-slate-400 mt-1 uppercase font-bold tracking-tight">Capital friction costs</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-slate-900">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Channel Efficiency</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">99.8%</h3>
            <p class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Success rate threshold</p>
        </div>
    </div>

    <!-- Transfers Ledger -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Liquidity Ledger</h2>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-primary-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Verified Transfers</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Date Execution</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Capital Corridor</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Net Amount</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Surcharge (Fee)</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Verification Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($transfers as $transfer)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-900 uppercase tracking-tight">{{ optional($transfer->transfer_date)->format('M d, Y') ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col items-end">
                                    <span class="text-[10px] font-black text-slate-900 uppercase tracking-tighter">{{ $transfer->fromAccount->name }}</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase">Origin</span>
                                </div>
                                <div class="flex items-center justify-center h-6 w-6 rounded-full bg-slate-100 text-slate-400">
                                    <i class="fas fa-arrow-right text-[8px]"></i>
                                </div>
                                <div class="flex flex-col items-start">
                                    <span class="text-[10px] font-black text-primary-600 uppercase tracking-tighter">{{ $transfer->toAccount->name }}</span>
                                    <span class="text-[8px] font-bold text-slate-400 uppercase">Target</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black text-slate-900 tracking-tight tabular-nums">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-[10px] font-bold text-rose-500 tabular-nums">Rp {{ number_format($transfer->fee, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[9px] font-black uppercase ring-1 ring-inset {{ $transfer->status === 'completed' ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : ($transfer->status === 'pending' ? 'bg-amber-50 text-amber-600 ring-amber-100' : 'bg-rose-50 text-rose-600 ring-rose-100') }}">
                                {{ $transfer->status_tags ?? $transfer->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('transfers.show', $transfer) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600" title="Transfer Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                @if($transfer->status === 'pending')
                                <a href="{{ route('transfers.edit', $transfer) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Modify Parameters">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                @endif
                                @if(in_array($transfer->status, ['pending', 'failed']))
                                <form action="{{ route('transfers.destroy', $transfer) }}" method="POST" class="inline" onsubmit="return confirm('Securely delete this transfer record?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="Purge Record">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                    <i class="fas fa-exchange-alt text-2xl"></i>
                                </div>
                                <p class="text-xs text-slate-400 font-medium italic">No internal liquidity shifts recorded.</p>
                                <a href="{{ route('transfers.create') }}" class="text-xs font-black text-primary-600 hover:underline">Execute First Transfer</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transfers->hasPages())
        <div class="p-6 border-t border-slate-50 bg-slate-50/30">
            {{ $transfers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
