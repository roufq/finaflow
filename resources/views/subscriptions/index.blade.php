@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('subscriptions.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Monitor and optimize your recurring digital service expenses</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('subscriptions.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                {{ __('subscriptions.buttons.add_subscription') }}
            </a>
        </div>
    </div>

    <!-- Summary Metrics Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Active -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-primary-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('subscriptions.summary.total') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                    <i class="fas fa-sync-alt text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ $subscriptions->count() }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium text-slate-500">Active services</p>
            </div>
        </div>

        <!-- Monthly Cost -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-rose-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('subscriptions.summary.monthly_cost') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <i class="fas fa-dollar-sign text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalMonthlyCost, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Auto-deducted monthly</p>
            </div>
        </div>

        <!-- Renewal Forecast -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-amber-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('subscriptions.summary.renewing_soon') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                    <i class="fas fa-calendar-alt text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">{{ $renewingSoon->count() }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium text-slate-500">Upcoming next 7 days</p>
            </div>
        </div>

        <!-- Potential Efficiency -->
        <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">{{ __('subscriptions.summary.potential_savings') }}</span>
                <i class="fas fa-piggy-bank text-primary-400"></i>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-white tracking-tight">Rp {{ number_format($potentialSavings, 0, ',', '.') }}</h3>
                <div class="mt-2 h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                    <div class="h-full bg-primary-500" style="width: 65%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Optimization AI Insights -->
    @if($potentialSavings > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="relative overflow-hidden rounded-2xl bg-primary-50/50 border border-primary-100 p-5 flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-primary-600 shadow-sm ring-1 ring-primary-100">
                <i class="fas fa-lightbulb"></i>
            </div>
            <div>
                <h6 class="text-sm font-bold text-primary-900">{{ __('subscriptions.tips.bundle_title') }}</h6>
                <p class="text-[11px] text-primary-700 leading-relaxed mt-1">
                    {{ __('subscriptions.tips.bundle_message', ['amount' => number_format($potentialSavings, 0, ',', '.')]) }}
                </p>
            </div>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-emerald-50/50 border border-emerald-100 p-5 flex items-start gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600 shadow-sm ring-1 ring-emerald-100">
                <i class="fas fa-magic"></i>
            </div>
            <div>
                <h6 class="text-sm font-bold text-emerald-900">{{ __('subscriptions.tips.smart_title') }}</h6>
                <p class="text-[11px] text-emerald-700 leading-relaxed mt-1">
                    {{ __('subscriptions.tips.smart_message') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Subscriptions Table -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/30">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-widest">{{ __('subscriptions.table.heading') }}</h2>
            <div class="flex items-center gap-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Live Monitoring</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Service Profile</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Plan & Frequency</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Next Billing</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Renewal</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Monthly Amount</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($subscriptions as $subscription)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-white group-hover:shadow-sm transition-all border border-transparent group-hover:border-slate-100 uppercase font-black text-[10px]">
                                    {{ substr($subscription->provider, 0, 2) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $subscription->name }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-tight text-slate-400">{{ $subscription->provider }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-[10px] font-black text-slate-600 ring-1 ring-slate-200 uppercase whitespace-nowrap">
                                {{ $subscription->frequency }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700">{{ $subscription->next_billing_date->format('d M Y') }}</span>
                                @if($subscription->next_billing_date->isPast())
                                    <span class="text-[9px] font-bold text-rose-500 uppercase">Overdue</span>
                                @elseif($subscription->next_billing_date->diffInDays() <= 7)
                                    <span class="text-[9px] font-bold text-amber-500 uppercase">Due in {{ $subscription->next_billing_date->diffInDays() }} days</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($subscription->auto_renewal)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black text-emerald-600 ring-1 ring-emerald-100 uppercase">{{ __('subscriptions.badges.auto') }}</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black text-slate-500 ring-1 ring-slate-200 uppercase">{{ __('subscriptions.badges.manual') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black text-slate-900 tracking-tight">Rp {{ number_format($subscription->amount, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('subscriptions.edit', $subscription) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('subscriptions.destroy', $subscription) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('subscriptions.messages.delete_confirm') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-rose-50 hover:text-rose-600">
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
                                    <i class="fas fa-sync-alt text-2xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-base font-bold text-slate-900">{{ __('subscriptions.empty.title') }}</h4>
                                    <p class="text-xs text-slate-500 mt-1">{{ __('subscriptions.empty.description') }}</p>
                                </div>
                                <a href="{{ route('subscriptions.create') }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-8 py-2.5 text-xs font-bold text-white shadow-premium hover:bg-slate-800">
                                    <i class="fas fa-plus mr-2"></i> {{ __('subscriptions.empty.cta') }}
                                </a>
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
