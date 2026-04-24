@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Financial Goals</h1>
            <p class="text-sm font-medium text-slate-500">Track and manage your future financial aspirations</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('goals.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Set New Goal
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="relative overflow-hidden rounded-2xl bg-emerald-50 border border-emerald-100 p-4 shadow-sm flex items-center gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm ring-1 ring-emerald-100">
            <i class="fas fa-check"></i>
        </div>
        <p class="text-sm font-bold text-emerald-900">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Goals Grid -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
        @forelse($goals as $goal)
        <div class="group relative flex flex-col overflow-hidden rounded-2xl bg-white p-6 shadow-premium ring-1 ring-slate-100 transition-all hover:shadow-soft">
            <!-- Card Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 shadow-sm transition-transform group-hover:scale-105">
                    <i class="fas fa-bullseye text-xl"></i>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('goals.edit', $goal) }}" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-600">
                        <i class="fas fa-edit text-xs"></i>
                    </a>
                    <form action="{{ route('goals.destroy', $goal) }}" method="POST" class="inline" onsubmit="return confirm('Delete goals?')">
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
                <a href="{{ route('goals.show', $goal) }}" class="block text-lg font-extrabold text-slate-900 truncate group-hover:text-primary-600 transition-colors">
                    {{ $goal->name }}
                </a>
                
                <div class="mt-6 space-y-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1">Current Savings</p>
                        <h4 class="text-xl font-black text-slate-900 tracking-tight">
                            Rp {{ number_format($goal->current_amount, 0, ',', '.') }}
                        </h4>
                        <p class="text-[10px] font-bold text-slate-400 mt-0.5">Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-2">
                        <div class="h-2 w-full rounded-full bg-slate-100 overflow-hidden ring-1 ring-slate-200/50">
                            <div class="h-full bg-primary-500 rounded-full transition-all duration-1000 ease-out" 
                                 style="width: {{ min($goal->progress_percentage, 100) }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-widest">
                            <span class="{{ $goal->progress_percentage >= 100 ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $goal->progress_percentage }}% Complete
                            </span>
                            @if($goal->progress_percentage >= 100)
                                <span class="text-emerald-600">Goal Achieved!</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Footer -->
            <div class="mt-8">
                <a href="{{ route('goals.show', $goal) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-slate-900 py-3 text-xs font-bold text-white shadow-premium transition-all hover:bg-slate-800">
                    Manage Contributions
                    <i class="fas fa-chevron-right ml-2 text-[10px]"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-full py-20 bg-white rounded-3xl shadow-premium border border-dashed border-slate-200 flex flex-col items-center justify-center">
            <div class="flex h-20 w-20 items-center justify-center rounded-3xl bg-slate-50 text-slate-200 mb-6">
                <i class="fas fa-flag text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No Active Goals</h3>
            <p class="text-sm text-slate-500 mb-8 max-w-sm text-center">Set up your first financial target to start visualizing your future wealth progress.</p>
            <a href="{{ route('goals.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-primary-600 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-primary-500">
                Define First Goal
            </a>
        </div>
        @endforelse
    </div>
</div>
@endsection
