@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Define Aspiration</h1>
            <p class="text-sm font-medium text-slate-500">Establish a new target milestone for your long-term wealth strategy.</p>
        </div>
        <a href="{{ route('goals.index') }}" class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Main Form Area -->
        <div class="lg:col-span-8">
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <form action="{{ route('goals.store') }}" method="POST" class="space-y-8">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="name" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Aspiration Label <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Dream Villa in Bali, Retirement Fund" required
                               class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('name') ring-rose-500 @enderror">
                        @error('name') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Category & Type -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="category" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Thematic Focus <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="category" name="category" required
                                        class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10 @error('category') ring-rose-500 @enderror">
                                    <option value="">Select Category...</option>
                                    <option value="emergency_fund" {{ old('category') == 'emergency_fund' ? 'selected' : '' }}>Emergency Reserve</option>
                                    <option value="vacation" {{ old('category') == 'vacation' ? 'selected' : '' }}>Leisure & Travel</option>
                                    <option value="house_down_payment" {{ old('category') == 'house_down_payment' ? 'selected' : '' }}>Real Estate Equity</option>
                                    <option value="car_purchase" {{ old('category') == 'car_purchase' ? 'selected' : '' }}>Automotive Purchase</option>
                                    <option value="education" {{ old('category') == 'education' ? 'selected' : '' }}>Academic Growth</option>
                                    <option value="retirement" {{ old('category') == 'retirement' ? 'selected' : '' }}>Retirement Security</option>
                                    <option value="investment" {{ old('category') == 'investment' ? 'selected' : '' }}>Capital Reinvestment</option>
                                    <option value="debt_payoff" {{ old('category') == 'debt_payoff' ? 'selected' : '' }}>Liability Liquidation</option>
                                    <option value="business" {{ old('category') == 'business' ? 'selected' : '' }}>Venture Capital</option>
                                    <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Miscellaneous</option>
                                </select>
                                <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="type" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Temporal Horizon <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <select id="type" name="type" required
                                        class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                                    <option value="">Select Horizon...</option>
                                    <option value="short_term" {{ old('type') == 'short_term' ? 'selected' : '' }}>Short Range (≤ 1 yr)</option>
                                    <option value="medium_term" {{ old('type') == 'medium_term' ? 'selected' : '' }}>Mid Range (1-5 yrs)</option>
                                    <option value="long_term" {{ old('type') == 'long_term' ? 'selected' : '' }}>Log Range (> 5 yrs)</option>
                                </select>
                                <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <i class="fas fa-chevron-down text-[10px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Target Values -->
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div class="space-y-2">
                            <label for="target_amount" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Capital Accumulation Target <span class="text-rose-500">*</span></label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5">
                                    <span class="text-xs font-black text-slate-400 group-focus-within:text-primary-500">Rp</span>
                                </div>
                                <input type="number" id="target_amount" name="target_amount" value="{{ old('target_amount') }}" placeholder="0.00" required
                                       class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-extrabold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label for="target_date" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Completion Deadline <span class="text-rose-500">*</span></label>
                            <input type="date" id="target_date" name="target_date" value="{{ old('target_date') }}" min="{{ date('Y-m-d') }}" required
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-2">
                        <label for="description" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Motivational Context (Optional)</label>
                        <textarea id="description" name="description" rows="3" placeholder="Why is this target important to your mission?"
                                  class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-medium text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-300 focus:bg-white focus:ring-4 focus:ring-primary-500/10">{{ old('description') }}</textarea>
                    </div>

                    <!-- Actions -->
                    <div class="pt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end border-t border-slate-50">
                        <a href="{{ route('goals.index') }}" class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                            Discard
                        </a>
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                            <i class="fas fa-bullseye mr-2 text-primary-400"></i>
                            Establish Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Meta Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/5 ring-1 ring-white/10 text-primary-400 mb-6">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-4">Smart Targetting</h3>
                <p class="text-[11px] font-medium text-slate-400 leading-relaxed">
                    Setting specific capital aspirations increases achievement rates by <span class="text-white font-bold">42%</span>. Define realistic horizons and track your velocity.
                </p>
            </div>

            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100 flex flex-col items-center text-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-900 shadow-sm border border-slate-100 italic font-black">
                    P
                </div>
                <div>
                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-1">Psychological ROI</h4>
                    <p class="text-[10px] font-medium text-slate-400 leading-relaxed">
                        Visualizing progress towards long-term aspirations reinforces positive financial habits and reduces impulsive consumption.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
