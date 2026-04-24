@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ $budget->name }}</h1>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-black uppercase ring-1 ring-inset {{ $budget->status === 'active' ? 'bg-emerald-50 text-emerald-600 ring-emerald-100' : 'bg-slate-100 text-slate-500 ring-slate-200' }}">
                    {{ $budget->status_tags }}
                </span>
            </div>
            <p class="text-sm font-medium text-slate-500">Comprehensive performance analysis for the {{ $budget->period }} spending protocol.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('budgets.index') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('budgets.edit', $budget) }}" class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                <i class="fas fa-edit mr-2 text-primary-400"></i>
                Edit Strategy
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-12">
        <!-- Left: Core Metrics -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Progress Visualization -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100 text-center">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-10">Consumption Velocity</h3>
                
                <div class="relative mx-auto flex h-48 w-48 items-center justify-center">
                    <svg class="h-full w-full -rotate-90 transform" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" class="text-slate-50"></circle>
                        @php
                            $isOver = $budget->spent_percentage > 100;
                            $strokeColor = $isOver ? 'text-rose-500' : ($budget->spent_percentage > 85 ? 'text-amber-500' : 'text-primary-500');
                        @endphp
                        <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" 
                                class="{{ $strokeColor }} transition-all duration-1000 ease-out"
                                stroke-dasharray="282.7"
                                stroke-dashoffset="{{ 282.7 - (min($budget->spent_percentage, 100) / 100 * 282.7) }}"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-black tracking-tighter text-slate-900">{{ number_format($budget->spent_percentage, 1) }}%</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Utilized</span>
                    </div>
                </div>

                <div class="mt-10 space-y-1">
                    <h4 class="text-2xl font-black text-slate-900">Rp {{ number_format($budget->spent_amount, 0, ',', '.') }}</h4>
                    <p class="text-[11px] font-medium text-slate-400">of Rp {{ number_format($budget->total_budget, 0, ',', '.') }} limit</p>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-50">
                    @if($isOver)
                        <div class="rounded-2xl bg-rose-50 p-4 text-center ring-1 ring-rose-100">
                            <p class="text-[10px] font-black text-rose-900 uppercase">Deficit Threshold Exceeded</p>
                            <p class="mt-1 text-sm font-bold text-rose-600">Rp {{ number_format($budget->spent_amount - $budget->total_budget, 0, ',', '.') }}</p>
                        </div>
                    @else
                        <div class="rounded-2xl bg-emerald-50 p-4 text-center ring-1 ring-emerald-100">
                            <p class="text-[10px] font-black text-emerald-900 uppercase">Available Buffer</p>
                            <p class="mt-1 text-sm font-bold text-emerald-600">Rp {{ number_format($budget->remaining_amount, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Strategy Meta -->
            <div class="rounded-3xl bg-slate-900 p-8 text-white shadow-premium ring-1 ring-white/10">
                <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-8">Strategic parameters</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase">Methodology</span>
                        <span class="text-xs font-black text-white italic capitalize">{{ str_replace('_', ' ', $budget->type) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase">Financial Cycle</span>
                        <span class="text-xs font-black text-white capitalize">{{ $budget->period }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400 uppercase">Commencement</span>
                        <span class="text-xs font-black text-white">{{ $budget->start_date->format('d M Y') }}</span>
                    </div>
                </div>
                @if($budget->description)
                <div class="mt-8 pt-8 border-t border-white/5">
                    <p class="text-[10px] font-medium text-slate-500 leading-relaxed italic">{{ $budget->description }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Right: Distributions & Tracking -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Distributions -->
            @if($budget->category_allocations && count($budget->category_allocations) > 0)
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 text-center md:text-left">Capital Distributions</h3>
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    @foreach($budget->category_allocations as $categoryId => $allocatedAmount)
                        @php
                            $category = \App\Models\Category::find($categoryId);
                        @endphp
                        @if($category)
                        <div class="group rounded-2xl bg-slate-50 p-5 ring-1 ring-slate-100 transition-all hover:bg-white hover:shadow-soft">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-black text-slate-900 lowercase tracking-tighter">{{ $category->name }}</span>
                                <span class="text-[10px] font-bold text-primary-500">Target: Rp {{ number_format($allocatedAmount, 0, ',', '.') }}</span>
                            </div>
                            <div class="h-1.5 w-full rounded-full bg-slate-100 overflow-hidden">
                                <div class="h-full bg-primary-500 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Operation Console -->
            <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-8 text-center md:text-left">Liquidity Maintenance</h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <button @click="$dispatch('open-modal', 'update-expense')" class="flex items-center gap-4 rounded-2xl bg-emerald-50 p-6 ring-1 ring-emerald-100 transition-all hover:bg-emerald-100 text-left">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm">
                            <i class="fas fa-plus-circle text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-emerald-900 uppercase">Log Consumption</p>
                            <p class="text-[10px] font-medium text-emerald-600 mt-1">Manual adjustment for external ledger sync.</p>
                        </div>
                    </button>

                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" onsubmit="return confirm('Securely archive this budget protocol?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex w-full items-center gap-4 rounded-2xl bg-rose-50 p-6 ring-1 ring-rose-100 transition-all hover:bg-rose-100 text-left">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-white text-rose-500 shadow-sm">
                                <i class="fas fa-archive text-xl"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-rose-900 uppercase">Archive Strategy</p>
                                <p class="text-[10px] font-medium text-rose-600 mt-1">Permanently remove this spending protocol.</p>
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Simple Modern Modal (Tailwind based) -->
<div x-data="{ open: false }" @open-modal.window="if($event.detail === 'update-expense') open = true" class="relative z-50" x-show="open" x-cloak>
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="open = false"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white p-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Manual Adjustments</h3>
                        <button @click="open = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
                    </div>
                    <form id="updateSpentForm" class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total Consumption Volume (Rp)</label>
                            <input type="number" name="spent_amount" value="{{ $budget->spent_amount }}" min="0" required
                                   class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-extrabold text-slate-900 ring-1 ring-slate-200 focus:ring-4 focus:ring-primary-500/10">
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                            <button type="button" @click="open = false" class="px-6 py-3 text-sm font-bold text-slate-500">Cancel</button>
                            <button type="submit" class="rounded-xl bg-slate-900 px-8 py-3 text-sm font-extrabold text-white shadow-lg transition-all hover:bg-slate-800">Assign Value</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle update spent form
    const form = document.getElementById('updateSpentForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch(`{{ route('budgets.updateSpent', $budget) }}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
</script>
@endpush
@endsection
