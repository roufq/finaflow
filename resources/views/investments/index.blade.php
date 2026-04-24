@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Portfolio Alpha</h1>
            <p class="text-sm font-medium text-slate-500">Professional-grade oversight of your capital allocations and performance</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('investments.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-briefcase mr-2"></i>
                Add Holding
            </a>
        </div>
    </div>

    <!-- Master Metrics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Net Value -->
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-slate-900">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Asset Value</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400">
                    <i class="fas fa-wallet text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-900">Rp {{ number_format($totalValue, 0, ',', '.') }}</h3>
                <p class="mt-1 text-[10px] text-slate-400 font-bold uppercase">Aggregated Market Value</p>
            </div>
        </div>

        <!-- Profit/Loss -->
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 {{ $totalGainLoss >= 0 ? 'border-emerald-500' : 'border-rose-500' }}">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Unrealized P/L</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg {{ $totalGainLoss >= 0 ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black {{ $totalGainLoss >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                    Rp {{ number_format($totalGainLoss, 0, ',', '.') }}
                </h3>
                <p class="mt-1 text-[10px] text-slate-400 font-bold uppercase">Since Acquisition</p>
            </div>
        </div>

        <!-- Yield -->
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Dividends</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                    <i class="fas fa-coins text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-900">Rp {{ number_format($totalDividends, 0, ',', '.') }}</h3>
                <p class="mt-1 text-[10px] text-slate-400 font-bold uppercase">Passive Yield Capture</p>
            </div>
        </div>

        <!-- Transaction Friction -->
        <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-widest text-white/40">Aggregated Fees</span>
                <i class="fas fa-receipt text-primary-400"></i>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black">Rp {{ number_format($totalFees, 0, ',', '.') }}</h3>
                <div class="mt-2 h-1 w-full bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-primary-500" style="width: 30%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Holdings Ledger -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">Active Holdings</h2>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Live Valuation</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Security / Symbol</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Type</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Position</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Market Value</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Performance</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($investments as $investment)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-white font-black text-[10px] shadow-soft uppercase">
                                    {{ substr($investment->symbol, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 leading-none">{{ $investment->name }}</p>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">{{ $investment->symbol }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-500 uppercase tracking-tight">
                                {{ str_replace('_', ' ', $investment->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <p class="text-xs font-bold text-slate-700 tabular-nums">{{ number_format($investment->quantity, 4) }}</p>
                            <p class="text-[9px] font-medium text-slate-400 mt-0.5">@ Rp {{ number_format($investment->current_price, 2, ',', '.') }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black text-slate-900 tracking-tight tabular-nums">Rp {{ number_format($investment->current_value, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-bold {{ $investment->unrealized_gain_loss >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $investment->unrealized_gain_loss >= 0 ? '+' : '' }}Rp {{ number_format($investment->unrealized_gain_loss, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-black {{ $investment->roi_percentage >= 0 ? 'text-emerald-500' : 'text-rose-500' }} uppercase">
                                    {{ $investment->roi_percentage >= 0 ? '▲' : '▼' }} {{ number_format(abs($investment->roi_percentage), 2) }}% ROI
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('investments.show', $investment) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600 transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('investments.edit', $investment) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600 transition-all">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('investments.destroy', $investment) }}" method="POST" class="inline" onsubmit="return confirm('Liquisate and delete this holding from records?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600 transition-all">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                    <i class="fas fa-chart-pie text-2xl"></i>
                                </div>
                                <p class="text-xs text-slate-400 font-medium italic">Your investment portfolio is currently empty.</p>
                                <a href="{{ route('investments.create') }}" class="text-xs font-bold text-primary-600 hover:underline">Start Investing</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
