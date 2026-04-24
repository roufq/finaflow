@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Financial Budgets</h1>
            <p class="text-sm font-medium text-slate-500">Configure and monitor your spending limits across different categories.</p>
        </div>
        <a href="{{ route('budgets.create') }}" 
           class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
            <i class="fas fa-plus mr-2"></i>
            Create New Budget
        </a>
    </div>

    <!-- Stats Overview (Optional but adds value) -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($budgets as $budget)
        @php
            $isOver = $budget->spent_percentage > 100;
            $isWarning = $budget->spent_percentage > 85 && !$isOver;
            $barColor = $isOver ? 'bg-rose-500' : ($isWarning ? 'bg-amber-500' : 'bg-primary-500');
            $iconBg = $isOver ? 'bg-rose-50' : ($isWarning ? 'bg-amber-50' : 'bg-primary-50');
            $iconColor = $isOver ? 'text-rose-600' : ($isWarning ? 'text-amber-600' : 'text-primary-600');
        @endphp
        <div class="group relative flex flex-col overflow-hidden rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 transition-all hover:shadow-soft">
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $iconBg }} {{ $iconColor }} shadow-sm transition-transform group-hover:scale-105">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('budgets.edit', $budget) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-600">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('budgets.destroy', $budget) }}" method="POST" class="inline" onsubmit="return confirm('Delete this budget?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-600">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Card Content -->
            <div class="flex flex-col flex-1">
                <a href="{{ route('budgets.show', $budget) }}" class="block text-lg font-bold text-slate-900 group-hover:text-primary-600 transition-colors">
                    {{ $budget->name }}
                </a>
                
                <div class="mt-6 space-y-4">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Total Spent</p>
                            <h4 class="text-xl font-extrabold text-slate-900 leading-none">
                                {{ $currencySymbol }} {{ number_format($budget->spent_amount, 0, ',', '.') }}
                            </h4>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Limit</p>
                            <p class="text-sm font-bold text-slate-500">
                                {{ $currencySymbol }} {{ number_format($budget->total_budget, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-2">
                        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden ring-1 ring-slate-200/50">
                            <div class="h-full {{ $barColor }} rounded-full transition-all duration-1000 ease-out" style="width: {{ min($budget->spent_percentage, 100) }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-tighter">
                            <span class="{{ $isOver ? 'text-rose-600' : ($isWarning ? 'text-amber-600' : 'text-slate-400') }}">
                                {{ number_format($budget->spent_percentage, 1) }}% Used
                            </span>
                            @if($isOver)
                                <span class="text-rose-600">Over Budget!</span>
                            @else
                                <span class="text-slate-400">{{ $currencySymbol }} {{ number_format($budget->remaining_amount, 0, ',', '.') }} Left</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Footer -->
            <div class="mt-8">
                <a href="{{ route('budgets.show', $budget) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-slate-50 py-2.5 text-xs font-bold text-slate-600 ring-1 ring-slate-200 transition-all hover:bg-slate-100 hover:text-slate-900">
                    Detailed Analytics
                    <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 bg-white rounded-2xl shadow-premium border border-dashed border-slate-200 flex flex-col items-center justify-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-50 text-slate-200 mb-6">
                <i class="fas fa-calculator text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Active Budgets</h3>
            <p class="text-sm text-slate-500 mb-8 max-w-sm text-center">Establish spending limits to keep your finances healthy and within your goals.</p>
            <a href="{{ route('budgets.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-8 py-3 text-sm font-bold text-white shadow-premium transition-all hover:bg-primary-500">
                Setup First Budget
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
