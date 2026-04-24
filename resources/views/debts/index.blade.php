@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Liabilities & Debts</h1>
            <p class="text-sm font-medium text-slate-500">Monitor and strategist your path to financial freedom</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('debts.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Record New Debt
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

    <!-- Debt Summary Bar -->
    @if($debts->count() > 0)
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-amber-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Active Obligations</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $debts->where('status', 'active')->count() }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Currently Servicing</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-emerald-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Paid In Full</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $debts->where('status', 'paid_off')->count() }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Financial Milestones</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-rose-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Alerts / Defaulted</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $debts->where('status', 'defaulted')->count() }}</h3>
            <p class="text-[10px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Attention Required</p>
        </div>
        <div class="rounded-2xl bg-slate-900 p-6 shadow-soft ring-1 ring-white/10">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Total Outstanding</p>
            <h3 class="text-2xl font-black text-white mt-2">Rp {{ number_format($debts->where('status', 'active')->sum('current_balance'), 0, ',', '.') }}</h3>
            <div class="mt-2 h-1 w-full bg-white/10 rounded-full overflow-hidden">
                <div class="h-full bg-primary-500" style="width: 75%"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Debts Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse($debts as $debt)
        <div class="group relative flex flex-col overflow-hidden rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100 transition-all hover:shadow-soft">
            <!-- Status Badge -->
            <div class="flex items-start justify-between mb-8">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $debt->status == 'active' ? 'bg-amber-50 text-amber-600' : ($debt->status == 'paid_off' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600') }} transition-transform group-hover:scale-110">
                    <i class="fas fa-hand-holding-usd text-xl"></i>
                </div>
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-black uppercase ring-1 ring-inset {{ $debt->status == 'active' ? 'bg-amber-50 text-amber-600 ring-amber-100' : ($debt->status == 'paid_off' ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-rose-50 text-rose-600 ring-rose-100') }}">
                    {{ str_replace('_', ' ', $debt->status) }}
                </span>
            </div>

            <!-- Content -->
            <div class="flex flex-col flex-1">
                <h4 class="text-xl font-black text-slate-900 tracking-tight mb-2 truncate group-hover:text-primary-600 transition-colors">
                    {{ $debt->name }}
                </h4>
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-[10px] font-black uppercase tracking-[0.1em] text-slate-400">{{ $debt->lender }}</span>
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    <span class="text-[10px] font-black uppercase tracking-[0.1em] text-primary-500">{{ $debt->interest_rate }}% APR</span>
                </div>

                <div class="space-y-6">
                    <div>
                        <div class="flex items-end justify-between mb-1.5">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Balance Remaining</span>
                            <span class="text-lg font-black text-slate-900 tabular-nums">Rp {{ number_format($debt->current_balance, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden ring-1 ring-slate-200/50">
                            <div class="h-full {{ $debt->status == 'active' ? 'bg-amber-500' : ($debt->status == 'paid_off' ? 'bg-emerald-500' : 'bg-rose-500') }} rounded-full transition-all duration-1000 ease-out" 
                                 style="width: {{ $debt->payoff_progress }}%"></div>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ number_format($debt->payoff_progress, 1) }}% Liquidated</span>
                            <i class="fas fa-chart-line text-[10px] text-slate-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex items-center gap-2">
                <a href="{{ route('debts.show', $debt) }}" class="flex-1 inline-flex items-center justify-center rounded-xl bg-slate-50 py-3 text-xs font-bold text-slate-900 ring-1 ring-slate-200 transition-all hover:bg-slate-100 hover:text-slate-900">
                    Audit Payments
                </a>
                <a href="{{ route('debts.edit', $debt) }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-400 hover:text-amber-600 hover:border-amber-200 transition-all">
                    <i class="fas fa-edit text-xs"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 bg-white rounded-3xl shadow-premium border border-dashed border-slate-200 flex flex-col items-center justify-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-50 text-slate-200 mb-6">
                <i class="fas fa-credit-card text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Debts Tracked</h3>
            <p class="text-sm text-slate-500 mb-8 max-w-sm text-center">Start recording your liabilities to strategically manage repayments and interest costs.</p>
            <a href="{{ route('debts.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-primary-600 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-primary-500">
                Record First Debt
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
