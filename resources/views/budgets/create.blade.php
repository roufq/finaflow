@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Define New Budget</h1>
            <p class="text-sm font-medium text-slate-500">Establish spending protocols to maintain long-term financial health.</p>
        </div>
        <a href="{{ route('budgets.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Budgets
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
        <form action="{{ route('budgets.store') }}" method="POST" class="space-y-10" x-data="{ 
            allocations: {{ json_encode(old('category_allocations', [])) }},
            add() { this.allocations.push({ category_id: '', amount: '' }) },
            remove(index) { this.allocations.splice(index, 1) }
        }">
            @csrf

            <!-- Primary Info Section -->
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Budget Strategy Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Monthly Household Buffer" required
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20 @error('name') ring-rose-500 @enderror">
                    @error('name') <p class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Budgeting Methodology *</label>
                        <div class="relative">
                            <select name="type" required
                                    class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                                <option value="">Select methodology...</option>
                                <option value="zero_based" {{ old('type') == 'zero_based' ? 'selected' : '' }}>Zero-Based Budgeting</option>
                                <option value="envelope" {{ old('type') == 'envelope' ? 'selected' : '' }}>Envelope System</option>
                                <option value="percentage_based" {{ old('type') == 'percentage_based' ? 'selected' : '' }}>Percentage-Based</option>
                                <option value="fixed_amount" {{ old('type', 'fixed_amount') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Financial Cycle *</label>
                        <div class="relative">
                            <select name="period" required
                                    class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                                <option value="">Select cycle...</option>
                                <option value="weekly" {{ old('period') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                <option value="monthly" {{ old('period', 'monthly') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('period') == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="yearly" {{ old('period') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Effective Start Date *</label>
                        <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                               class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Expiration Date (Optional)</label>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-semibold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Aggregate Budget Limit *</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-sm font-extrabold text-slate-400">Rp</span>
                        <input type="number" step="0.01" name="total_budget" value="{{ old('total_budget') }}" placeholder="0.00" required
                               class="w-full rounded-2xl border-none bg-slate-50 pl-12 pr-5 py-3.5 text-sm font-extrabold text-slate-900 ring-1 ring-slate-200 transition-all focus:ring-2 focus:ring-primary-500/20">
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400 ml-1">Strategic Description</label>
                <textarea name="description" rows="3" placeholder="Define the goals for this spending threshold..."
                          class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-medium text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-300 focus:ring-2 focus:ring-primary-500/20">{{ old('description') }}</textarea>
            </div>

            <!-- Dynamic Allocation Section -->
            <div class="space-y-6 pt-6 border-t border-slate-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-tight">Category Distribution</h3>
                        <p class="text-[10px] font-medium text-slate-400 mt-1 uppercase tracking-widest">Optional breakdown for precise tracking</p>
                    </div>
                    <button type="button" @click="add()" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-50 text-primary-600 transition-all hover:bg-primary-100">
                        <i class="fas fa-plus text-[10px]"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(allocation, index) in allocations" :key="index">
                        <div class="group flex flex-col gap-3 p-4 rounded-2xl bg-slate-50 ring-1 ring-slate-100 md:flex-row md:items-center">
                            <div class="relative flex-1">
                                <select :name="'category_allocations[' + index + '][category_id]'" required
                                        class="w-full appearance-none rounded-xl border-none bg-white px-4 py-2 text-xs font-bold text-slate-700 ring-1 ring-slate-200 focus:ring-2 focus:ring-primary-500/20">
                                    <option value="">Category...</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 text-[8px] pointer-events-none"></i>
                            </div>
                            
                            <div class="relative flex-1">
                                <input type="number" :name="'category_allocations[' + index + '][amount]'" placeholder="Amount (Rp)" required
                                       class="w-full rounded-xl border-none bg-white px-4 py-2 text-xs font-bold text-slate-900 ring-1 ring-slate-200 focus:ring-2 focus:ring-primary-500/20">
                            </div>

                            <button type="button" @click="remove(index)" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-slate-300 hover:bg-rose-50 hover:text-rose-600 transition-colors">
                                <i class="fas fa-times text-xs"></i>
                            </button>
                        </div>
                    </template>
                    
                    <div x-show="allocations.length === 0" class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">No detailed category distribution defined</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="pt-8 border-t border-slate-50 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('budgets.index') }}" 
                   class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                    Activate Budget Protocol
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
