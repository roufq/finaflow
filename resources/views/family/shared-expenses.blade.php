@extends('layouts.app')

@section('content')
<div class="space-y-10 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Shared Expenses</h1>
            <p class="text-sm font-medium text-slate-500">Coordinate and track collective liquidity obligations across household members</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('family.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Dashboard
            </a>
            <a href="{{ route('family.shared-expenses.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-plus mr-2 text-primary-400"></i>
                Log Expense
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-2xl bg-emerald-50 border border-emerald-100 p-4 flex items-center gap-4 shadow-sm">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Collaborative Metrics -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-slate-900">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-ellipsis">Active Obligations</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $expenses->count() }}</h3>
            <p class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Total tracked events</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-emerald-500">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-ellipsis">Settlement Velocity</p>
            <h3 class="text-2xl font-black text-slate-900 mt-2">{{ $expenses->where('is_settled', true)->count() }}</h3>
            <p class="text-[9px] text-emerald-500 mt-1 uppercase font-bold tracking-tight">Fully audited records</p>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 border-l-4 border-primary-500 col-span-1 md:col-span-2">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-ellipsis">Gross Collective Capital</p>
            <h3 class="text-2xl font-black text-primary-600 mt-2">Rp {{ number_format($expenses->sum('total_amount'), 0, ',', '.') }}</h3>
            <p class="text-[9px] text-slate-500 mt-1 uppercase font-bold tracking-tight">Aggregate household throughput</p>
        </div>
    </div>

    <!-- Main Ledger -->
    <div class="rounded-3xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100 border-b-4 border-slate-900">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between bg-slate-50/20">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-slate-900 flex items-center justify-center text-white">
                    <i class="fas fa-receipt text-xs"></i>
                </div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest">Shared Ledger Audit</h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-primary-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Live Financial Sync</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Expense Identifier</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Corridor</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aggregate</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Unit Context</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($expenses as $expense)
                    <tr class="group transition-colors hover:bg-slate-50/50" x-data="{ open: false }">
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900">{{ $expense->expense_name }}</span>
                                <span class="text-[9px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">{{ $expense->expense_date->format('M d, Y') }} execution window</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-3 py-1 text-[9px] font-black uppercase text-slate-500 ring-1 ring-slate-200">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right font-black text-slate-900 tabular-nums">
                            Rp {{ number_format($expense->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="px-8 py-6 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-xs font-black text-slate-700">{{ $expense->participant_count }}</span>
                                <p class="text-[8px] font-bold text-slate-400 uppercase">Participants</p>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-[9px] font-black uppercase ring-1 ring-inset {{ $expense->is_settled ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-amber-50 text-amber-600 ring-amber-100' }}">
                                {{ $expense->is_settled ? 'Settled' : 'Pending' }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button type="button" @click="$dispatch('open-modal', {id: 'details-{{ $expense->id }}'})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 hover:text-primary-600 hover:ring-primary-500/30 transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                @if(!$expense->is_settled)
                                <button type="button" @click="$dispatch('open-modal', {id: 'settle-{{ $expense->id }}'})" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 hover:text-emerald-600 hover:ring-emerald-500/30 transition-all">
                                    <i class="fas fa-check-double text-xs"></i>
                                </button>
                                @endif
                                <a href="{{ route('family.shared-expenses.edit', $expense) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-slate-400 shadow-sm ring-1 ring-slate-200 hover:text-amber-600 hover:ring-amber-500/30 transition-all">
                                    <i class="fas fa-pen-fancy text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="h-16 w-16 rounded-[2rem] bg-slate-50 flex items-center justify-center text-slate-200">
                                    <i class="fas fa-receipt text-2xl"></i>
                                </div>
                                <p class="text-xs font-bold text-slate-400 italic">No collaborative obligations found in history.</p>
                                <a href="{{ route('family.shared-expenses.create') }}" class="text-xs font-black text-primary-600 uppercase hover:underline">Log First Shared Event</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Dynamic Modals Container -->
<div x-data="{ activeModal: null }" 
     @open-modal.window="activeModal = $event.detail.id"
     @close-modal.window="activeModal = null"
     class="fixed inset-0 z-[60] flex items-center justify-center p-4 pointer-events-none"
     :class="{ 'pointer-events-auto': activeModal }">
    
    <!-- Modal Backdrop -->
    <div x-show="activeModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm pointer-events-auto"
         @click="activeModal = null"></div>

    @foreach($expenses as $expense)
    <!-- Details Modal: {{ $expense->id }} -->
    <div x-show="activeModal === 'details-{{ $expense->id }}'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-2xl bg-white rounded-[2.5rem] shadow-premium ring-1 ring-slate-100 overflow-hidden pointer-events-auto">
        
        <div class="p-8 space-y-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-primary-50 text-primary-600 flex items-center justify-center">
                        <i class="fas fa-file-invoice text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900">{{ $expense->expense_name }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Audit Context</p>
                    </div>
                </div>
                <button @click="activeModal = null" class="h-8 w-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-900">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-6">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Execution Metrics</p>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-500">Gross Principal</span>
                                <span class="text-xs font-black text-slate-900">Rp {{ number_format($expense->total_amount, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-500">Split Method</span>
                                <span class="text-xs font-black text-primary-600 uppercase tracking-tighter">{{ $expense->split_method }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs font-bold text-slate-500">Execution Date</span>
                                <span class="text-xs font-black text-slate-900">{{ $expense->expense_date->format('d F, Y') }}</span>
                            </div>
                        </div>
                    </div>
                    @if($expense->description)
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Contextual Description</p>
                        <p class="text-xs text-slate-600 italic leading-relaxed">{{ $expense->description }}</p>
                    </div>
                    @endif
                </div>

                <div class="rounded-2xl bg-slate-50 p-6">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Unit Participants</p>
                    <div class="space-y-4">
                        @foreach($expense->participant_details ?? [] as $participant)
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-900 leading-none">{{ $participant['name'] }}</span>
                                <span class="text-[8px] font-bold text-slate-400 uppercase mt-0.5">{{ $participant['relationship'] }}</span>
                            </div>
                            <span class="text-[11px] font-black text-emerald-600">Rp {{ number_format($participant['share'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button @click="activeModal = null" class="px-8 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-widest shadow-premium hover:bg-slate-800">Dismiss View</button>
            </div>
        </div>
    </div>

    <!-- Settle Modal: {{ $expense->id }} -->
    <div x-show="activeModal === 'settle-{{ $expense->id }}'" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="relative w-full max-w-md bg-white rounded-[2.5rem] shadow-premium ring-1 ring-slate-100 overflow-hidden pointer-events-auto">
        <form method="POST" action="{{ route('family.shared-expenses.settle') }}" class="p-8 space-y-8">
            @csrf
            @method('PATCH')
            <input type="hidden" name="expense_id" value="{{ $expense->id }}">
            
            <div class="flex flex-col items-center text-center gap-4">
                <div class="h-16 w-16 rounded-[2rem] bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter">Settlement Verification</h3>
                    <p class="text-xs text-slate-400 mt-1">Audit the collective fund collection for <span class="text-slate-900 font-bold font-italic">{{ $expense->expense_name }}</span></p>
                </div>
            </div>

            <div class="space-y-2">
                <label for="settlement_date{{ $expense->id }}" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-1">Settlement Date</label>
                <input type="date" id="settlement_date{{ $expense->id }}" name="settlement_date" value="{{ date('Y-m-d') }}" required
                       class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="activeModal = null" class="flex-1 px-5 py-3.5 rounded-2xl bg-white text-slate-500 text-xs font-black uppercase tracking-widest hover:text-slate-900 transition-colors">Abort</button>
                <button type="submit" class="flex-1 px-5 py-3.5 rounded-2xl bg-emerald-500 text-white text-xs font-black uppercase tracking-widest shadow-premium hover:bg-emerald-600 active:scale-95 transition-all">Verify & Settle</button>
            </div>
        </form>
    </div>
    @endforeach
</div>

@endsection

