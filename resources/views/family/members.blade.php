@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-10 animate-fade-in pb-20">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 line-clamp-1">Household Registry</h1>
            <p class="text-sm font-medium text-slate-500">Manage unit participants and their individual balanced liquidity</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('family.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-th-large mr-2 text-slate-400"></i>
                Dashboard
            </a>
            <a href="{{ route('family.members.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-plus mr-2 text-white/50"></i>
                Incorporate Member
            </a>
        </div>
    </div>

    @if($members->count() > 0)
    <!-- Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 flex items-center gap-4 transition-all hover:ring-primary-500/30 group">
            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 transition-colors group-hover:bg-primary-500 group-hover:text-white">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Aggregate Unit</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-black text-slate-900 leading-none">{{ $members->count() }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Operatives</span>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 flex items-center gap-4 transition-all hover:ring-green-500/30 group border-b-4 border-green-500">
            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 transition-colors group-hover:bg-green-500 group-hover:text-white">
                <i class="fas fa-user-check text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Active Status</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-xl font-black text-slate-900 leading-none">{{ $members->where('is_active', true)->count() }}</span>
                    <span class="text-[9px] font-bold text-slate-400 uppercase">Verified</span>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-premium ring-1 ring-slate-100 flex items-center gap-4 transition-all hover:ring-blue-500/30 group border-b-4 border-blue-500">
            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 transition-colors group-hover:bg-blue-500 group-hover:text-white">
                <i class="fas fa-money-bill-wave text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Monthly Budget</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-sm font-black text-slate-400 uppercase leading-none">Rp</span>
                    <span class="text-xl font-black text-slate-900 leading-none">{{ number_format($members->sum('monthly_allowance'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-slate-900 p-6 shadow-premium ring-1 ring-white/10 flex items-center gap-4 group">
            <div class="h-12 w-12 rounded-2xl bg-white/10 flex items-center justify-center text-white/40 group-hover:text-primary-400">
                <i class="fas fa-wallet text-lg"></i>
            </div>
            <div>
                <p class="text-[10px] font-black text-white/40 uppercase tracking-widest leading-none mb-1 text-primary-400">Net Liquidity</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-sm font-black text-white/40 uppercase leading-none italic">Rp</span>
                    <span class="text-xl font-black text-white leading-none tabular-nums">{{ number_format($members->sum('current_balance'), 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Registry Table -->
    <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-50 bg-slate-50/30 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-slate-900 flex items-center justify-center text-white shadow-lg shadow-slate-900/20">
                    <i class="fas fa-id-card text-xs"></i>
                </div>
                <h2 class="text-xs font-black text-slate-900 uppercase tracking-widest italic">Unit Participant Registry</h2>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <span class="h-1.5 w-1.5 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Database Synchronized</span>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-slate-50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Identified Operative</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest italic">Nexus / Age</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Allocated Budget</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-right">Current Ledger</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-center">Protocol</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest italic text-right">Authorization</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($members as $member)
                    <tr class="group hover:bg-slate-50/50 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 shrink-0 rounded-2xl bg-slate-100 flex items-center justify-center ring-4 ring-white shadow-premium transition-transform group-hover:scale-105 group-hover:rotate-3 overflow-hidden border border-slate-100">
                                    <span class="text-lg font-black text-slate-400 uppercase italic font-serif leading-none">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-900 italic tracking-tight">{{ $member->name }}</span>
                                    @if($member->date_of_birth)
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $member->date_of_birth->format('d M Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex flex-col gap-1">
                                <span class="inline-flex self-start rounded-md bg-slate-50 px-2 py-0.5 text-[9px] font-black text-slate-600 uppercase tracking-widest ring-1 ring-inset ring-slate-200 group-hover:bg-white">{{ $member->relationship }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                    @if($member->date_of_birth) {{ $member->age }} Cycles Old @else Term Undefined @endif
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            @if($member->monthly_allowance > 0)
                                <span class="text-xs font-black text-slate-900 tabular-nums italic tracking-tighter">Rp {{ number_format($member->monthly_allowance, 0, ',', '.') }}</span>
                            @else
                                <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-6 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-black tabular-nums {{ $member->current_balance >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                    Rp {{ number_format($member->current_balance, 0, ',', '.') }}
                                </span>
                                @if($member->current_balance < 0)
                                    <span class="text-[7px] font-black text-red-400 uppercase tracking-tighter">Liquidity Deficit</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-6 text-center">
                            @if($member->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-[9px] font-black text-green-600 uppercase tracking-widest ring-1 ring-inset ring-green-600/20">Operational</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[9px] font-black text-slate-400 uppercase tracking-widest ring-1 ring-inset ring-slate-200 italic">Disengaged</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('family.members.edit', $member) }}" 
                                   class="h-8 w-8 rounded-lg bg-white flex items-center justify-center text-slate-400 ring-1 ring-slate-200 shadow-sm transition-all hover:bg-primary-500 hover:text-white hover:ring-primary-500 active:scale-90">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                <form method="POST" action="{{ route('family.members.update', $member) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_active" value="{{ $member->is_active ? '0' : '1' }}">
                                    <button type="submit" 
                                            class="h-8 w-8 rounded-lg bg-white flex items-center justify-center transition-all ring-1 ring-slate-200 shadow-sm active:scale-90 {{ $member->is_active ? 'text-orange-400 hover:bg-orange-500 hover:text-white hover:ring-orange-500' : 'text-green-500 hover:bg-green-500 hover:text-white hover:ring-green-500' }}"
                                            onclick="return confirm('Recalibrate operational status for {{ $member->name }}?')">
                                        <i class="fas fa-{{ $member->is_active ? 'ban' : 'check' }} text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 p-20 flex flex-col items-center text-center space-y-6">
        <div class="h-24 w-24 rounded-[2.5rem] bg-slate-50 flex items-center justify-center text-slate-200 border-4 border-white shadow-premium animate-bounce-slow">
            <i class="fas fa-users text-4xl"></i>
        </div>
        <div class="space-y-2">
            <h3 class="text-xl font-black text-slate-900 italic tracking-tight uppercase">Operational Void Detected</h3>
            <p class="text-sm text-slate-400 font-medium max-w-sm mx-auto">The household registry is currently vacant. Initialize unit participants to enable collaborative financial auditing.</p>
        </div>
        <a href="{{ route('family.members.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-primary-600 px-8 py-4 text-sm font-black uppercase tracking-widest text-white shadow-premium transition-all hover:bg-primary-700 active:scale-95">
            Initialize First Member
        </a>
    </div>
    @endif
</div>
@endsection
