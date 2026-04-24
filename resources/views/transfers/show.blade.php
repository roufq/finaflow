@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('transfers.index') }}" class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-slate-400 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50 hover:text-slate-900">
                <i class="fas fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Transfer Details</h1>
                <p class="text-sm font-medium text-slate-500">Audit trail for transaction reference: <span class="font-bold text-slate-700">{{ $transfer->reference_number ?: 'N/A' }}</span></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if($transfer->status === 'pending')
            <a href="{{ route('transfers.edit', $transfer) }}" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-amber-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-amber-50">
                <i class="fas fa-edit mr-2"></i>
                Modify
            </a>
            @endif
            <button onclick="window.print()" class="inline-flex items-center justify-center rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-print mr-2 text-slate-400"></i>
                Print Audit
            </button>
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

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Execution corridor Visualization -->
        <div class="lg:col-span-2 space-y-8">
            <div class="rounded-3xl bg-white p-10 shadow-premium ring-1 ring-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8">
                    <span class="inline-flex items-center rounded-full px-4 py-1.5 text-xs font-black uppercase ring-1 ring-inset {{ $transfer->status === 'completed' ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : ($transfer->status === 'pending' ? 'bg-amber-50 text-amber-600 ring-amber-100' : 'bg-rose-50 text-rose-600 ring-rose-100') }}">
                        {{ $transfer->status }}
                    </span>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-12 relative z-10">
                    <!-- From Account -->
                    <div class="flex flex-col items-center text-center gap-4 group">
                        <div class="flex h-20 w-20 items-center justify-center rounded-[2.5rem] bg-slate-50 text-slate-400 ring-8 ring-slate-100/50 shadow-inner-soft transition-transform group-hover:scale-110">
                            <i class="fas fa-university text-3xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Origin Entity</p>
                            <h4 class="text-lg font-black text-slate-900 leading-tight">{{ $transfer->fromAccount->name }}</h4>
                            <p class="text-xs font-bold text-slate-400 mt-1">Balance: Rp {{ number_format($transfer->fromAccount->balance, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Flow Indicator -->
                    <div class="flex-1 flex flex-col items-center gap-4">
                        <div class="w-full h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-soft ring-1 ring-slate-100 text-primary-500">
                                    <i class="fas fa-chevron-right text-sm"></i>
                                </div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</h3>
                            @if($transfer->fee > 0)
                                <p class="text-[10px] font-bold text-rose-400 uppercase tracking-widest mt-1">+ Rp {{ number_format($transfer->fee, 0, ',', '.') }} Surcharge</p>
                            @endif
                        </div>
                    </div>

                    <!-- To Account -->
                    <div class="flex flex-col items-center text-center gap-4 group">
                        <div class="flex h-20 w-20 items-center justify-center rounded-[2.5rem] bg-primary-50 text-primary-500 ring-8 ring-primary-100/50 shadow-inner-soft transition-transform group-hover:scale-110">
                            <i class="fas fa-receipt text-3xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest mb-1">Target Entity</p>
                            <h4 class="text-lg font-black text-slate-900 leading-tight">{{ $transfer->toAccount->name }}</h4>
                            <p class="text-xs font-bold text-slate-400 mt-1">Balance: Rp {{ number_format($transfer->toAccount->balance, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Audit Ledger -->
            <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden border-b-4 border-slate-900">
                <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest">Transaction Metadata</h3>
                    <i class="fas fa-fingerprint text-slate-200"></i>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Execution Timestamp</p>
                        <p class="text-sm font-black text-slate-900">{{ optional($transfer->transfer_date)->format('d F Y') ?? 'N/A' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Audit Reference</p>
                        <p class="text-sm font-black text-slate-900">{{ $transfer->reference_number ?: 'No reference provided' }}</p>
                    </div>
                    <div class="space-y-1 md:col-span-2">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Operational Context</p>
                        <p class="text-sm font-medium text-slate-600 italic leading-relaxed">{{ $transfer->description ?: 'No additional context provided for this movement.' }}</p>
                    </div>
                    @if($transfer->status === 'completed')
                    <div class="pt-4 border-t border-slate-50 md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Processed At</p>
                            <p class="text-xs font-bold text-slate-500">{{ $transfer->processed_at ? $transfer->processed_at->format('d M Y, H:i:s') : 'Batch processed' }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Record Created</p>
                            <p class="text-xs font-bold text-slate-500">{{ $transfer->created_at->format('d M Y, H:i:s') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Financial Summary Sidebar -->
        <div class="space-y-8">
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-soft ring-1 ring-white/10 relative overflow-hidden">
                <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-primary-500/10 blur-3xl"></div>
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 relative z-10">Capital Manifest</h3>
                
                <div class="space-y-6 relative z-10">
                    <div class="space-y-1">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Principal Amount</p>
                        <p class="text-xl font-black text-white tracking-tight">Rp {{ number_format($transfer->amount, 0, ',', '.') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Friction/Fees</p>
                        <p class="text-sm font-bold text-rose-400">Rp {{ number_format($transfer->fee, 0, ',', '.') }}</p>
                    </div>
                    <div class="pt-6 border-t border-white/10">
                        <p class="text-[10px] font-black text-primary-400 uppercase tracking-[0.2em] mb-1">Aggregate Debit</p>
                        <p class="text-3xl font-black text-white tracking-tighter">Rp {{ number_format($transfer->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            @if($transfer->status === 'pending')
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100 space-y-4">
                <p class="text-xs font-bold text-slate-900 leading-tight">Administrative Control</p>
                <p class="text-[10px] text-slate-400">This record is currently pending and can be modified or purged from history.</p>
                <div class="flex flex-col gap-2 pt-2">
                    <form action="{{ route('transfers.destroy', $transfer) }}" method="POST" onsubmit="return confirm('Purge this record permanently?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-xl bg-rose-50 px-4 py-3 text-xs font-black text-rose-600 transition-all hover:bg-rose-100">
                            <i class="fas fa-trash mr-2"></i>
                            Permanently Delete
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

