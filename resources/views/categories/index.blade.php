@extends('layouts.app')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Financial Categories</h1>
            <p class="text-sm font-medium text-slate-500">Organize your income and expenses for more detailed financial analysis.</p>
        </div>
        <a href="{{ route('categories.create') }}" 
           class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
            <i class="fas fa-plus mr-2"></i>
            New Category
        </a>
    </div>

    <!-- Category Table -->
    <div class="rounded-2xl bg-white shadow-premium overflow-hidden ring-1 ring-slate-100">
        <div class="p-6 border-b border-slate-50 flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Management Ledger</h2>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $categories->count() }} Total Categories</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-slate-50 bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">#</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Category Name</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Stream Type</th>
                        <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Description</th>
                        <th class="px-6 py-4 text-right text-[10px] font-bold uppercase tracking-widest text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($categories as $category)
                    <tr class="group transition-colors hover:bg-slate-50/50">
                        <td class="px-6 py-4 text-xs font-bold text-slate-300">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-slate-900">{{ $category->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($category->type === 'income')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-extrabold text-emerald-600 ring-1 ring-emerald-100 uppercase">
                                    <i class="fas fa-arrow-up text-[8px]"></i>
                                    Income
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-3 py-1 text-[10px] font-extrabold text-rose-600 ring-1 ring-rose-100 uppercase">
                                    <i class="fas fa-arrow-down text-[8px]"></i>
                                    Expense
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-slate-400">{{ $category->description ?: 'No detail provided' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 transition-opacity group-hover:opacity-100">
                                <a href="{{ route('categories.show', $category) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-primary-50 hover:text-primary-600">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="{{ route('categories.edit', $category) }}" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-amber-50 hover:text-amber-600">
                                    <i class="fas fa-edit text-xs"></i>
                                </a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="inline" onsubmit="return confirm('Delete this category? This may affect linked transactions.')">
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
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400 italic">No categories defined yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
