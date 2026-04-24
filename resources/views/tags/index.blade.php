@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">Classification Tags</h1>
            <p class="text-sm font-medium text-slate-500">Fine-grained organization for your transaction metadata</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                Back
            </a>
            <a href="{{ route('tags.create') }}" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                <i class="fas fa-plus mr-2"></i>
                Create Tag
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

    <!-- Tags Grid -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @forelse($tags as $tag)
        <div class="group relative flex flex-col p-6 rounded-2xl bg-white shadow-premium ring-1 ring-slate-100 transition-all hover:shadow-soft overflow-hidden">
            <!-- Sidebar Color Indicator -->
            <div class="absolute inset-y-0 left-0 w-1.5" style="background-color: {{ $tag->color }}"></div>

            <div class="flex items-start justify-between mb-4 pl-2">
                <div class="space-y-1">
                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px] font-black uppercase tracking-tighter text-white shadow-sm" style="background-color: {{ $tag->color }}">
                        {{ $tag->name }}
                    </span>
                    <h4 class="text-xl font-black text-slate-900 tracking-tighter mt-4">{{ $tag->getTransactionsCount() }}</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Linked Transactions</p>
                </div>
                
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="h-8 w-8 flex items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-ellipsis-v text-xs"></i>
                    </button>
                    
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-36 rounded-xl bg-white shadow-soft ring-1 ring-slate-200 z-50 p-1">
                        <a href="{{ route('tags.show', $tag) }}" class="flex items-center gap-2 px-3 py-2 text-[10px] font-bold text-slate-600 hover:bg-slate-50 hover:text-primary-600 rounded-lg transition-colors">
                            <i class="fas fa-eye w-4"></i> View Audit
                        </a>
                        <a href="{{ route('tags.edit', $tag) }}" class="flex items-center gap-2 px-3 py-2 text-[10px] font-bold text-slate-600 hover:bg-slate-50 hover:text-amber-600 rounded-lg transition-colors">
                            <i class="fas fa-edit w-4"></i> Edit Meta
                        </a>
                        <div class="h-px bg-slate-100 my-1"></div>
                        <form action="{{ route('tags.destroy', $tag) }}" method="POST" onsubmit="return confirm('Securely delete this tag?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-[10px] font-bold text-rose-500 hover:bg-rose-50 rounded-lg transition-colors text-left uppercase">
                                <i class="fas fa-trash w-4"></i> Purge Tag
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($tag->description)
            <div class="mt-4 pt-4 border-t border-slate-50 pl-2">
                <p class="text-[10px] font-medium text-slate-400 leading-relaxed italic line-clamp-2">"{{ $tag->description }}"</p>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full py-20 bg-white rounded-2xl shadow-premium border border-dashed border-slate-200 flex flex-col items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                <i class="fas fa-tags text-2xl"></i>
            </div>
            <p class="text-xs text-slate-400 font-medium italic">Your classification system is currently empty.</p>
            <a href="{{ route('tags.create') }}" class="text-xs font-bold text-primary-600 hover:underline">Establish First Tag</a>
        </div>
        @endforelse
    </div>
</div>
@endsection
