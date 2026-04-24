@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-10">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Edit Category</h1>
            <p class="text-sm font-medium text-slate-500">Refine the classification details for {{ $category->name }}.</p>
        </div>
        <a href="{{ route('categories.index') }}" class="inline-flex items-center text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-3xl bg-white p-8 shadow-premium ring-1 ring-slate-100">
        <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                <!-- Name -->
                <div class="space-y-2">
                    <label for="name" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Category Label</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}" placeholder="e.g. Wellness, SaaS, Groceries" required
                           class="w-full rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                </div>

                <!-- Type -->
                <div class="space-y-2">
                    <label for="type" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Flow Direction</label>
                    <div class="relative">
                        <select id="type" name="type" required
                                class="w-full appearance-none rounded-2xl border-none bg-slate-50 px-5 py-3.5 text-sm font-bold text-slate-900 ring-1 ring-slate-200 transition-all focus:bg-white focus:ring-4 focus:ring-primary-500/10">
                            <option value="income" {{ old('type', $category->type) == 'income' ? 'selected' : '' }}>Income (Inflow)</option>
                            <option value="expense" {{ old('type', $category->type) == 'expense' ? 'selected' : '' }}>Expense (Outflow)</option>
                        </select>
                        <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label for="description" class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1">Functional Description (Optional)</label>
                <textarea id="description" name="description" rows="4" placeholder="What kind of transactions belong here?"
                          class="w-full rounded-2xl border-none bg-slate-50 px-5 py-4 text-sm font-medium text-slate-900 ring-1 ring-slate-200 transition-all placeholder:text-slate-300 focus:bg-white focus:ring-4 focus:ring-primary-500/10">{{ old('description', $category->description) }}</textarea>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                <a href="{{ route('categories.index') }}" class="inline-flex items-center justify-center rounded-2xl px-8 py-3.5 text-sm font-bold text-slate-500 hover:text-slate-900 transition-colors">
                    Discard Changes
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-10 py-3.5 text-sm font-extrabold text-white shadow-premium transition-all hover:bg-slate-800 active:scale-95">
                    <i class="fas fa-save mr-2 text-primary-400"></i>
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
