@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Update Profile</h1>
            <p class="text-sm font-medium text-slate-500">Modify currency configurations and behavioral parameters for {{ $setting->tags ?: 'this profile' }}.</p>
        </div>
        <a href="{{ route('settings.index') }}" class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
        <form action="{{ route('settings.update', $setting) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Tags & Currency -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="tags" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Profile Label</label>
                    <input type="text" id="tags" name="tags" value="{{ old('tags', $setting->tags) }}" placeholder="e.g. Global Savings, Business USD" required
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                    <p class="text-[10px] text-slate-400 font-medium ml-1">Identifier for this configuration set.</p>
                </div>
                <div class="space-y-2">
                    <label for="currency_symbol" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Currency Token</label>
                    <input type="text" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $setting->currency_symbol) }}" placeholder="Rp, $, €, etc." required
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                </div>
            </div>

            <!-- Exchange Rate & Month -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="exchange_rate" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Exchange Co-efficient</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                            <i class="fas fa-calculator text-slate-300 group-focus-within:text-primary-500 transition-colors"></i>
                        </div>
                        <input type="number" step="0.000001" id="exchange_rate" name="exchange_rate" value="{{ old('exchange_rate', (float)$setting->exchange_rate) }}" required
                               class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="start_month" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Fiscal Start Month</label>
                    <div class="relative">
                        <select id="start_month" name="start_month" required
                                class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ old('start_month', $setting->start_month) == $i ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                                </option>
                            @endfor
                        </select>
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk & Credit -->
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="risk_profile" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Behavioral Risk Horizon</label>
                    <div class="relative">
                        <select id="risk_profile" name="risk_profile"
                                class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            <option value="">Not Defined</option>
                            <option value="conservative" {{ old('risk_profile', $setting->risk_profile) === 'conservative' ? 'selected' : '' }}>Conservative (Safety First)</option>
                            <option value="balanced" {{ old('risk_profile', $setting->risk_profile) === 'balanced' ? 'selected' : '' }}>Balanced (Moderate Growth)</option>
                            <option value="aggressive" {{ old('risk_profile', $setting->risk_profile) === 'aggressive' ? 'selected' : '' }}>Aggressive (Max Yield)</option>
                        </select>
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <label for="credit_score" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Credit Assessment Score</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                            <i class="fas fa-gauge-high text-slate-300 group-focus-within:text-primary-500 transition-colors"></i>
                        </div>
                        <input type="number" id="credit_score" name="credit_score" value="{{ old('credit_score', $setting->credit_score) }}" min="300" max="900" placeholder="e.g. 750"
                               class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                    </div>
                </div>
            </div>

            <!-- Default Toggle -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 ring-1 ring-slate-200">
                <div class="space-y-0.5">
                    <p class="text-xs font-black text-slate-900 uppercase tracking-widest">Global Master Default</p>
                    <p class="text-[10px] text-slate-500 font-medium">Use this configuration for all primary dashboard metrics.</p>
                </div>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" id="is_default" name="is_default" value="1" {{ old('is_default', $setting->is_default) ? 'checked' : '' }} class="peer sr-only">
                    <div class="h-6 w-11 rounded-full bg-slate-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-500/20"></div>
                </label>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('settings.index') }}" class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                    <i class="fas fa-save mr-2 text-primary-400"></i>
                    Update Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
