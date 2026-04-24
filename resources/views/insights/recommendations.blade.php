@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('insights.recommendations.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Strategic directives to optimize your financial performance</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-premium ring-1 ring-slate-200 transition-all hover:bg-slate-50">
                <i class="fas fa-arrow-left mr-2 text-slate-400"></i>
                {{ __('common.back') }}
            </a>
            <form method="POST" action="{{ route('insights.generate') }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-2 text-sm font-semibold text-white shadow-premium transition-all hover:bg-primary-500 active:scale-95">
                    <i class="fas fa-sync-alt mr-2"></i>
                    {{ __('insights.generate_insights') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Data Table Ecosystem -->
    <div class="rounded-3xl bg-white shadow-premium ring-1 ring-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.recommendations.type') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.recommendations.content') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">{{ __('insights.recommendations.priority') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">{{ __('insights.recommendations.status') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('common.created_at') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recommendations as $recommendation)
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-primary-50 px-2.5 py-0.5 text-[10px] font-bold text-primary-600 uppercase tracking-tight">
                                {{ $recommendation->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900 leading-relaxed">{{ $recommendation->content }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $priorityColors = [
                                    1 => 'bg-emerald-50 text-emerald-600',
                                    2 => 'bg-amber-50 text-amber-600',
                                    3 => 'bg-rose-50 text-rose-600',
                                ];
                                $color = $priorityColors[$recommendation->priority] ?? 'bg-slate-50 text-slate-600';
                            @endphp
                            <span class="inline-flex items-center rounded-lg {{ $color }} px-2.5 py-0.5 text-[10px] font-black uppercase tracking-tight">
                                {{ __('insights.recommendations.priority_' . $recommendation->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($recommendation->is_read)
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                                    <i class="fas fa-check-circle text-[10px]"></i>
                                    {{ __('insights.recommendations.read') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-500">
                                    <i class="fas fa-clock text-[10px]"></i>
                                    {{ __('insights.recommendations.unread') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-slate-500">{{ $recommendation->created_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                @if(!$recommendation->is_read)
                                <form action="{{ route('insights.recommendations.read', $recommendation) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all" title="{{ __('insights.recommendations.mark_as_read') }}">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-200">
                                    <i class="fas fa-lightbulb text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">{{ __('insights.no_recommendations') }}</h4>
                                <p class="text-xs text-slate-400 max-w-[200px]">{{ __('insights.generate_recommendations_hint') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($recommendations->hasPages())
        <div class="p-6 border-t border-slate-50 bg-slate-50/30">
            {{ $recommendations->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
