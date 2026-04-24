@extends('layouts.app')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900">{{ __('insights.anomalies.title') }}</h1>
            <p class="text-sm font-medium text-slate-500">Forensic detection of unusual patterns and potential financial risks</p>
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
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.anomalies.type') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.anomalies.description') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">{{ __('insights.anomalies.severity') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">{{ __('insights.anomalies.status') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('insights.anomalies.detected_at') }}</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($anomalies as $anomaly)
                    <tr class="group hover:bg-slate-50/50 transition-all">
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-600 uppercase tracking-tight">
                                {{ str_replace('_', ' ', $anomaly->anomaly_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-slate-900 leading-relaxed">{{ $anomaly->description }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $severityColors = [
                                    1 => 'bg-emerald-50 text-emerald-600',
                                    2 => 'bg-amber-50 text-amber-600',
                                    3 => 'bg-rose-50 text-rose-600 shadow-[0_0_10px_rgba(244,63,94,0.1)]',
                                ];
                                $color = $severityColors[$anomaly->severity] ?? 'bg-slate-50 text-slate-600';
                            @endphp
                            <span class="inline-flex items-center rounded-lg {{ $color }} px-2.5 py-0.5 text-[10px] font-black uppercase tracking-tight">
                                {{ $anomaly->severity_tags }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($anomaly->is_resolved)
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600">
                                    <i class="fas fa-shield-check text-[10px]"></i>
                                    {{ __('insights.anomalies.resolved') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-500 animate-pulse">
                                    <i class="fas fa-radar text-[10px]"></i>
                                    {{ __('insights.anomalies.unresolved') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-medium text-slate-500">{{ $anomaly->detected_at->diffForHumans() }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                @if(!$anomaly->is_resolved)
                                <form action="{{ route('insights.anomalies.resolve', $anomaly) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 hover:bg-emerald-50 hover:text-emerald-600 transition-all" title="{{ __('insights.anomalies.mark_resolved') }}">
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
                                    <i class="fas fa-shield-alt text-2xl"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">{{ __('insights.no_anomalies') }}</h4>
                                <p class="text-xs text-slate-400 max-w-[200px]">{{ __('insights.generate_anomalies_hint') }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($anomalies->hasPages())
        <div class="p-6 border-t border-slate-50 bg-slate-50/30">
            {{ $anomalies->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
