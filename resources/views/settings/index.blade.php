@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Financial Settings</h1>
            <p class="text-sm font-medium text-slate-500">Manage multi-currency configurations, risk profiles, and operational parameters.</p>
        </div>
        <a href="{{ route('settings.create') }}" 
           class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
            <i class="fas fa-plus mr-2"></i>
            New Configuration
        </a>
    </div>

    <!-- Settings Table -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Multi-Currency Profiles</h2>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">{{ $settings->count() }} Profiles</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Profile Name</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Currency</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Exchange Rate</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Credit Score</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Risk Profile</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($settings as $setting)
                    <tr class="group transition-colors hover:bg-slate-50/50 {{ $setting->is_default ? 'bg-primary-50/30' : '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-slate-900">{{ $setting->tags ?? 'Base Profile' }}</span>
                                @if($setting->is_default)
                                    <span class="inline-flex items-center rounded-full bg-primary-100 px-2 py-0.5 text-[8px] font-extrabold text-primary-700 uppercase">Default</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200">
                                {{ $setting->currency_symbol }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-slate-900">
                                {{ number_format($setting->exchange_rate, floor($setting->exchange_rate) == $setting->exchange_rate ? 0 : 4) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium text-slate-500">
                            {{ $setting->credit_score ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($setting->risk_profile)
                                @php
                                    $riskColors = [
                                        'aggressive' => 'bg-rose-50 text-rose-600 ring-rose-100',
                                        'balanced' => 'bg-amber-50 text-amber-600 ring-amber-100',
                                        'conservative' => 'bg-emerald-50 text-emerald-600 ring-emerald-100'
                                    ];
                                    $color = $riskColors[strtolower($setting->risk_profile)] ?? 'bg-slate-50 text-slate-600 ring-slate-100';
                                @endphp
                                <span class="inline-flex items-center rounded-full {{ $color }} px-2.5 py-0.5 text-[10px] font-extrabold uppercase ring-1">
                                    {{ $setting->risk_profile }}
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 uppercase">Not Defined</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('settings.show', $setting) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('settings.edit', $setting) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('settings.destroy', $setting) }}" method="POST" class="inline" onsubmit="return confirm('Remove this configuration profile?')">
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
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">No settings profiles found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
