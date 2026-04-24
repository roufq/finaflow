@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Asset Management</h1>
            <p class="text-sm font-medium text-slate-500">Inventory and valuation of your physical and digital holdings</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('assets.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Add Asset
            </a>
        </div>
    </div>

    <!-- Smart Alerts -->
    @if($expiredInsurance->count() > 0 || $expiringInsurance->count() > 0)
    <div class="space-y-3">
        @foreach($expiredInsurance as $asset)
        <div class="relative overflow-hidden rounded-2xl bg-rose-50 border border-rose-100 p-4 shadow-sm flex items-center gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-rose-500 shadow-sm ring-1 ring-rose-100">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="flex-1">
                <h6 class="text-sm font-bold text-rose-900">Insurance Expired: {{ $asset->name }}</h6>
                <p class="text-[11px] text-rose-700 opacity-80">This asset is currently unprotected. Please renew as soon as possible.</p>
            </div>
            <a href="{{ route('assets.edit', $asset) }}" class="text-xs font-bold text-rose-600 hover:underline">Renew Now</a>
        </div>
        @endforeach
        
        @foreach($expiringInsurance as $asset)
        <div class="relative overflow-hidden rounded-2xl bg-amber-50 border border-amber-100 p-4 shadow-sm flex items-center gap-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-500 shadow-sm ring-1 ring-amber-100">
                <i class="fas fa-clock"></i>
            </div>
            <div class="flex-1">
                <h6 class="text-sm font-bold text-amber-900">Expiring Soon: {{ $asset->name }}</h6>
                <p class="text-[11px] text-amber-700 opacity-80">Insurance expires on {{ $asset->insurance_expiry->format('M d, Y') }}.</p>
            </div>
            <a href="{{ route('assets.edit', $asset) }}" class="text-xs font-bold text-amber-600 hover:underline">Manage</a>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Value -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-primary-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Asset Value</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600">
                    <i class="fas fa-home text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalValue, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Acquisition cost total</p>
            </div>
        </div>

        <!-- Depreciation -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-rose-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Depreciation</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                    <i class="fas fa-chart-line text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Accumulated over time</p>
            </div>
        </div>

        <!-- Annual Income -->
        <div class="rounded-2xl bg-white p-6 shadow-premium transition-all hover:shadow-soft border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Yield / Annual Income</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600">
                    <i class="fas fa-coins text-sm"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold text-slate-900">Rp {{ number_format($totalIncome, 0, ',', '.') }}</h3>
                <p class="mt-1 text-xs text-slate-400 font-medium">Passive generating power</p>
            </div>
        </div>

        <!-- Count -->
        <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-soft">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Inventory</span>
                <i class="fas fa-list text-primary-400"></i>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-bold">{{ $assets->count() }} <span class="text-xs font-medium text-slate-400">Assets tracked</span></h3>
                <div class="mt-2 h-1.5 w-full rounded-full bg-white/10 overflow-hidden">
                    <div class="h-full bg-primary-500" style="width: {{ min($assets->count() * 10, 100) }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assets List -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Asset Ledger</h2>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Inventory Management</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Asset Info</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Valuation</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Depreciated</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Yield</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold uppercase tracking-widest text-slate-400">Protection</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($assets as $asset)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-50 text-slate-400 group-hover:bg-white group-hover:shadow-sm transition-all border border-transparent group-hover:border-slate-100">
                                    <i class="fas {{ $asset->type == 'property' ? 'fa-home' : ($asset->type == 'vehicle' ? 'fa-car' : 'fa-box') }} text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $asset->name }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-tight text-slate-400">{{ ucfirst(str_replace('_', ' ', $asset->type)) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-extrabold text-slate-900 tracking-tight">Rp {{ number_format($asset->current_value, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-400 tracking-tight">Rp {{ number_format($asset->depreciated_value, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-emerald-600 tracking-tight">+Rp {{ number_format($asset->monthly_income, 0, ',', '.') }} <span class="text-[10px] text-slate-400">/mo</span></span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($asset->insurance_expiry)
                                @if($asset->is_insurance_expired)
                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-black text-rose-600 ring-1 ring-rose-100 uppercase">Expired</span>
                                @elseif($asset->insurance_expires_soon)
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-black text-amber-600 ring-1 ring-amber-100 uppercase">Warning</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-black text-emerald-600 ring-1 ring-emerald-100 uppercase">Protected</span>
                                @endif
                            @else
                                <span class="text-[10px] font-bold text-slate-300">Uninsured</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('assets.show', $asset) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('assets.edit', $asset) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                    <i class="fas fa-layer-group text-xl"></i>
                                </div>
                                <p class="text-sm font-medium text-slate-400 italic">Add your first asset to track valuation over time.</p>
                                <a href="{{ route('assets.create') }}" class="text-xs font-bold text-primary-600 hover:underline">Get Started</a>
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
