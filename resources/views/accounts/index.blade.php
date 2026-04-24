@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Financial Accounts</h1>
            <p class="text-sm font-medium text-slate-500">Manage your bank accounts, digital wallets, and credit cards in one place.</p>
        </div>
        <a href="{{ route('accounts.create') }}" 
           class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
            <i class="fas fa-plus mr-2"></i>
            Add Account
        </a>
    </div>

    <!-- Summary Section -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Total Balance Card -->
        <div class="lg:col-span-2 relative overflow-hidden rounded-2xl bg-white p-8 shadow-premium ring-1 ring-slate-100 group">
            <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-5">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 shadow-sm transition-transform group-hover:scale-105">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Liquid Balance</p>
                        <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">
                            {{ $currencySymbol }} {{ number_format($totalBalance, 0, ',', '.') }}
                        </h3>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0">
                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-600 ring-1 ring-green-100">
                        <i class="fas fa-check-circle mr-1.5"></i>
                        Assets Cleared
                    </span>
                </div>
            </div>
            <!-- Background Decorative Element -->
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-primary-50/30 blur-3xl transition-opacity group-hover:opacity-100"></div>
        </div>

        <!-- Account Health Stats -->
        <div class="rounded-2xl bg-slate-900 p-8 text-white shadow-soft ring-1 ring-white/10">
            <div class="flex items-center justify-between mb-6">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Account Integrity</span>
                <i class="fas fa-shield-alt text-primary-400"></i>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-slate-400">Active Accounts</span>
                    <span class="text-sm font-bold">{{ $accounts->where('is_active', true)->count() }}</span>
                </div>
                <div class="h-1.5 rounded-full bg-white/5 overflow-hidden">
                    <div class="h-full bg-primary-500 rounded-full" style="width: {{ $accounts->count() > 0 ? ($accounts->where('is_active', true)->count() / $accounts->count()) * 100 : 0 }}%"></div>
                </div>
                <p class="text-[10px] text-slate-500 font-medium leading-relaxed">
                    All linked accounts are synchronized and monitored for unusual activity.
                </p>
            </div>
        </div>
    </div>

    <!-- Accounts Table -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Ledger Accounts</h2>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Real-time balances</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">ID</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Account Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Institution & Type</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Current Balance</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($accounts as $account)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-slate-300">#{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <a href="{{ route('accounts.show', $account) }}" class="text-sm font-bold text-slate-900 hover:text-primary-600 transition-colors">
                                    {{ $account->name }}
                                </a>
                                @if($account->account_number)
                                <span class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $account->account_number }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tight">{{ $account->bank_name ?: 'System Wallet' }}</span>
                                <span class="inline-flex w-fit items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200 uppercase">
                                    {{ $account->type_tags }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col">
                                <span class="text-sm font-extrabold tracking-tight {{ $account->balance < 0 ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ $account->setting->currency_symbol ?? $currencySymbol }} {{ number_format($account->balance, 0, ',', '.') }}
                                </span>
                                @if($account->type === 'credit_card' && $account->credit_limit)
                                <span class="text-[10px] font-bold text-slate-400">Available: {{ number_format($account->available_balance, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($account->is_active)
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-extrabold text-emerald-600 ring-1 ring-emerald-100 uppercase">
                                <span class="h-1 w-1 rounded-full bg-emerald-500"></span>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-extrabold text-slate-400 uppercase">
                                <span class="h-1 w-1 rounded-full bg-slate-400"></span>
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('accounts.show', $account) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600" title="View Details">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('accounts.edit', $account) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600" title="Edit Account">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this account?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600" title="Remove Account">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No ledger accounts registered yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
